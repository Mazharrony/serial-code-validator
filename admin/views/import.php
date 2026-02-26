<?php
/**
 * Import codes view.
 *
 * @package Serial_Validator
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Handle CSV import
$import_result = null;
if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
    check_admin_referer('sv_import_csv');
    
    $import_result = Serial_Validator_CSV_Handler::import_codes($_FILES['csv_file']);
}

// Handle CSV export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    Serial_Validator_CSV_Handler::export_codes();
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Import Serial Codes', 'serial-validator'); ?></h1>
    
    <?php if ($import_result): ?>
        <?php if ($import_result['success']): ?>
            <div class="notice notice-success">
                <p><?php echo esc_html($import_result['message']); ?></p>
                <?php if (!empty($import_result['errors'])): ?>
                    <details>
                        <summary><?php esc_html_e('View Errors', 'serial-validator'); ?></summary>
                        <ul>
                            <?php foreach ($import_result['errors'] as $error): ?>
                                <li><?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($import_result['message']); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="sv-import-container">
        <div class="sv-import-instructions">
            <h2><?php esc_html_e('CSV Import Instructions', 'serial-validator'); ?></h2>
            
            <p><?php esc_html_e('Upload a CSV file to import multiple serial codes at once.', 'serial-validator'); ?></p>
            
            <h3><?php esc_html_e('Required Columns', 'serial-validator'); ?></h3>
            <ul>
                <li><strong>code</strong> - <?php esc_html_e('The serial code (required, must be unique)', 'serial-validator'); ?></li>
                <li><strong>product_name</strong> - <?php esc_html_e('Product name (required)', 'serial-validator'); ?></li>
            </ul>
            
            <h3><?php esc_html_e('Optional Columns', 'serial-validator'); ?></h3>
            <ul>
                <li><strong>batch</strong> - <?php esc_html_e('Batch number or identifier', 'serial-validator'); ?></li>
                <li><strong>expiry_date</strong> - <?php esc_html_e('Expiry date (format: YYYY-MM-DD)', 'serial-validator'); ?></li>
                <li><strong>warranty_months</strong> - <?php esc_html_e('Warranty duration in months', 'serial-validator'); ?></li>
                <li><strong>status</strong> - <?php esc_html_e('active or blocked (default: active)', 'serial-validator'); ?></li>
            </ul>
            
            <h3><?php esc_html_e('Example CSV Format', 'serial-validator'); ?></h3>
            <pre class="sv-csv-example">code,product_name,batch,expiry_date,warranty_months,status
ABC123,Product A,BATCH-001,2026-12-31,12,active
XYZ789,Product B,BATCH-002,2027-06-30,24,active
DEF456,Product C,BATCH-001,,6,active</pre>
            
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-import&action=export')); ?>" class="button">
                    <?php esc_html_e('Download Sample CSV', 'serial-validator'); ?>
                </a>
            </p>
        </div>
        
        <div class="sv-import-form">
            <h2><?php esc_html_e('Upload CSV File', 'serial-validator'); ?></h2>
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('sv_import_csv'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="csv_file"><?php esc_html_e('CSV File', 'serial-validator'); ?></label></th>
                        <td>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                            <p class="description"><?php esc_html_e('Select a CSV file to import.', 'serial-validator'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="import_csv" class="button button-primary" value="<?php esc_attr_e('Import Codes', 'serial-validator'); ?>">
                </p>
            </form>
        </div>
    </div>
</div>
