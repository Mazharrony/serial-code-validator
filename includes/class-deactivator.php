<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Deactivator {

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        // Flush rewrite rules.
        flush_rewrite_rules();
        
        // Note: Do NOT delete data on deactivation, only on uninstall.
    }
}
