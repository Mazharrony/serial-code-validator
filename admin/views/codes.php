<?php
/**
 * Codes management view.
 *
 * @package Serial_Validator
 */
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- view partial, variables are local scope
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- admin-only direct DB operations

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Handle bulk actions
if (isset($_POST['action']) && $_POST['action'] !== '-1') {
    check_admin_referer('bulk-codes');

    $sv_post = wp_unslash($_POST);
    
    $action = isset($sv_post['action']) ? sanitize_text_field($sv_post['action']) : '';
    $codes = isset($sv_post['codes']) && is_array($sv_post['codes']) ? array_map('intval', $sv_post['codes']) : array();
    
    if (!empty($codes)) {
        global $wpdb;
        $table = $wpdb->prefix . 'sv_codes';
        $ids = implode(',', $codes);
        
        switch ($action) {
            case 'activate':
                $wpdb->query("UPDATE {$table} SET status = 'active' WHERE id IN ({$ids})");
                echo '<div class="notice notice-success"><p>' . esc_html__('Codes activated.', 'serial-validator') . '</p></div>';
                break;
            case 'block':
                $wpdb->query("UPDATE {$table} SET status = 'blocked' WHERE id IN ({$ids})");
                echo '<div class="notice notice-success"><p>' . esc_html__('Codes blocked.', 'serial-validator') . '</p></div>';
                break;
            case 'delete':
                $wpdb->query("DELETE FROM {$table} WHERE id IN ({$ids})");
                echo '<div class="notice notice-success"><p>' . esc_html__('Codes deleted.', 'serial-validator') . '</p></div>';
                break;
            case 'block_batch':
                // Get unique batches from selected codes
                $batches = $wpdb->get_col("SELECT DISTINCT batch FROM {$table} WHERE id IN ({$ids}) AND batch != ''");
                if (!empty($batches)) {
                    $batch_placeholders = implode(',', array_fill(0, count($batches), '%s'));
                    $wpdb->query($wpdb->prepare("UPDATE {$table} SET status = 'blocked' WHERE batch IN ({$batch_placeholders})", $batches));
                    /* translators: %d is number of affected batches. */
                    echo '<div class="notice notice-success"><p>' . sprintf(esc_html__('All codes in %d batch(es) have been blocked.', 'serial-validator'), count($batches)) . '</p></div>';
                } else {
                    echo '<div class="notice notice-warning"><p>' . esc_html__('Selected codes have no batch assigned.', 'serial-validator') . '</p></div>';
                }
                break;
            case 'delete_batch':
                // Get unique batches from selected codes
                $batches = $wpdb->get_col("SELECT DISTINCT batch FROM {$table} WHERE id IN ({$ids}) AND batch != ''");
                if (!empty($batches)) {
                    $batch_placeholders = implode(',', array_fill(0, count($batches), '%s'));
                    $affected = $wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE batch IN ({$batch_placeholders})", $batches));
                    /* translators: 1: number of deleted codes, 2: number of affected batches. */
                    echo '<div class="notice notice-success"><p>' . sprintf(esc_html__('Deleted %1$d codes from %2$d batch(es).', 'serial-validator'), (int) $affected, count($batches)) . '</p></div>';
                } else {
                    echo '<div class="notice notice-warning"><p>' . esc_html__('Selected codes have no batch assigned.', 'serial-validator') . '</p></div>';
                }
                break;
        }
    }
}

// Handle single delete
if (isset($_GET['action']) && 'delete' === sanitize_text_field(wp_unslash($_GET['action'])) && isset($_GET['id'])) {
    check_admin_referer('sv_delete_code');
    
    global $wpdb;
    $table = $wpdb->prefix . 'sv_codes';
    $id = intval(wp_unslash($_GET['id']));
    
    $wpdb->delete($table, array('id' => $id));
    echo '<div class="notice notice-success"><p>' . esc_html__('Code deleted.', 'serial-validator') . '</p></div>';
}

// Handle add new code
if (isset($_POST['add_code'])) {
    check_admin_referer('sv_add_code');

    $sv_post = wp_unslash($_POST);
    
    global $wpdb;
    $table = $wpdb->prefix . 'sv_codes';
    
    $wpdb->insert($table, array(
        'code' => isset($_POST['code']) ? sanitize_text_field(wp_unslash($_POST['code'])) : '',
        'product_name' => isset($_POST['product_name']) ? sanitize_text_field(wp_unslash($_POST['product_name'])) : '',
        'batch' => isset($_POST['batch']) ? sanitize_text_field(wp_unslash($_POST['batch'])) : '',
        'expiry_date' => !empty($_POST['expiry_date']) ? sanitize_text_field(wp_unslash($_POST['expiry_date'])) : null,
        'warranty_months' => !empty($sv_post['warranty_months']) ? intval($sv_post['warranty_months']) : null,
        'status' => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'active',
        'one_time_use' => isset($_POST['one_time_use']) ? 1 : 0,
        'created_at' => current_time('mysql')
    ));
    
    echo '<div class="notice notice-success"><p>' . esc_html__('Code added successfully.', 'serial-validator') . '</p></div>';
}

// Create table instance
$table = new Serial_Validator_Codes_List_Table();
$table->prepare_items();
?>

<div class="wrap">
    <h1><?php esc_html_e('Serial Codes', 'serial-validator'); ?></h1>
    
    <div class="sv-add-code-form">
        <h2><?php esc_html_e('Add New Code', 'serial-validator'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('sv_add_code'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="code"><?php esc_html_e('Code', 'serial-validator'); ?> *</label></th>
                    <td><input type="text" id="code" name="code" required class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="product_name"><?php esc_html_e('Product Name', 'serial-validator'); ?> *</label></th>
                    <td><input type="text" id="product_name" name="product_name" required class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="batch"><?php esc_html_e('Batch', 'serial-validator'); ?></label></th>
                    <td><input type="text" id="batch" name="batch" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="expiry_date"><?php esc_html_e('Expiry Date', 'serial-validator'); ?></label></th>
                    <td><input type="date" id="expiry_date" name="expiry_date"></td>
                </tr>
                <tr>
                    <th><label for="warranty_months"><?php esc_html_e('Warranty (Months)', 'serial-validator'); ?></label></th>
                    <td><input type="number" id="warranty_months" name="warranty_months" min="0" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="status"><?php esc_html_e('Status', 'serial-validator'); ?></label></th>
                    <td>
                        <select id="status" name="status">
                            <option value="active"><?php esc_html_e('Active', 'serial-validator'); ?></option>
                            <option value="blocked"><?php esc_html_e('Blocked', 'serial-validator'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="one_time_use"><?php esc_html_e('One-Time Use', 'serial-validator'); ?></label></th>
                    <td><input type="checkbox" id="one_time_use" name="one_time_use" value="1" checked></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="add_code" class="button button-primary" value="<?php esc_attr_e('Add Code', 'serial-validator'); ?>">
            </p>
        </form>
    </div>
    
    <hr>
    
    <form method="get">
        <input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? esc_attr(sanitize_text_field(wp_unslash($_REQUEST['page']))) : ''; ?>">
        <?php
        $table->search_box(__('Search Codes', 'serial-validator'), 'search_id');
        ?>
    </form>
    
    <form method="post">
        <?php
        $table->display();
        ?>
    </form>
</div>
