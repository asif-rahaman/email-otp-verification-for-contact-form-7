<?php
/**
 * Plugin Name: Email OTP Verification for Contact Form 7
 * Description: Adds OTP email verification to Contact Form 7 using the site's default SMTP. No third-party APIs required.
 * Version: 0.0.1
 * Author: Asif Rahaman
 * Text Domain: email-otp-verification-for-contact-form-7
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

class EOV_CF7_Email_OTP_Verification {
    private $limit_count = 3;
    private $limit_time = 300; // 5 minutes in seconds
    private $domain = 'email-otp-verification-for-contact-form-7';

    public function __construct() {
        // Load translations
        add_action('init', [$this, 'eov_cf7_load_textdomain']);
        
        // Assets and AJAX
        add_action('wp_enqueue_scripts', [$this, 'eov_cf7_enqueue_assets']);
        add_action('wp_ajax_send_cf7_otp', [$this, 'eov_cf7_handle_send_otp']);
        add_action('wp_ajax_nopriv_send_cf7_otp', [$this, 'eov_cf7_handle_send_otp']);
        
        // CF7 Validation filters
        add_filter('wpcf7_validate_text*', [$this, 'eov_cf7_validate_otp'], 20, 2);
        add_filter('wpcf7_validate_text', [$this, 'eov_cf7_validate_otp'], 20, 2);
    }

    /**
     * Load plugin textdomain for translations.
     */
    public function eov_cf7_load_textdomain() {
        load_plugin_textdomain($this->domain, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Enqueue JS and localized strings.
     */
    public function eov_cf7_enqueue_assets() {
        wp_enqueue_script('eov-cf7-otp-js', plugin_dir_url(__FILE__) . 'assets/otp-handler.js', ['jquery'], '1.0.0', true);
        
        wp_localize_script('eov-cf7-otp-js', 'eov_cf7_obj', [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('eov_cf7_otp_nonce'),
            'msg_sending' => __('Sending...', $this->domain),
            'msg_wait'    => __('Wait', $this->domain),
            'msg_resend'  => __('Resend OTP', $this->domain),
            'msg_invalid_email' => __('Please enter a valid email address.', $this->domain),
        ]);

        $custom_css = "
            #cf7-otp-response {
                display: block;
                padding: 5px 0;
                animation: eovFadeIn 0.5s;
            }
            @keyframes eovFadeIn { from { opacity: 0; } to { opacity: 1; } }";
            
        wp_add_inline_style('eov-cf7-otp-js', $custom_css);
    }

    private function eov_cf7_get_user_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Handle AJAX request to send OTP.
     */
    public function eov_cf7_handle_send_otp() {
        check_ajax_referer('eov_cf7_otp_nonce', 'security');

        $email = sanitize_email($_POST['email']);
        $ip    = $this->eov_cf7_get_user_ip();
        $rate_key = 'eov_limit_' . md5($ip);

        // Rate Limiting
        $attempts = get_transient($rate_key) ?: 0;
        if ($attempts >= $this->limit_count) {
            wp_send_json_error(__('Too many attempts. Please try again in 5 minutes.', $this->domain));
        }

        if (!is_email($email)) {
            wp_send_json_error(__('Invalid email address.', $this->domain));
        }

        $otp = rand(100000, 999999);
        set_transient('eov_cf7_otp_' . md5($email), $otp, 5 * MINUTE_IN_SECONDS);

        // Prepare Email
        $subject = sprintf(__('[%s] Your OTP Code', $this->domain), get_bloginfo('name'));
        $message = sprintf(__('Your OTP code is: %s', $this->domain), $otp) . "\r\n\r\n" . __('This code will expire in 5 minutes.', $this->domain);
        
        if (wp_mail($email, $subject, $message)) {
            set_transient($rate_key, $attempts + 1, $this->limit_time);
            wp_send_json_success(__('Verification code sent.', $this->domain));
        } else {
            wp_send_json_error(__('Email failed to send. Check SMTP config.', $this->domain));
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
            
            // Detect Email Field Dynamically
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
                $result->invalidate($tag, __('Invalid or expired OTP.', $this->domain));
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