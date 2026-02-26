# Serial Validator WordPress Plugin

A clean, professional WordPress plugin for verifying product serial codes with lead collection, Elementor integration, and comprehensive admin management.

## Features

### Customer-Facing Features
- ✅ **Verification Form** - Shortcode `[serial_validator]` or Elementor widget
- 🔒 **Security** - Rate limiting and optional Google reCAPTCHA v2
- 📱 **QR Code Support** - Auto-fill codes from URL parameters (`?code=XXXX`)
- ✅ **Multiple Result States** - Valid, Invalid, Already Used, Blocked

### Admin Features
- 📊 **Dashboard** - Visual statistics and verification trends
- 📦 **Code Management** - Add, edit, activate, block, or delete codes
- 🎲 **Bulk Code Generator** - Generate thousands of unique codes with custom formats
- � **Batch Management** - Filter by batch, bulk block or delete entire batches
- �📥 **CSV Import/Export** - Bulk import codes or export data
- 👥 **Lead Management** - Collect customer information, filter and export
- ⚙️ **Settings** - Customize form fields, messages, and security options

### Developer Features
- 🎨 **Elementor Integration** - Custom widget with style controls
- 🔐 **GDPR Compliant** - WordPress privacy hooks for data export/erasure
- 🌍 **Translation Ready** - Full i18n support with text domain
- 🔌 **Extensible** - Action and filter hooks throughout

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Elementor (optional, for widget functionality)

## Installation

1. Upload the `serial-validator` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Serial Validator > Settings** to configure options
4. Start adding codes via **Serial Validator > Import Codes**

## Usage

### Shortcode

Add the verification form to any page or post:

```
[serial_validator]
```

### Elementor Widget

1. Edit a page with Elementor
2. Search for "Serial Validator" in the widget panel
3. Drag and drop the widget onto your page
4. Customize styling in the widget settings

### Bulk Code Generation

Generate up to 10,000 unique codes at once with custom formatting:

1. Go to **Serial Validator > Generate Codes**
2. Configure generation settings:
   - **Quantity**: 1-10,000 codes
   - **Code Format**: Alphanumeric, Numeric, or Alphabetic
   - **Code Length**: 6-20 characters (including prefix/suffix)
   - **Prefix**: Optional prefix (e.g., "SN-")
   - **Suffix**: Optional suffix (e.g., "-2024")
3. Set code properties:
   - **Product Name**: Required
   - **Batch Number**: Optional tracking identifier
   - **Expiry Date**: Optional expiration date
   - **Warranty**: Optional warranty period in months
   - **Status**: Active or Blocked
   - **One-Time Use**: Check to allow single verification only
4. Preview sample codes in real-time
5. Click **Generate & Download CSV** to create and download codes instantly

#### Code Format Examples

- Alphanumeric with prefix: `SN-4K9P2X7M`
- Numeric only: `846291730265`
- With prefix and suffix: `PRD-X7M9K2-24`

### Batch Management

Efficiently manage codes by batch:

1. **Filter by Batch**: Go to **Serial Validator > Codes** and use the batch dropdown filter to show codes from specific batches
2. **Bulk Actions by Batch**: 
   - Select one or more codes
   - Choose "Block Entire Batch" or "Delete Entire Batch"
   - All codes sharing the same batch number will be affected
3. **View Batch Column**: The batch column is prominently displayed in the codes list for easy identification

This feature is perfect for managing product recalls, discontinuing specific production runs, or organizing codes by manufacturing date.

### Lead Collection Settings

Control customer data collection with flexible lead requirements:

1. Go to **Serial Validator > Settings > Leads**
2. Choose when to create leads:
   - **Only on valid verification**: Only collect data from successful verifications
   - **On every attempt**: Collect data from all verification attempts
3. **Make Fields Required**:
   - **Name Required**: Users must enter their name to verify
   - **Email Required**: Users must enter their email to verify
   - **Phone Required**: Users must enter their phone number to verify
4. Fields must be enabled in Form Settings to be required

Example use cases:
- **Warranty Registration**: Require email + phone for valid verifications
- **Lead Generation**: Make all fields optional to reduce friction
- **Product Authentication**: Require name + email for genuine products only

### QR Codes

Generate QR codes pointing to:

```
https://yourdomain.com/verify/?code=XXXX
```

Replace `XXXX` with the actual serial code. The code will auto-fill in the form.

## Database Structure

### Tables Created

1. **`wp_sv_codes`** - Serial codes and product information
   - `id`, `code`, `product_name`, `batch`, `expiry_date`, `warranty_months`, `status`, `one_time_use`, `created_at`

2. **`wp_sv_verifications`** - Verification history
   - `id`, `code`, `verification_status`, `ip_address`, `user_agent`, `created_at`

3. **`wp_sv_leads`** - Customer lead data
   - `id`, `name`, `email`, `phone`, `code`, `result_status`, `verification_date`

## CSV Import Format

Required columns:
- `code` - Unique serial code
- `product_name` - Product name

Optional columns:
- `batch` - Batch number
- `expiry_date` - Format: YYYY-MM-DD
- `warranty_months` - Number of months
- `status` - "active" or "blocked"

### Example CSV

```csv
code,product_name,batch,expiry_date,warranty_months,status
ABC123,Product A,BATCH-001,2026-12-31,12,active
XYZ789,Product B,BATCH-002,2027-06-30,24,active
DEF456,Product C,BATCH-001,,6,active
```

## Settings

### Form Settings
- Toggle Name, Email, and Phone fields

### Verification Settings
- Enable one-time use for codes
- Allow re-verification of used codes

### Security Settings
- Rate limiting (attempts per hour)
- Google reCAPTCHA v2 integration

### Messages
- Customize all user-facing messages

### Leads
- Choose when to create leads (valid only / all attempts)
- Make name, email, or phone fields required for verification
- Control data collection requirements independently

## Hooks & Filters

### Action Hooks

```php
// After successful verification
do_action('sv_after_verification', $code_data, $verification_status);

// After code import
do_action('sv_after_code_import', $imported_codes);

// After lead creation
do_action('sv_lead_created', $lead_id, $lead_data);
```

### Filter Hooks

```php
// Modify verification result before returning
apply_filters('sv_verification_result', $result, $code_data);

// Modify lead data before saving
apply_filters('sv_lead_data', $lead_data, $code);

// Modify custom messages
apply_filters('sv_custom_messages', $messages, $settings);
```

## Security

- All AJAX requests verify WordPress nonces
- Admin pages require `manage_options` capability
- Database queries use prepared statements
- User inputs are sanitized and validated
- Outputs are properly escaped
- Rate limiting prevents abuse
- Optional reCAPTCHA for additional protection

## Privacy & GDPR

The plugin integrates with WordPress privacy features:

- Adds privacy policy content suggestions
- Exports personal data on request (leads)
- Erases personal data on request (leads)
- No tracking cookies or external requests (except reCAPTCHA if enabled)

## Uninstallation

When the plugin is deleted (not just deactivated), it will:

- Drop all custom database tables
- Delete all plugin options
- Clean up transients

Note: Data is preserved during deactivation, only removed on full uninstall.

## Support

For issues, feature requests, or questions:

- Check the documentation above
- Review settings in **Serial Validator > Settings**
- Verify database tables were created properly

## Changelog

### Version 1.0.0
- Initial release
- Frontend verification form with shortcode and Elementor widget
- Admin dashboard with statistics
- Code management with bulk actions
- CSV import/export functionality
- Lead collection and management
- Settings page with full customization
- Rate limiting and reCAPTCHA support
- GDPR compliance features
- Translation ready

## Credits

Developed with ❤️ for WordPress

## License

GPL v2 or later
