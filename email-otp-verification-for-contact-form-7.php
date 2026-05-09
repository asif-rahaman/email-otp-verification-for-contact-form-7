<?php
/**
 * Plugin Name: Email OTP Verification for Contact Form 7
 * Plugin URI:  https://github.com/asif-rahaman/email-otp-verification-for-contact-form-7
 * Description: Adds OTP email verification to Contact Form 7 using the site's default SMTP. No third-party APIs required.
 * Version:     1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:      Asif Rahaman
 * Author URI:        https://portfolio.asif.rocks
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: email-otp-verification-for-contact-form-7
 * Domain Path: /languages
 * Requires at least: 5.2
 * Tested up to: 6.9
 * Requires Plugins:  contact-form-7
 */

if (!defined('ABSPATH')) exit;

class EOV_CF7_Email_OTP_Verification {
    
    private $limit_count = 3;
    private $limit_time  = 300; 

    public function __construct() {       
        add_action('wp_enqueue_scripts', [$this, 'eov_cf7_enqueue_assets']);
        add_action('wp_ajax_send_cf7_otp', [$this, 'eov_cf7_handle_send_otp']);
        add_action('wp_ajax_nopriv_send_cf7_otp', [$this, 'eov_cf7_handle_send_otp']);
        
        add_filter('wpcf7_validate_text*', [$this, 'eov_cf7_validate_otp'], 20, 2);
        add_filter('wpcf7_validate_text', [$this, 'eov_cf7_validate_otp'], 20, 2);
    }
    /**
     * Enqueue JS and localized strings.
     */
    public function eov_cf7_enqueue_assets() {
        wp_enqueue_script('eov-cf7-otp-js', plugin_dir_url(__FILE__) . 'assets/otp-handler.js', ['jquery'], '1.0.0', true);
        
        wp_localize_script('eov-cf7-otp-js', 'eov_cf7_obj', [
            'ajax_url'          => admin_url('admin-ajax.php'),
            'nonce'             => wp_create_nonce('eov_cf7_otp_nonce'),
            'msg_sending'       => __('Sending...', 'email-otp-verification-for-contact-form-7'),
            'msg_wait'          => __('Wait', 'email-otp-verification-for-contact-form-7'),
            'msg_resend'        => __('Resend OTP', 'email-otp-verification-for-contact-form-7'),
            'msg_invalid_email' => __('Please enter a valid email address.', 'email-otp-verification-for-contact-form-7'),
        ]);

        $custom_css = ".cf7-otp-response { display: block; padding: 5px 0; animation: eovFadeIn 0.5s; }
                       @keyframes eovFadeIn { from { opacity: 0; } to { opacity: 1; } }";
        wp_add_inline_style('eov-cf7-otp-js', $custom_css);
    }

    private function eov_cf7_get_user_ip() {
        // SECURITY: wp_unslash() and sanitize_text_field for server vars
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
        return $ip;
    }

    /**
     * Handle AJAX request to send OTP.
     */
    public function eov_cf7_handle_send_otp() {
        check_ajax_referer('eov_cf7_otp_nonce', 'security');

        // SECURITY: Check if POST index exists and unslash before sanitizing
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $ip    = $this->eov_cf7_get_user_ip();
        $rate_key = 'eov_limit_' . md5($ip);

        // Rate Limiting
        $attempts = get_transient($rate_key) ?: 0;
        if ($attempts >= $this->limit_count) {
            wp_send_json_error(__('Too many attempts. Please try again in 5 minutes.', 'email-otp-verification-for-contact-form-7'));
        }

        if (!is_email($email)) {
            wp_send_json_error(__('Invalid email address.', 'email-otp-verification-for-contact-form-7'));
        }

        // SECURITY: Use wp_rand() instead of rand()
        $otp = wp_rand(100000, 999999);
        set_transient('eov_cf7_otp_' . md5($email), $otp, 5 * MINUTE_IN_SECONDS);

        // Prepare Email
        /* translators: %s: Site Name */
        $subject = sprintf(__('[%s] Your Verification Code', 'email-otp-verification-for-contact-form-7'), get_bloginfo('name'));
        /* translators: %s: OTP Code */
        $message = sprintf(__('Your verification code is: %s', 'email-otp-verification-for-contact-form-7'), $otp) . "\r\n\r\n" . __('This code will expire in 5 minutes.', 'email-otp-verification-for-contact-form-7');
        
        if (wp_mail($email, $subject, $message)) {
            set_transient($rate_key, $attempts + 1, $this->limit_time);
            wp_send_json_success(__('Verification code sent successfully.', 'email-otp-verification-for-contact-form-7'));
        } else {
            wp_send_json_error(__('Email delivery failed. Check your SMTP settings.', 'email-otp-verification-for-contact-form-7'));
        }
    }

    /**
     * Validate the OTP during CF7 submission.
     */
    public function eov_cf7_validate_otp($result, $tag) {
        if ($tag->name === 'email-otp') {
            $submission = WPCF7_Submission::get_instance();
            if (!$submission) return $result;

            $posted_data = $submission->get_posted_data();
            $email = '';
            foreach ($posted_data as $key => $value) {
                if (is_email($value)) {
                    $email = $value;
                    break;
                }
            }

            $user_otp   = isset($posted_data['email-otp']) ? trim($posted_data['email-otp']) : '';
            $stored_otp = get_transient('eov_cf7_otp_' . md5($email));

            if (empty($email) || !$stored_otp || $user_otp != $stored_otp) {
                $result->invalidate($tag, __('The OTP code is incorrect or has expired.', 'email-otp-verification-for-contact-form-7'));
            } else {
                delete_transient('eov_cf7_otp_' . md5($email));
            }
        }
        return $result;
    }
}

/**
 * Initialize the plugin class
 */
function eov_cf7_start_verification() {
    new EOV_CF7_Email_OTP_Verification();
}
add_action('plugins_loaded', 'eov_cf7_start_verification');