=== Email OTP Verification for Contact Form 7 ===
Contributors: asifrahaman
Tags: contact form 7, otp, email verification, spam protection, smtp
Requires at least: 5.2
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Verify user emails with a secure OTP code to block bots and fake leads. Uses your own SMTP—no API fees, no subscriptions.

== Description ==

**Email OTP Verification for Contact Form 7** provides a robust security layer for your website by verifying user email addresses before form submission. This effectively blocks bots, spammers, and malicious actors from flooding your inbox with fake data or invalid leads.

### Why this plugin is different:
Most existing OTP plugins require you to pay for their proprietary API or use their specific SMTP service to send codes. This often leads to unexpected monthly costs and vendor lock-in. 

This plugin is built with a **"Your Server, Your Rules"** philosophy. It sends OTP codes using the native `wp_mail()` function, meaning it utilizes your website's default mail configuration or your preferred SMTP service. **There are no extra costs, no hidden fees, and no third-party accounts required.**

== Features ==
*   **Block Bots & Spam:** Ensure every submission comes from a person with a valid, accessible email address.
*   **Cost-Effective:** Zero cost to send OTPs—it uses your existing server or SMTP setup.
*   **Privacy First:** No data is sent to external verification APIs; everything stays on your server.
*   **Smart Detection:** Automatically finds the email field in your Contact Form 7 forms.
*   **Rate Limiting:** Built-in protection to prevent OTP request abuse (3 attempts per 5 minutes per IP).
*   **Lightweight:** Minimal footprint to ensure your site remains fast and passes Core Web Vitals.

== Installation ==

1. Upload the `email-otp-verification-for-contact-form-7` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Edit your Contact Form 7 form.
4. Add the OTP field: `[text* email-otp placeholder "Enter OTP"]`
5. Add the "Send" button: `<button type="button" id="send-otp-btn" class="wpcf7-submit">Send OTP</button>`
6. Save your form.

== Frequently Asked Questions ==

= Is this really free to use? =
Yes. As long as your server can send emails (or you use an SMTP plugin), there is no cost to send OTP codes through this plugin.

= How does it block bots? =
Bots can easily fill out forms, but they usually cannot access a private email inbox to retrieve a unique, time-sensitive code. By making the OTP field required, only real users can complete the submission.

== Changelog ==

= 1.0.0 =
* Initial release.
* Implemented self hosted sever or SMTP-based OTP delivery via wp_mail() function.
* Added anti-spam and bot blocking verification.