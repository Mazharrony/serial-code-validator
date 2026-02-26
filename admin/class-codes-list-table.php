<?php
/**
 * Codes list table.
 *
 * @package Serial_Validator
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Serial_Validator_Codes_List_Table extends WP_List_Table {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => 'code',
            'plural' => 'codes',
            'ajax' => false
        ));
    }

    /**
     * Get columns.
     */
    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'code' => __('Code', 'serial-validator'),
            'product_name' => __('Product Name', 'serial-validator'),
            'batch' => __('Batch', 'serial-validator'),
            'expiry_date' => __('Expiry Date', 'serial-validator'),
            'status' => __('Status', 'serial-validator'),
            'one_time_use' => __('One-Time Use', 'serial-validator'),
            'created_at' => __('Created', 'serial-validator')
        );
    }

    /**
     * Get sortable columns.
     */
    public function get_sortable_columns() {
        return array(
            'code' => array('code', false),
            'product_name' => array('product_name', false),
            'status' => array('status', false),
            'created_at' => array('created_at', true)
        );
    }

    /**
     * Get bulk actions.
     */
    public function get_bulk_actions() {
        return array(
            'activate' => __('Activate', 'serial-validator'),
            'block' => __('Block', 'serial-validator'),
            'delete' => __('Delete', 'serial-validator'),
            'block_batch' => __('Block Entire Batch', 'serial-validator'),
            'delete_batch' => __('Delete Entire Batch', 'serial-validator')
        );
    }
    
    /**
     * Extra table navigation - add batch filter.
     */
    protected function extra_tablenav($which) {
        if ($which === 'top') {
            global $wpdb;
            $table = $wpdb->prefix . 'sv_codes';
            
            // Get all unique batches
            $batches = $wpdb->get_col("SELECT DISTINCT batch FROM {$table} WHERE batch != '' ORDER BY batch");
            
            if (!empty($batches)) {
                $current_batch = isset($_GET['batch']) ? sanitize_text_field($_GET['batch']) : '';
                
                echo '<div class="alignleft actions">';
                echo '<select name="batch" id="batch-filter">';
                echo '<option value="">' . esc_html__('All Batches', 'serial-validator') . '</option>';
                
                foreach ($batches as $batch) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($batch),
                        selected($current_batch, $batch, false),
                        esc_html($batch ?: __('(No Batch)', 'serial-validator'))
                    );
                }
                
                echo '</select>';
                submit_button(__('Filter', 'serial-validator'), '', 'filter_action', false);
                echo '</div>';
            }
        }
    }

    /**
     * Checkbox column.
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="codes[]" value="%d" />', $item->id);
    }

    /**
     * Code column.
     */
    public function column_code($item) {
        $actions = array(
            'delete' => sprintf(
                '<a href="?page=%s&action=delete&id=%d&_wpnonce=%s" onclick="return confirm(\'%s\');">%s</a>',
                $_REQUEST['page'],
                $item->id,
                wp_create_nonce('sv_delete_code'),
                __('Are you sure you want to delete this code?', 'serial-validator'),
                __('Delete', 'serial-validator')
            )
        );
        
        return sprintf('<strong>%s</strong>%s', $item->code, $this->row_actions($actions));
    }

    /**
     * Status column.
     */
    public function column_status($item) {
        $status_class = $item->status === 'active' ? 'sv-status-active' : 'sv-status-blocked';
        $status_text = $item->status === 'active' ? __('Active', 'serial-validator') : __('Blocked', 'serial-validator');
        
        return sprintf('<span class="sv-status-badge %s">%s</span>', $status_class, $status_text);
    }

    /**
     * One-time use column.
     */
    public function column_one_time_use($item) {
        return $item->one_time_use ? __('Yes', 'serial-validator') : __('No', 'serial-validator');
    }

    /**
     * Default column.
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'product_name':
            case 'batch':
                return esc_html($item->$column_name);
            case 'expiry_date':
                return $item->$column_name ? date_i18n(get_option('date_format'), strtotime($item->$column_name)) : '—';
            case 'created_at':
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
        $table = $wpdb->prefix . 'sv_codes';
        
        // Handle search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        
        // Handle batch filter
        $batch_filter = isset($_REQUEST['batch']) ? sanitize_text_field($_REQUEST['batch']) : '';
        
        // Handle sorting
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'created_at';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';
        
        // Build query
        $where = '1=1';
        if (!empty($search)) {
            $where .= $wpdb->prepare(' AND (code LIKE %s OR product_name LIKE %s)', '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
        }
        
        // Add batch filter
        if (!empty($batch_filter)) {
            $where .= $wpdb->prepare(' AND batch = %s', $batch_filter);
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
        esc_html_e('No codes found.', 'serial-validator');
    }
}
