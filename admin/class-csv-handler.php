<?php
/**
 * CSV import/export handler.
 *
 * @package Serial_Validator
 */
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- admin-only direct DB operations

class Serial_Validator_CSV_Handler {

    /**
     * Import codes from CSV file.
     */
    public static function import_codes($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array(
                'success' => false,
                'message' => __('Invalid file upload.', 'serial-validator')
            );
        }
        
        // Check file type
        $file_type = wp_check_filetype($file['name']);
        if ($file_type['ext'] !== 'csv') {
            return array(
                'success' => false,
                'message' => __('Please upload a CSV file.', 'serial-validator')
            );
        }
        
        // Open file
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Reading uploaded CSV stream.
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            return array(
                'success' => false,
                'message' => __('Could not read file.', 'serial-validator')
            );
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'sv_codes';
        
        $success_count = 0;
        $error_count = 0;
        $duplicate_count = 0;
        $errors = array();
        $row_number = 0;
        
        // Read header row
        $headers = fgetcsv($handle);
        if ($headers === false) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing uploaded CSV stream.
            fclose($handle);
            return array(
                'success' => false,
                'message' => __('CSV file is empty.', 'serial-validator')
            );
        }
        
        // Validate headers
        $required_headers = array('code', 'product_name');
        $header_map = array_flip(array_map('strtolower', $headers));
        
        foreach ($required_headers as $required) {
            if (!isset($header_map[$required])) {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing uploaded CSV stream.
                fclose($handle);
                return array(
                    'success' => false,
                    /* translators: %s is the missing CSV column name. */
                    'message' => sprintf(__('Missing required column: %s', 'serial-validator'), $required)
                );
            }
        }
        
        // Process rows
        while (($row = fgetcsv($handle)) !== false) {
            $row_number++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Map data
            $data = array();
            foreach ($headers as $index => $header) {
                $data[strtolower($header)] = isset($row[$index]) ? $row[$index] : '';
            }
            
            // Validate required fields
            if (empty($data['code']) || empty($data['product_name'])) {
                /* translators: %d is the CSV row number. */
                $errors[] = sprintf(__('Row %d: Missing required fields', 'serial-validator'), $row_number);
                $error_count++;
                continue;
            }
            
            // Check for duplicate
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table} WHERE LOWER(code) = LOWER(%s)",
                $data['code']
            ));
            
            if ($existing) {
                $duplicate_count++;
                continue;
            }
            
            // Prepare insert data
            $insert_data = array(
                'code' => sanitize_text_field($data['code']),
                'product_name' => sanitize_text_field($data['product_name']),
                'batch' => isset($data['batch']) ? sanitize_text_field($data['batch']) : null,
                'expiry_date' => isset($data['expiry_date']) && !empty($data['expiry_date']) ? sanitize_text_field($data['expiry_date']) : null,
                'warranty_months' => isset($data['warranty_months']) && !empty($data['warranty_months']) ? intval($data['warranty_months']) : null,
                'status' => isset($data['status']) && in_array(strtolower($data['status']), array('active', 'blocked')) ? strtolower($data['status']) : 'active',
                'one_time_use' => 1,
                'created_at' => current_time('mysql')
            );
            
            // Insert
            $result = $wpdb->insert($table, $insert_data);
            
            if ($result === false) {
                /* translators: %d is the CSV row number. */
                $errors[] = sprintf(__('Row %d: Database error', 'serial-validator'), $row_number);
                $error_count++;
            } else {
                $success_count++;
            }
        }
        
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing uploaded CSV stream.
        fclose($handle);
        
        // Build result message
        /* translators: %d is number of imported codes. */
        $message = sprintf(__('Import completed: %d codes added', 'serial-validator'), $success_count);
        
        if ($duplicate_count > 0) {
            /* translators: %d is number of duplicate rows skipped. */
            $message .= ', ' . sprintf(__('%d duplicates skipped', 'serial-validator'), $duplicate_count);
        }
        
        if ($error_count > 0) {
            /* translators: %d is number of rows that failed import. */
            $message .= ', ' . sprintf(__('%d errors', 'serial-validator'), $error_count);
        }
        
        return array(
            'success' => true,
            'message' => $message,
            'success_count' => $success_count,
            'duplicate_count' => $duplicate_count,
            'error_count' => $error_count,
            'errors' => $errors
        );
    }
    
    /**
     * Export codes to CSV.
     */
    public static function export_codes() {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_codes';
        
        $codes = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A);
        
        if (empty($codes)) {
            wp_die(esc_html__('No codes to export.', 'serial-validator'));
        }
        
        // Set headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="serial-codes-' . gmdate('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Open output stream
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Writing CSV to output stream.
        $output = fopen('php://output', 'w');
        
        // Write BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, array('code', 'product_name', 'batch', 'expiry_date', 'warranty_months', 'status', 'one_time_use', 'created_at'));
        
        // Write data
        foreach ($codes as $code) {
            fputcsv($output, $code);
        }
        
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing output stream.
        fclose($output);
        exit;
    }
    
    /**
     * Export leads to CSV.
     */
    public static function export_leads($lead_ids = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_leads';
        
        if ($lead_ids && is_array($lead_ids)) {
            $ids = implode(',', array_map('intval', $lead_ids));
            $leads = $wpdb->get_results("SELECT * FROM {$table} WHERE id IN ({$ids}) ORDER BY verification_date DESC", ARRAY_A);
        } else {
            $leads = $wpdb->get_results("SELECT * FROM {$table} ORDER BY verification_date DESC", ARRAY_A);
        }
        
        if (empty($leads)) {
            wp_die(esc_html__('No leads to export.', 'serial-validator'));
        }
        
        // Set headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="serial-leads-' . gmdate('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Open output stream
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Writing CSV to output stream.
        $output = fopen('php://output', 'w');
        
        // Write BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, array('name', 'email', 'phone', 'code', 'result_status', 'verification_date'));
        
        // Write data
        foreach ($leads as $lead) {
            fputcsv($output, array(
                $lead['name'],
                $lead['email'],
                $lead['phone'],
                $lead['code'],
                $lead['result_status'],
                $lead['verification_date']
            ));
        }
        
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing output stream.
        fclose($output);
        exit;
    }
    
    /**
     * Generate bulk codes.
     */
    public static function generate_codes($params) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_codes';
        
        // Extract parameters
        $quantity = isset($params['quantity']) ? intval($params['quantity']) : 0;
        $product_name = isset($params['product_name']) ? sanitize_text_field($params['product_name']) : '';
        $batch = isset($params['batch']) ? sanitize_text_field($params['batch']) : '';
        $expiry_date = isset($params['expiry_date']) && !empty($params['expiry_date']) ? sanitize_text_field($params['expiry_date']) : null;
        $warranty_months = isset($params['warranty_months']) && !empty($params['warranty_months']) ? intval($params['warranty_months']) : null;
        $status = isset($params['status']) ? sanitize_text_field($params['status']) : 'active';
        $one_time_use = isset($params['one_time_use']) ? 1 : 0;
        
        // Code format options
        $code_length = isset($params['code_length']) ? intval($params['code_length']) : 12;
        $code_prefix = isset($params['code_prefix']) ? sanitize_text_field($params['code_prefix']) : '';
        $code_suffix = isset($params['code_suffix']) ? sanitize_text_field($params['code_suffix']) : '';
        $code_format = isset($params['code_format']) ? sanitize_text_field($params['code_format']) : 'alphanumeric';
        
        // Validation
        if ($quantity < 1 || $quantity > 10000) {
            return array(
                'success' => false,
                'message' => __('Quantity must be between 1 and 10,000.', 'serial-validator')
            );
        }
        
        if (empty($product_name)) {
            return array(
                'success' => false,
                'message' => __('Product name is required.', 'serial-validator')
            );
        }
        
        // Generate codes
        $generated_codes = array();
        $success_count = 0;
        $duplicate_count = 0;
        $attempts = 0;
        $max_attempts = $quantity * 10; // Safety limit
        
        while ($success_count < $quantity && $attempts < $max_attempts) {
            $attempts++;
            
            // Generate unique code
            $code = self::generate_unique_code($code_length, $code_prefix, $code_suffix, $code_format);
            
            // Check if code already exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table} WHERE LOWER(code) = LOWER(%s)",
                $code
            ));
            
            if ($exists) {
                $duplicate_count++;
                continue;
            }
            
            // Insert code
            $result = $wpdb->insert($table, array(
                'code' => $code,
                'product_name' => $product_name,
                'batch' => $batch,
                'expiry_date' => $expiry_date,
                'warranty_months' => $warranty_months,
                'status' => $status,
                'one_time_use' => $one_time_use,
                'created_at' => current_time('mysql')
            ));
            
            if ($result !== false) {
                $success_count++;
                $generated_codes[] = array(
                    'code' => $code,
                    'product_name' => $product_name,
                    'batch' => $batch,
                    'expiry_date' => $expiry_date,
                    'warranty_months' => $warranty_months,
                    'status' => $status,
                    'one_time_use' => $one_time_use
                );
            }
        }
        
        if ($success_count === 0) {
            return array(
                'success' => false,
                'message' => __('Failed to generate codes. Please try again.', 'serial-validator')
            );
        }
        
        return array(
            'success' => true,
            /* translators: %d is number of successfully generated codes. */
            'message' => sprintf(__('Successfully generated %d codes.', 'serial-validator'), $success_count),
            'success_count' => $success_count,
            'duplicate_count' => $duplicate_count,
            'codes' => $generated_codes
        );
    }
    
    /**
     * Generate a unique code.
     */
    private static function generate_unique_code($length, $prefix, $suffix, $format) {
        $code = '';
        
        // Determine character set
        switch ($format) {
            case 'numeric':
                $characters = '0123456789';
                break;
            case 'alphabetic':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alphanumeric':
            default:
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
        
        // Calculate actual code length (minus prefix and suffix)
        $actual_length = $length - strlen($prefix) - strlen($suffix);
        if ($actual_length < 4) {
            $actual_length = 4; // Minimum 4 characters for uniqueness
        }
        
        // Generate random code
        $characters_length = strlen($characters);
        for ($i = 0; $i < $actual_length; $i++) {
            $code .= $characters[random_int(0, $characters_length - 1)];
        }
        
        // Add prefix and suffix
        return strtoupper($prefix . $code . $suffix);
    }
    
    /**
     * Download generated codes as CSV.
     */
    public static function download_generated_codes($codes) {
        if (empty($codes)) {
            wp_die(esc_html__('No codes to download.', 'serial-validator'));
        }
        
        // Set headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="generated-codes-' . gmdate('Y-m-d-His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Open output stream
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Writing CSV to output stream.
        $output = fopen('php://output', 'w');
        
        // Write BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, array('code', 'product_name', 'batch', 'expiry_date', 'warranty_months', 'status', 'one_time_use'));
        
        // Write data
        foreach ($codes as $code) {
            fputcsv($output, array(
                $code['code'],
                $code['product_name'],
                $code['batch'],
                $code['expiry_date'],
                $code['warranty_months'],
                $code['status'],
                $code['one_time_use']
            ));
        }
        
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing output stream.
        fclose($output);
        exit;
    }
}
