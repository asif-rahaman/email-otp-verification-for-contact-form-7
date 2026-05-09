# Email OTP Verification for Contact Form 7
Email OTP Verification for Contact Form 7 provides a robust security layer for your website by verifying user email addresses before form submission. This effectively blocks bots, spammers, and malicious actors from flooding your inbox with fake data or invalid leads.

## 🚀 Why This Plugin Is Different
Most existing OTP plugins require you to pay for their proprietary API or use their specific SMTP service to send codes. This often leads to unexpected monthly costs and vendor lock-in.

This plugin is built with a "Your Server, Your Rules" philosophy. It sends OTP codes using the native wp_mail() function, meaning it utilizes your website's default mail configuration or your preferred SMTP service. There are no extra costs, no hidden fees, and no third-party accounts required.

## ✨ Features
Block Bots & Spam: Ensure every submission comes from a person with a valid, accessible email address.

Cost-Effective: Zero cost to send OTPs—it uses your existing server or SMTP setup.

Privacy First: No data is sent to external verification APIs; everything stays on your server.

Smart Detection: Automatically finds the email field in your Contact Form 7 forms.

Multi-Form Support: Can be used in multiple forms on the same page without ID conflicts.

Rate Limiting: Built-in protection to prevent OTP request abuse (3 attempts per 5 minutes per IP).

Lightweight: Minimal footprint to ensure your site remains fast and passes Core Web Vitals.

## 🛠️ Installation
Upload the email-otp-verification-for-contact-form-7 folder to the /wp-content/plugins/ directory.

Activate the plugin through the 'Plugins' menu in WordPress.

Edit your Contact Form 7 form and add these two elements:

The Button: <button type="button" class="send-otp-btn wpcf7-submit">Send OTP</button>

The OTP Field: [text* email-otp placeholder "Enter OTP"]

Save your form.

## 👨‍💻 Developed By
**Asif Rahaman** Full-stack WordPress & WooCommerce Developer (12+ Years Experience). 
Specializing in high-performance WordPress optimization and enterprise-scale integrations.