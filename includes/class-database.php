<?php
/**
 * Database operations.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Database {

    /**
     * Create custom database tables.
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $db_version = '1.0.0';
        
        // Codes table.
        $table_codes = $wpdb->prefix . 'sv_codes';
        $sql_codes = "CREATE TABLE {$table_codes} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            code varchar(100) NOT NULL,
            product_name varchar(255) NOT NULL,
            batch varchar(100) DEFAULT NULL,
            expiry_date date DEFAULT NULL,
            warranty_months int(11) DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            one_time_use tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code),
            KEY status (status)
        ) {$charset_collate};";
        
        // Verifications table.
        $table_verifications = $wpdb->prefix . 'sv_verifications';
        $sql_verifications = "CREATE TABLE {$table_verifications} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            code varchar(100) NOT NULL,
            verification_status varchar(20) NOT NULL,
            ip_address varchar(100) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY code (code),
            KEY created_at (created_at),
            KEY verification_status (verification_status)
        ) {$charset_collate};";
        
        // Leads table.
        $table_leads = $wpdb->prefix . 'sv_leads';
        $sql_leads = "CREATE TABLE {$table_leads} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            code varchar(100) NOT NULL,
            result_status varchar(20) NOT NULL,
            verification_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY email (email),
            KEY code (code),
            KEY result_status (result_status)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_codes);
        dbDelta($sql_verifications);
        dbDelta($sql_leads);
        
        update_option('serial_validator_db_version', $db_version);
    }
    
    /**
     * Get code by code string.
     */
    public static function get_code($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_codes';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE LOWER(code) = LOWER(%s)",
            $code
        ));
    }
    
    /**
     * Check if code has been verified.
     */
    public static function is_code_verified($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_verifications';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE LOWER(code) = LOWER(%s) AND verification_status = 'valid'",
            $code
        ));

        return $count > 0;
    }
    
    /**
     * Get first verification date for a code.
     */
    public static function get_first_verification_date($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_verifications';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        return $wpdb->get_var($wpdb->prepare(
            "SELECT created_at FROM {$table} WHERE LOWER(code) = LOWER(%s) AND verification_status = 'valid' ORDER BY created_at ASC LIMIT 1",
            $code
        ));
    }
    
    /**
     * Insert verification record.
     */
    public static function insert_verification($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_verifications';
        
        return $wpdb->insert($table, $data);
    }
    
    /**
     * Insert lead record.
     */
    public static function insert_lead($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_leads';
        
        return $wpdb->insert($table, $data);
    }
    
    /**
     * Get verification statistics.
     */
    public static function get_stats($days = 7) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_verifications';
        $date_from = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));

        $stats = array();

        // Total verifications.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $stats['total'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s",
            $date_from
        ));

        // Valid codes.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $stats['valid'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE verification_status = 'valid' AND created_at >= %s",
            $date_from
        ));

        // Invalid attempts.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $stats['invalid'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE verification_status = 'invalid' AND created_at >= %s",
            $date_from
        ));

        // Already used.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $stats['used'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE verification_status = 'used' AND created_at >= %s",
            $date_from
        ));

        // Blocked.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $stats['blocked'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE verification_status = 'blocked' AND created_at >= %s",
            $date_from
        ));

        return $stats;
    }
    
    /**
     * Get daily verification counts for chart.
     */
    public static function get_daily_counts($days = 7) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_verifications';
        $date_from = gmdate('Y-m-d', strtotime("-{$days} days"));

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        return $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count
            FROM {$table}
            WHERE DATE(created_at) >= %s
            GROUP BY DATE(created_at)
            ORDER BY date ASC",
            $date_from
        ));
    }
}
