=== Serial Validator ===
Contributors: yourname
Tags: serial, verification, authenticity, product, validation
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple, clean serial code verification system for product authenticity with lead collection, Elementor widget, and admin dashboard.

== Description ==

Serial Validator is a professional WordPress plugin that helps you verify product serial codes, collect customer leads, and manage product authenticity verification with ease.

= Key Features =

**Customer Verification**
* Easy-to-use verification form via shortcode or Elementor widget
* Multiple result states: Valid, Invalid, Already Used, Blocked
* QR code support with auto-fill from URL parameters
* Optional customer information collection (name, email, phone)
* Customizable success and error messages
* Mobile-responsive design

**Security**
* Rate limiting to prevent abuse (configurable attempts per hour)
* Google reCAPTCHA v2 integration
* Input validation and sanitization
* IP address tracking for verification attempts

**Lead Collection**
* Collect customer data during verification
* Configurable: create leads on valid only or all attempts
* Export leads to CSV
* Filter by status and date
* Search by email or code

**Admin Dashboard**
* Visual statistics (7-day and 30-day views)
* Verification trend chart
* Quick access to all features
* Clean, intuitive interface

**Code Management**
* Add codes individually or bulk import via CSV
* Set product name, batch, expiry date, warranty period
* Activate, block, or delete codes
* Search and filter functionality
* One-time use option per code

**CSV Import/Export**
* Bulk import codes with all attributes
* Export codes and leads to CSV
* Duplicate detection during import
* Comprehensive error reporting

**GDPR Compliant**
* WordPress privacy policy integration
* Personal data export tool
* Personal data erasure tool
* No tracking cookies

**Developer Friendly**
* Translation ready (i18n)
* Action and filter hooks
* Clean, documented code
* Follows WordPress coding standards

= Usage =

**Shortcode:**
`[serial_validator]`

**QR Code URL Format:**
`https://yourdomain.com/verify/?code=XXXX`

**Elementor:**
Search for "Serial Validator" widget in Elementor editor.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/serial-validator/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Serial Validator > Settings to configure the plugin
4. Import your serial codes via Serial Validator > Import Codes
5. Add the shortcode `[serial_validator]` to any page or use the Elementor widget

== Frequently Asked Questions ==

= How do I add the verification form to my site? =

You can use the shortcode `[serial_validator]` on any page or post. If you're using Elementor, search for "Serial Validator" in the widget panel.

= How do I import multiple codes at once? =

Go to Serial Validator > Import Codes and upload a CSV file. Required columns: code, product_name. Optional: batch, expiry_date, warranty_months, status.

= What happens when a code is verified? =

The system checks if the code exists, is active, not expired, and not blocked. If valid, it shows product information and optional warranty details. For one-time use codes, subsequent verifications show "Already Used" status.

= Can I customize the messages? =

Yes! Go to Serial Validator > Settings > Messages tab to customize all user-facing messages.

= How do I enable reCAPTCHA? =

1. Get reCAPTCHA v2 keys from Google (https://www.google.com/recaptcha/admin)
2. Go to Serial Validator > Settings > Security tab
3. Enable reCAPTCHA and enter your Site Key and Secret Key

= Is this plugin GDPR compliant? =

Yes. The plugin integrates with WordPress privacy tools for data export and erasure. It only collects data you configure and provides privacy policy content suggestions.

= Can I use this plugin without Elementor? =

Absolutely! The shortcode works independently. Elementor integration is optional.

= How are leads created? =

You can configure in Settings > Leads whether to create leads only on valid verifications or on every attempt.

= What data is collected during verification? =

By default, only the serial code is required. You can optionally enable Name, Email, and Phone fields in Settings > Form Settings.

= Can codes be verified multiple times? =

This depends on your settings. You can enable "one-time use" per code, which marks codes as "Already Used" after first valid verification. You can also choose whether to allow re-verification.

== Screenshots ==

1. Admin Dashboard with statistics and trend chart
2. Codes management page with bulk actions
3. CSV import interface with instructions
4. Leads management with export functionality
5. Settings page with tabbed interface
6. Frontend verification form
7. Elementor widget with style controls
8. Verification result (valid code)

== Changelog ==

= 1.0.0 =
* Initial release
* Frontend verification form (shortcode and Elementor widget)
* Admin dashboard with statistics and chart
* Code management with CRUD operations
* CSV import/export functionality
* Lead collection and management
* Comprehensive settings page
* Rate limiting and reCAPTCHA support
* GDPR compliance features
* Translation ready
* QR code support via URL parameters

== Upgrade Notice ==

= 1.0.0 =
Initial release of Serial Validator plugin.

== Additional Information ==

= Support =

For support, please visit the plugin's support forum or contact the developer.

= Contributing =

Contributions are welcome! Please follow WordPress coding standards.

= Privacy =

This plugin stores verification data locally in your WordPress database including:
- Serial codes entered
- Optional: customer name, email, phone (only if form fields are enabled)
- Verification timestamp and result
- IP address for rate limiting

**External Services (Optional)**

Google reCAPTCHA is completely optional. If you enable reCAPTCHA in Settings > Security:
- User responses are sent to Google for verification
- Google's privacy policy applies: https://policies.google.com/privacy
- Users see Google's reCAPTCHA widget on the verification form

The plugin does NOT contact any external servers unless you explicitly enable reCAPTCHA. All code validation happens locally within your WordPress installation.

= Credits =

Developed with attention to WordPress best practices and user experience.
