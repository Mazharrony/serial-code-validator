<?php
/**
 * Fired during plugin activation.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-database.php';
        Serial_Validator_Database::create_tables();
        
        // Set default settings if not exists.
        if (!get_option('serial_validator_settings')) {
            $default_settings = array(
                'form_enable_name' => true,
                'form_enable_email' => false,
                'form_enable_phone' => false,
                'enable_one_time_use' => true,
                'allow_reverification' => true,
                'rate_limit_enabled' => true,
                'rate_limit_attempts' => 5,
                'rate_limit_duration' => 3600,
                'recaptcha_enabled' => false,
                'recaptcha_site_key' => '',
                'recaptcha_secret_key' => '',
                'lead_creation_rule' => 'valid_only',
                'message_valid' => __('Authentic Product! This code is valid.', 'serial-validator'),
                'message_invalid' => __('Code not found. Please check and try again.', 'serial-validator'),
                'message_used' => __('This code has already been verified.', 'serial-validator'),
                'message_blocked' => __('This code is not valid. Please contact support.', 'serial-validator'),
                'message_rate_limit' => __('Too many attempts. Please try again later.', 'serial-validator'),
            );
            update_option('serial_validator_settings', $default_settings);
        }
        
        // Flush rewrite rules.
        flush_rewrite_rules();
    }
}
