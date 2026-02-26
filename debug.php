<?php
/**
 * Serial Validator - Troubleshooting Script
 * 
 * Place this file in your plugin directory and access it via:
 * yourdomain.com/wp-content/plugins/serial-validator/debug.php
 * 
 * This will help identify issues with the plugin.
 */

// WordPress environment
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo '<h1>Serial Validator - Troubleshooting</h1>';
echo '<style>body{font-family:sans-serif;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:4px;}</style>';

// Check PHP version
echo '<h2>1. PHP Version</h2>';
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo '<p class="success">✓ PHP Version: ' . PHP_VERSION . ' (OK)</p>';
} else {
    echo '<p class="error">✗ PHP Version: ' . PHP_VERSION . ' (Requires 7.4 or higher)</p>';
}

// Check WordPress version
echo '<h2>2. WordPress Version</h2>';
global $wp_version;
if (version_compare($wp_version, '5.0', '>=')) {
    echo '<p class="success">✓ WordPress Version: ' . $wp_version . ' (OK)</p>';
} else {
    echo '<p class="error">✗ WordPress Version: ' . $wp_version . ' (Requires 5.0 or higher)</p>';
}

// Check if plugin is activated
echo '<h2>3. Plugin Status</h2>';
if (is_plugin_active('serial-validator/serial-validator.php')) {
    echo '<p class="success">✓ Plugin is activated</p>';
} else {
    echo '<p class="error">✗ Plugin is not activated</p>';
}

// Check plugin files exist
echo '<h2>4. Plugin Files</h2>';
$required_files = [
    'serial-validator.php',
    'includes/class-serial-validator.php',
    'includes/class-loader.php',
    'includes/class-database.php',
    'includes/class-ajax-handler.php',
    'includes/class-shortcode.php',
    'includes/class-privacy.php',
    'admin/class-admin.php',
    'public/class-public.php',
    'widgets/class-elementor-widget.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    $path = dirname(__FILE__) . '/' . $file;
    if (file_exists($path)) {
        echo '<p class="success">✓ ' . $file . '</p>';
    } else {
        echo '<p class="error">✗ ' . $file . ' (Missing)</p>';
        $missing_files[] = $file;
    }
}

// Check database tables
echo '<h2>5. Database Tables</h2>';
global $wpdb;
$tables = [
    $wpdb->prefix . 'sv_codes',
    $wpdb->prefix . 'sv_verifications',
    $wpdb->prefix . 'sv_leads'
];

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        echo '<p class="success">✓ ' . $table . ' (Found, ' . $count . ' rows)</p>';
    } else {
        echo '<p class="error">✗ ' . $table . ' (Not found)</p>';
    }
}

// Check plugin settings
echo '<h2>6. Plugin Settings</h2>';
$settings = get_option('serial_validator_settings');
if ($settings) {
    echo '<p class="success">✓ Settings found</p>';
    echo '<pre>' . print_r($settings, true) . '</pre>';
} else {
    echo '<p class="error">✗ No settings found (Try deactivating and reactivating the plugin)</p>';
}

// Check Elementor
echo '<h2>7. Elementor Status</h2>';
if (did_action('elementor/loaded')) {
    echo '<p class="success">✓ Elementor is loaded</p>';
} else {
    echo '<p class="info">ℹ Elementor is not active (Widget will not be available)</p>';
}

// Check for errors
echo '<h2>8. Error Log Check</h2>';
if (defined('WP_DEBUG') && WP_DEBUG) {
    echo '<p class="info">✓ WP_DEBUG is enabled</p>';
} else {
    echo '<p class="info">ℹ WP_DEBUG is disabled. Enable it in wp-config.php to see errors:</p>';
    echo '<pre>define(\'WP_DEBUG\', true);
define(\'WP_DEBUG_LOG\', true);
define(\'WP_DEBUG_DISPLAY\', false);</pre>';
}

// Test class loading
echo '<h2>9. Class Loading Test</h2>';
$classes = [
    'Serial_Validator',
    'Serial_Validator_Loader',
    'Serial_Validator_Database',
    'Serial_Validator_Ajax_Handler',
    'Serial_Validator_Shortcode',
    'Serial_Validator_Privacy',
    'Serial_Validator_Admin',
    'Serial_Validator_Public'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo '<p class="success">✓ ' . $class . '</p>';
    } else {
        echo '<p class="error">✗ ' . $class . ' (Not loaded)</p>';
    }
}

// Recommendations
echo '<h2>10. Recommendations</h2>';
if (!empty($missing_files)) {
    echo '<p class="error">✗ Missing files detected. Re-upload the plugin.</p>';
}

echo '<h3>To fix the "Critical Error":</h3>';
echo '<ol>';
echo '<li>Enable WP_DEBUG in wp-config.php to see the actual error</li>';
echo '<li>Check the error log at wp-content/debug.log</li>';
echo '<li>Deactivate and reactivate the plugin</li>';
echo '<li>If tables are missing, deactivate, delete, and reinstall the plugin</li>';
echo '<li>Check file permissions (folders: 755, files: 644)</li>';
echo '<li>Ensure all plugin files were uploaded correctly</li>';
echo '</ol>';

echo '<hr>';
echo '<p><strong>Next Steps:</strong></p>';
echo '<ol>';
echo '<li>If everything shows ✓ above, try accessing <a href="' . admin_url('admin.php?page=serial-validator') . '">Serial Validator Dashboard</a></li>';
echo '<li>If errors persist, check the WordPress debug log</li>';
echo '<li>Try creating a page with the shortcode: [serial_validator]</li>';
echo '</ol>';

echo '<hr>';
echo '<p><em>Delete this debug.php file after troubleshooting for security.</em></p>';
