<?php
/**
 * User guide view.
 *
 * @package Serial_Validator
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap sv-guide">
    <h1><?php esc_html_e('Serial Validator User Guide', 'serial-validator'); ?></h1>

    <div class="sv-info-box">
        <h3><?php esc_html_e('Quick Start', 'serial-validator'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to Settings and configure form fields, security, and lead options.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Add or import serial codes from Codes / Import Codes / Generate Codes.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Place shortcode [serial_validator] on a page to show the verification form.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Open Dashboard to monitor verification activity and trends.', 'serial-validator'); ?></li>
        </ol>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('1) Managing Codes', 'serial-validator'); ?></h3>
        <ul>
            <li><?php esc_html_e('Add single codes in Codes > Add New Code.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Bulk import CSV from Import Codes.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Generate up to 10,000 unique codes from Generate Codes.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Use bulk actions to Activate, Block, Delete, or apply actions by Batch.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Use batch filter in Codes list to view codes from a specific batch.', 'serial-validator'); ?></li>
        </ul>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('2) Frontend Verification Form', 'serial-validator'); ?></h3>
        <p><?php esc_html_e('Use the shortcode below on any page/post:', 'serial-validator'); ?></p>
        <code>[serial_validator]</code>
        <p><?php esc_html_e('If Elementor is active, you can also use the “Serial Validator” widget.', 'serial-validator'); ?></p>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('3) Lead Collection', 'serial-validator'); ?></h3>
        <ul>
            <li><?php esc_html_e('Enable/disable Name, Email, and Phone fields in Settings > Form Settings.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Choose lead creation rule in Settings > Leads (valid only or all attempts).', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Mark fields as required in Settings > Leads when needed.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Export leads from Leads page as CSV.', 'serial-validator'); ?></li>
        </ul>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('4) Security & Privacy', 'serial-validator'); ?></h3>
        <ul>
            <li><?php esc_html_e('Enable rate limiting to reduce abuse.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('Enable Google reCAPTCHA only if needed (optional).', 'serial-validator'); ?></li>
            <li><?php esc_html_e('All serial code validation runs locally in WordPress database.', 'serial-validator'); ?></li>
        </ul>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('5) QR Code Usage', 'serial-validator'); ?></h3>
        <p><?php esc_html_e('Create QR codes pointing to URL format below:', 'serial-validator'); ?></p>
        <code><?php echo esc_html(home_url('/verify/?code=XXXX')); ?></code>
        <p><?php esc_html_e('Replace XXXX with an actual serial code. The form will auto-fill the code.', 'serial-validator'); ?></p>
    </div>

    <div class="sv-info-box">
        <h3><?php esc_html_e('Troubleshooting', 'serial-validator'); ?></h3>
        <ul>
            <li><?php esc_html_e('If verification fails unexpectedly, confirm code status is Active.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('If a required field error appears, ensure field visibility and requirement settings match.', 'serial-validator'); ?></li>
            <li><?php esc_html_e('If reCAPTCHA fails, verify site key and secret key in Settings > Security.', 'serial-validator'); ?></li>
        </ul>
    </div>
</div>
