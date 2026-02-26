<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Serial_Validator
 */

// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Drop custom database tables.
$table_codes = $wpdb->prefix . 'sv_codes';
$table_verifications = $wpdb->prefix . 'sv_verifications';
$table_leads = $wpdb->prefix . 'sv_leads';

$wpdb->query("DROP TABLE IF EXISTS {$table_codes}");
$wpdb->query("DROP TABLE IF EXISTS {$table_verifications}");
$wpdb->query("DROP TABLE IF EXISTS {$table_leads}");

// Delete plugin options.
delete_option('serial_validator_settings');
delete_option('serial_validator_db_version');

// Clean up transients (rate limiting).
$wpdb->query(
    "DELETE FROM {$wpdb->options} 
    WHERE option_name LIKE '_transient_sv_rate_limit_%' 
    OR option_name LIKE '_transient_timeout_sv_rate_limit_%'"
);

// On multisite, delete from all sites.
if (is_multisite()) {
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        
        $table_codes = $wpdb->prefix . 'sv_codes';
        $table_verifications = $wpdb->prefix . 'sv_verifications';
        $table_leads = $wpdb->prefix . 'sv_leads';
        
        $wpdb->query("DROP TABLE IF EXISTS {$table_codes}");
        $wpdb->query("DROP TABLE IF EXISTS {$table_verifications}");
        $wpdb->query("DROP TABLE IF EXISTS {$table_leads}");
        
        delete_option('serial_validator_settings');
        delete_option('serial_validator_db_version');
        
        restore_current_blog();
    }
}
