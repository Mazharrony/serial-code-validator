<?php
/**
 * Leads list table.
 *
 * @package Serial_Validator
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Serial_Validator_Leads_List_Table extends WP_List_Table {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'lead',
            'plural' => 'leads',
            'ajax' => false
        ));
    }

    /**
     * Get columns.
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'serial-validator'),
            'email' => __('Email', 'serial-validator'),
            'phone' => __('Phone', 'serial-validator'),
            'code' => __('Code', 'serial-validator'),
            'result_status' => __('Status', 'serial-validator'),
            'verification_date' => __('Date', 'serial-validator')
        );
    }

    /**
     * Get sortable columns.
     */
    public function get_sortable_columns() {
        return array(
            'name' => array('name', false),
            'email' => array('email', false),
            'code' => array('code', false),
            'result_status' => array('result_status', false),
            'verification_date' => array('verification_date', true)
        );
    }

    /**
     * Get bulk actions.
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __('Delete', 'serial-validator'),
            'export' => __('Export Selected', 'serial-validator')
        );
    }

    /**
     * Checkbox column.
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="leads[]" value="%d" />', $item->id);
    }

    /**
     * Name column.
     */
    public function column_name($item) {
        return esc_html($item->name ?: '—');
    }

    /**
     * Status column.
     */
    public function column_result_status($item) {
        $status_classes = array(
            'valid' => 'sv-status-valid',
            'invalid' => 'sv-status-invalid',
            'used' => 'sv-status-used',
            'blocked' => 'sv-status-blocked'
        );
        
        $class = isset($status_classes[$item->result_status]) ? $status_classes[$item->result_status] : 'sv-status-unknown';
        
        return sprintf('<span class="sv-status-badge %s">%s</span>', $class, esc_html(ucfirst($item->result_status)));
    }

    /**
     * Default column.
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'email':
            case 'phone':
            case 'code':
                return esc_html($item->$column_name ?: '—');
            case 'verification_date':
                return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->$column_name));
            default:
                return '';
        }
    }

    /**
     * Prepare items.
     */
    public function prepare_items() {
        global $wpdb;
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $table = $wpdb->prefix . 'sv_leads';
        
        // Handle search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        
        // Handle filters
        $filter_status = isset($_REQUEST['filter_status']) ? sanitize_text_field($_REQUEST['filter_status']) : '';
        
        // Handle sorting
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'verification_date';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';
        
        // Build query
        $where = '1=1';
        if (!empty($search)) {
            $where .= $wpdb->prepare(' AND (email LIKE %s OR code LIKE %s)', '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
        }
        
        if (!empty($filter_status)) {
            $where .= $wpdb->prepare(' AND result_status = %s', $filter_status);
        }
        
        // Get total items
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE {$where}");
        
        // Get items
        $offset = ($current_page - 1) * $per_page;
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE {$where} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );
        
        $this->items = $items;
        
        // Pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
        
        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns()
        );
    }
    
    /**
     * Display when no items.
     */
    public function no_items() {
        esc_html_e('No leads found.', 'serial-validator');
    }
    
    /**
     * Extra tablenav (filters).
     */
    protected function extra_tablenav($which) {
        if ($which === 'top') {
            ?>
            <div class="alignleft actions">
                <select name="filter_status">
                    <option value=""><?php esc_html_e('All Statuses', 'serial-validator'); ?></option>
                    <option value="valid" <?php selected(isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'valid'); ?>><?php esc_html_e('Valid', 'serial-validator'); ?></option>
                    <option value="invalid" <?php selected(isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'invalid'); ?>><?php esc_html_e('Invalid', 'serial-validator'); ?></option>
                    <option value="used" <?php selected(isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'used'); ?>><?php esc_html_e('Used', 'serial-validator'); ?></option>
                    <option value="blocked" <?php selected(isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'blocked'); ?>><?php esc_html_e('Blocked', 'serial-validator'); ?></option>
                </select>
                <input type="submit" class="button" value="<?php esc_attr_e('Filter', 'serial-validator'); ?>">
            </div>
            <?php
        }
    }
}
