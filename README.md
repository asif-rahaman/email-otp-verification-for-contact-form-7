# Email OTP Verification for Contact Form 7

Email OTP Verification for Contact Form 7 provides a robust security layer for your website by verifying user email addresses before form submission. This effectively blocks bots, spammers, and malicious actors from flooding your inbox with fake data or invalid leads.

## 🚀 Why This Plugin Is Different

Most existing OTP plugins require you to pay for their proprietary API or use their specific SMTP service to send codes. This often leads to unexpected monthly costs and vendor lock-in.

This plugin is built with a **"Your Server, Your Rules"** philosophy. It sends OTP codes using the native `wp_mail()` function, meaning it utilizes your website's default mail configuration or your preferred SMTP service. There are no extra costs, no hidden fees, and no third-party accounts required.

## ✨ Features

* **Block Bots & Spam:** Ensure every submission comes from a person with a valid, accessible email address.
* **Cost-Effective:** Zero cost to send OTPs—it uses your existing server or SMTP setup.
* **Privacy First:** No data is sent to external verification APIs; everything stays on your server.
* **Security-First Rate Limiting:** Includes built-in protection to prevent OTP request abuse by limiting users to 3 attempts per 5 minutes per IP address.
* **Smart Detection:** Automatically finds the email field in your Contact Form 7 forms.
* **Multi-Form Support:** Can be used in multiple forms on the same page without ID conflicts.
* **Lightweight Performance:** Optimized with a minimal footprint to ensure your site remains fast and passes Core Web Vitals.

## 🛠️ Installation

1. Upload the `email-otp-verification-for-contact-form-7` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Edit your Contact Form 7 form and add these two elements:
    * **The Button:** `<button type="button" class="send-otp-btn wpcf7-submit">Send OTP</button>`
    * **The OTP Field:** `[text* email-otp placeholder "Enter OTP"]`
4. Save your form.

## ❓ Frequently Asked Questions

### Is this truly free to use?
Yes. As long as your server can send emails, there is no cost to send OTP codes through this plugin.

### How does the rate limiting work?
To protect your server from being used for mail bombing or automated attacks, the plugin tracks the user's IP address and limits them to 3 OTP requests within a 5-minute window.

### Does it work with "WP Mail SMTP"?
Absolutely. It routes emails through whatever SMTP provider you have configured in your dashboard.

## 👨‍💻 Developed By

**Asif Rahaman** — Full-stack WordPress & WooCommerce Developer with over 12 years of professional experience. Specializing in high-performance WordPress optimization for large-scale enterprise websites and complex WooCommerce environments.