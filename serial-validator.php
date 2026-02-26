<?php
/**
 * Plugin Name:       Serial Validator
 * Plugin URI:        https://www.meetmazhar.site/serial-validator
 * Description:       Simple, clean serial code verification system for product authenticity with lead collection, Elementor widget, and admin dashboard.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Mazhar Rony
 * Author URI:        https://www.meetmazhar.site/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       serial-validator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('SERIAL_VALIDATOR_VERSION', '1.0.0');
define('SERIAL_VALIDATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SERIAL_VALIDATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SERIAL_VALIDATOR_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Activation hook.
 *
 * @return void
 */
function serial_validator_activate() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-activator.php';
    Serial_Validator_Activator::activate();
}

/**
 * Deactivation hook.
 *
 * @return void
 */
function serial_validator_deactivate() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-deactivator.php';
    Serial_Validator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'serial_validator_activate' );
register_deactivation_hook( __FILE__, 'serial_validator_deactivate' );

/**
 * Core plugin class.
 */
require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-serial-validator.php';

/**
 * Begin execution.
 *
 * @return void
 */
function serial_validator_run() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    $plugin = new Serial_Validator();
    $plugin->run();
}

// Hook plugin initialization to plugins_loaded to ensure WordPress is fully loaded.
add_action( 'plugins_loaded', 'serial_validator_run' );
