<?php
/**
 * Privacy/GDPR functionality.
 *
 * @package Serial_Validator
 */
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- admin privacy handler, direct DB required

class Serial_Validator_Privacy {

    /**
     * Add privacy policy content.
     */
    public function add_privacy_policy() {
        if (!function_exists('wp_add_privacy_policy_content')) {
            return;
        }
        
        $content = sprintf(
            '<h2>%s</h2><p>%s</p><h3>%s</h3><p>%s</p><ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
            __('Serial Validator', 'serial-validator'),
            __('This site uses the Serial Validator plugin to verify product authenticity and collect customer information.', 'serial-validator'),
            __('What data we collect', 'serial-validator'),
            __('When you verify a serial code, we may collect:', 'serial-validator'),
            __('Your name (optional)', 'serial-validator'),
            __('Your email address (optional)', 'serial-validator'),
            __('Your phone number (optional)', 'serial-validator'),
            __('The serial code you entered, verification timestamp, and IP address', 'serial-validator')
        );
        
        wp_add_privacy_policy_content('Serial Validator', wp_kses_post($content));
    }

    /**
     * Register personal data exporter.
     */
    public function register_exporter($exporters) {
        $exporters['serial-validator'] = array(
            'exporter_friendly_name' => __('Serial Validator', 'serial-validator'),
            'callback' => array($this, 'export_personal_data')
        );
        return $exporters;
    }

    /**
     * Export personal data.
     */
    public function export_personal_data($email_address, $page = 1) {
        global $wpdb;
        
        $data_to_export = array();
        $table_leads = $wpdb->prefix . 'sv_leads';
        $table_verifications = $wpdb->prefix . 'sv_verifications';
        
        // Get leads data.
        $leads = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_leads} WHERE email = %s",
            $email_address
        ));
        
        if (!empty($leads)) {
            $lead_data = array();
            
            foreach ($leads as $lead) {
                $lead_data[] = array(
                    array(
                        'name' => __('Name', 'serial-validator'),
                        'value' => $lead->name
                    ),
                    array(
                        'name' => __('Email', 'serial-validator'),
                        'value' => $lead->email
                    ),
                    array(
                        'name' => __('Phone', 'serial-validator'),
                        'value' => $lead->phone
                    ),
                    array(
                        'name' => __('Serial Code', 'serial-validator'),
                        'value' => $lead->code
                    ),
                    array(
                        'name' => __('Verification Status', 'serial-validator'),
                        'value' => $lead->result_status
                    ),
                    array(
                        'name' => __('Verification Date', 'serial-validator'),
                        'value' => $lead->verification_date
                    )
                );
            }
            
            $data_to_export[] = array(
                'group_id' => 'serial-validator-leads',
                'group_label' => __('Serial Validator Leads', 'serial-validator'),
                'item_id' => 'leads',
                'data' => $lead_data
            );
        }
        
        return array(
            'data' => $data_to_export,
            'done' => true
        );
    }

    /**
     * Register personal data eraser.
     */
    public function register_eraser($erasers) {
        $erasers['serial-validator'] = array(
            'eraser_friendly_name' => __('Serial Validator', 'serial-validator'),
            'callback' => array($this, 'erase_personal_data')
        );
        return $erasers;
    }

    /**
     * Erase personal data.
     */
    public function erase_personal_data($email_address, $page = 1) {
        global $wpdb;
        
        $table_leads = $wpdb->prefix . 'sv_leads';
        $items_removed = 0;
        $items_retained = 0;
        
        // Delete leads with this email.
        $deleted = $wpdb->delete($table_leads, array('email' => $email_address));
        
        if ($deleted !== false) {
            $items_removed = $deleted;
        }
        
        return array(
            'items_removed' => $items_removed,
            'items_retained' => $items_retained,
            'messages' => array(),
            'done' => true
        );
    }
}
