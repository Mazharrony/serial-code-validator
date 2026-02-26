<?php
/**
 * Leads management view.
 *
 * @package Serial_Validator
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Handle CSV export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-csv-handler.php';
    Serial_Validator_CSV_Handler::export_leads();
    exit;
}

// Handle bulk actions
if (isset($_POST['action']) && $_POST['action'] !== '-1') {
    check_admin_referer('bulk-leads');
    
    $action = sanitize_text_field($_POST['action']);
    $leads = isset($_POST['leads']) ? array_map('intval', $_POST['leads']) : array();
    
    if (!empty($leads)) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_leads';
        $ids = implode(',', $leads);
        
        switch ($action) {
            case 'delete':
                $wpdb->query("DELETE FROM {$table} WHERE id IN ({$ids})");
                echo '<div class="notice notice-success"><p>' . esc_html__('Leads deleted.', 'serial-validator') . '</p></div>';
                break;
            case 'export':
                require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-csv-handler.php';
                Serial_Validator_CSV_Handler::export_leads($leads);
                exit;
                break;
        }
    }
}

// Create table instance
$table = new Serial_Validator_Leads_List_Table();
$table->prepare_items();
?>

<div class="wrap">
    <h1>
        <?php esc_html_e('Leads', 'serial-validator'); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-leads&action=export')); ?>" class="page-title-action">
            <?php esc_html_e('Export All', 'serial-validator'); ?>
        </a>
    </h1>
    
    <form method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>">
        <?php
        $table->search_box(__('Search Leads', 'serial-validator'), 'search_id');
        ?>
    </form>
    
    <form method="post">
        <?php
        $table->display();
        ?>
    </form>
</div>
