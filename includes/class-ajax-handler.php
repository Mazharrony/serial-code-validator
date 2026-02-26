<?php
/**
 * AJAX handler for verification requests.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Ajax_Handler {

    /**
     * Handle code verification AJAX request.
     */
    public function verify_code() {
        // Verify nonce.
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'sv_verify_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'serial-validator')));
        }
        
        // Get settings.
        $settings = get_option('serial_validator_settings', array());
        
        // Check reCAPTCHA if enabled.
        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_secret_key'])) {
            if (!isset($_POST['recaptcha_response']) || empty($_POST['recaptcha_response'])) {
                wp_send_json_error(array('message' => __('Please complete the reCAPTCHA.', 'serial-validator')));
            }
            
            $recaptcha_response = sanitize_text_field(wp_unslash($_POST['recaptcha_response']));
            $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
            
            $response = wp_remote_post($verify_url, array(
                'body' => array(
                    'secret' => $settings['recaptcha_secret_key'],
                    'response' => $recaptcha_response,
                    'remoteip' => $this->get_client_ip()
                )
            ));
            
            if (is_wp_error($response)) {
                wp_send_json_error(array('message' => __('reCAPTCHA verification failed.', 'serial-validator')));
            }
            
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (empty($body['success'])) {
                wp_send_json_error(array('message' => __('reCAPTCHA verification failed.', 'serial-validator')));
            }
        }
        
        // Check rate limiting.
        if (!empty($settings['rate_limit_enabled'])) {
            $ip = $this->get_client_ip();
            $rate_limit_key = 'sv_rate_limit_' . md5($ip);
            $attempts = get_transient($rate_limit_key);
            $limit = isset($settings['rate_limit_attempts']) ? (int)$settings['rate_limit_attempts'] : 5;
            
            if ($attempts !== false && $attempts >= $limit) {
                wp_send_json_error(array(
                    'message' => isset($settings['message_rate_limit']) ? $settings['message_rate_limit'] : __('Too many attempts. Please try again later.', 'serial-validator')
                ));
            }
            
            // Increment attempt counter.
            $duration = isset($settings['rate_limit_duration']) ? (int)$settings['rate_limit_duration'] : 3600;
            set_transient($rate_limit_key, ($attempts === false ? 1 : $attempts + 1), $duration);
        }
        
        // Sanitize and validate input.
        $code = isset($_POST['code']) ? sanitize_text_field(wp_unslash($_POST['code'])) : '';
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        
        if (empty($code)) {
            wp_send_json_error(array('message' => __('Please enter a serial code.', 'serial-validator')));
        }
        
        // Validate required lead fields (only if field is enabled AND required)
        if (!empty($settings['form_enable_name']) && !empty($settings['lead_require_name']) && empty($name)) {
            wp_send_json_error(array('message' => __('Name is required.', 'serial-validator')));
        }
        
        if (!empty($settings['form_enable_email']) && !empty($settings['lead_require_email']) && empty($email)) {
            wp_send_json_error(array('message' => __('Email is required.', 'serial-validator')));
        }
        
        if (!empty($settings['form_enable_phone']) && !empty($settings['lead_require_phone']) && empty($phone)) {
            wp_send_json_error(array('message' => __('Phone number is required.', 'serial-validator')));
        }
        
        // Validate code format (6-50 alphanumeric characters).
        if (!preg_match('/^[a-zA-Z0-9]{6,50}$/', $code)) {
            wp_send_json_error(array('message' => __('Invalid code format. Please use 6-50 alphanumeric characters.', 'serial-validator')));
        }
        
        // Validate email if provided.
        if (!empty($email) && !is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'serial-validator')));
        }
        
        // Check if code exists.
        $code_data = Serial_Validator_Database::get_code($code);
        
        if (!$code_data) {
            // Code not found - invalid.
            $verification_status = 'invalid';
            $message = isset($settings['message_invalid']) ? $settings['message_invalid'] : __('Code not found. Please check and try again.', 'serial-validator');
            
            // Insert verification record.
            Serial_Validator_Database::insert_verification(array(
                'code' => $code,
                'verification_status' => $verification_status,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $this->get_user_agent(),
                'created_at' => current_time('mysql')
            ));
            
            // Create lead if setting allows.
            $this->maybe_create_lead($settings, $name, $email, $phone, $code, $verification_status);
            
            wp_send_json_error(array(
                'status' => $verification_status,
                'message' => $message
            ));
        }
        
        // Check if code is blocked.
        if ($code_data->status === 'blocked') {
            $verification_status = 'blocked';
            $message = isset($settings['message_blocked']) ? $settings['message_blocked'] : __('This code is not valid. Please contact support.', 'serial-validator');
            
            Serial_Validator_Database::insert_verification(array(
                'code' => $code,
                'verification_status' => $verification_status,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $this->get_user_agent(),
                'created_at' => current_time('mysql')
            ));
            
            $this->maybe_create_lead($settings, $name, $email, $phone, $code, $verification_status);
            
            wp_send_json_error(array(
                'status' => $verification_status,
                'message' => $message
            ));
        }
        
        // Check expiry date.
        if (!empty($code_data->expiry_date)) {
            $expiry = strtotime($code_data->expiry_date);
            if ($expiry !== false && $expiry < time()) {
                $verification_status = 'invalid';
                $message = __('This code has expired.', 'serial-validator');
                
                Serial_Validator_Database::insert_verification(array(
                    'code' => $code,
                    'verification_status' => $verification_status,
                    'ip_address' => $this->get_client_ip(),
                    'user_agent' => $this->get_user_agent(),
                    'created_at' => current_time('mysql')
                ));
                
                $this->maybe_create_lead($settings, $name, $email, $phone, $code, $verification_status);
                
                wp_send_json_error(array(
                    'status' => $verification_status,
                    'message' => $message
                ));
            }
        }
        
        // Check if already verified (for one-time use codes).
        $is_verified = Serial_Validator_Database::is_code_verified($code);
        
        if ($is_verified && $code_data->one_time_use) {
            $verification_status = 'used';
            $first_verification = Serial_Validator_Database::get_first_verification_date($code);
            $message = isset($settings['message_used']) ? $settings['message_used'] : __('This code has already been verified.', 'serial-validator');
            
            Serial_Validator_Database::insert_verification(array(
                'code' => $code,
                'verification_status' => $verification_status,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $this->get_user_agent(),
                'created_at' => current_time('mysql')
            ));
            
            $this->maybe_create_lead($settings, $name, $email, $phone, $code, $verification_status);
            
            wp_send_json_error(array(
                'status' => $verification_status,
                'message' => $message,
                'first_verified' => $first_verification ? date_i18n(get_option('date_format'), strtotime($first_verification)) : ''
            ));
        }
        
        // Code is valid!
        $verification_status = 'valid';
        $message = isset($settings['message_valid']) ? $settings['message_valid'] : __('Authentic Product! This code is valid.', 'serial-validator');
        
        // Calculate warranty expiry if applicable.
        $warranty_info = '';
        if (!empty($code_data->warranty_months)) {
            if ($is_verified) {
                // Use first verification date for warranty calculation.
                $first_verification = Serial_Validator_Database::get_first_verification_date($code);
                if ($first_verification) {
                    $warranty_expiry = gmdate('Y-m-d', strtotime($first_verification . ' + ' . $code_data->warranty_months . ' months'));
                    /* translators: %s is a formatted date. */
                    $warranty_info = sprintf(__('Warranty valid until: %s', 'serial-validator'), date_i18n(get_option('date_format'), strtotime($warranty_expiry)));
                }
            } else {
                // This is first verification, warranty starts now.
                $warranty_expiry = gmdate('Y-m-d', strtotime('+ ' . $code_data->warranty_months . ' months'));
                /* translators: %d is the number of warranty months. */
                $warranty_info = sprintf(__('Warranty valid for %d months', 'serial-validator'), $code_data->warranty_months);
            }
        }
        
        // Insert verification record.
        Serial_Validator_Database::insert_verification(array(
            'code' => $code,
            'verification_status' => $verification_status,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $this->get_user_agent(),
            'created_at' => current_time('mysql')
        ));
        
        // Create lead.
        $this->maybe_create_lead($settings, $name, $email, $phone, $code, $verification_status);
        
        // Success response.
        wp_send_json_success(array(
            'status' => $verification_status,
            'message' => $message,
            'product_name' => $code_data->product_name,
            'batch' => $code_data->batch,
            'warranty_info' => $warranty_info
        ));
    }
    
    /**
     * Create lead based on settings.
     */
    private function maybe_create_lead($settings, $name, $email, $phone, $code, $status) {
        $lead_rule = isset($settings['lead_creation_rule']) ? $settings['lead_creation_rule'] : 'valid_only';
        
        // Check if we should create a lead.
        if ($lead_rule === 'valid_only' && $status !== 'valid') {
            return;
        }
        
        // Insert lead.
        Serial_Validator_Database::insert_lead(array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'code' => $code,
            'result_status' => $status,
            'verification_date' => current_time('mysql')
        ));
    }
    
    /**
     * Get client IP address.
     */
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } else {
            $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        }
        
        return $ip;
    }
    
    /**
     * Get user agent.
     */
    private function get_user_agent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
    }
}
