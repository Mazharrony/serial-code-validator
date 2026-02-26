<?php
/**
 * Admin view for bulk code generation.
 */
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- view partial, variables are local scope

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['sv_generate_codes']) && check_admin_referer('sv_generate_codes_nonce', 'sv_generate_codes_nonce_field')) {
    $sv_post = wp_unslash($_POST);

    $params = array(
        'quantity' => isset($sv_post['quantity']) ? intval($sv_post['quantity']) : 0,
        'product_name' => isset($sv_post['product_name']) ? sanitize_text_field($sv_post['product_name']) : '',
        'batch' => isset($sv_post['batch']) ? sanitize_text_field($sv_post['batch']) : '',
        'expiry_date' => isset($sv_post['expiry_date']) ? sanitize_text_field($sv_post['expiry_date']) : '',
        'warranty_months' => isset($sv_post['warranty_months']) ? sanitize_text_field($sv_post['warranty_months']) : '',
        'status' => isset($sv_post['status']) ? sanitize_text_field($sv_post['status']) : 'active',
        'one_time_use' => isset($sv_post['one_time_use']) ? 1 : 0,
        'code_length' => isset($sv_post['code_length']) ? intval($sv_post['code_length']) : 12,
        'code_prefix' => isset($sv_post['code_prefix']) ? sanitize_text_field($sv_post['code_prefix']) : '',
        'code_suffix' => isset($sv_post['code_suffix']) ? sanitize_text_field($sv_post['code_suffix']) : '',
        'code_format' => isset($sv_post['code_format']) ? sanitize_text_field($sv_post['code_format']) : 'alphanumeric'
    );
    
    $result = Serial_Validator_CSV_Handler::generate_codes($params);
    
    if ($result['success']) {
        // Option to download immediately
        if (isset($sv_post['download']) && $sv_post['download'] === '1') {
            Serial_Validator_CSV_Handler::download_generated_codes($result['codes']);
        } else {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($result['message']) . '</p></div>';
        }
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($result['message']) . '</p></div>';
    }
}
?>

<div class="wrap sv-generate-codes">
    <h1><?php esc_html_e('Generate Codes', 'serial-validator'); ?></h1>
    
    <div class="sv-generate-container">
        <form method="post" action="" id="sv-generate-form">
            <?php wp_nonce_field('sv_generate_codes_nonce', 'sv_generate_codes_nonce_field'); ?>
            <input type="hidden" name="download" id="download-field" value="0">
            
            <div class="sv-generate-sections">
                <!-- Generation Settings Section -->
                <div class="sv-generate-section">
                    <h2><?php esc_html_e('Generation Settings', 'serial-validator'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="quantity"><?php esc_html_e('Quantity', 'serial-validator'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="number" name="quantity" id="quantity" class="regular-text" min="1" max="10000" value="100" required>
                                <p class="description"><?php esc_html_e('Number of codes to generate (1-10,000)', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="code_format"><?php esc_html_e('Code Format', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <select name="code_format" id="code_format" class="regular-text">
                                    <option value="alphanumeric"><?php esc_html_e('Alphanumeric (A-Z, 0-9)', 'serial-validator'); ?></option>
                                    <option value="numeric"><?php esc_html_e('Numeric (0-9)', 'serial-validator'); ?></option>
                                    <option value="alphabetic"><?php esc_html_e('Alphabetic (A-Z)', 'serial-validator'); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="code_length"><?php esc_html_e('Code Length', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="code_length" id="code_length" class="regular-text" min="6" max="20" value="12">
                                <p class="description"><?php esc_html_e('Total length including prefix and suffix (6-20 characters)', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="code_prefix"><?php esc_html_e('Prefix', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="code_prefix" id="code_prefix" class="regular-text" maxlength="5" placeholder="e.g., SN-">
                                <p class="description"><?php esc_html_e('Optional prefix for all codes (max 5 characters)', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="code_suffix"><?php esc_html_e('Suffix', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="code_suffix" id="code_suffix" class="regular-text" maxlength="5" placeholder="e.g., -2024">
                                <p class="description"><?php esc_html_e('Optional suffix for all codes (max 5 characters)', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Code Properties Section -->
                <div class="sv-generate-section">
                    <h2><?php esc_html_e('Code Properties', 'serial-validator'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="product_name"><?php esc_html_e('Product Name', 'serial-validator'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" name="product_name" id="product_name" class="regular-text" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="batch"><?php esc_html_e('Batch Number', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="batch" id="batch" class="regular-text" placeholder="e.g., BATCH-2024-01">
                                <p class="description"><?php esc_html_e('Optional batch identifier for tracking', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="expiry_date"><?php esc_html_e('Expiry Date', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="date" name="expiry_date" id="expiry_date" class="regular-text">
                                <p class="description"><?php esc_html_e('Optional expiry date for codes', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="warranty_months"><?php esc_html_e('Warranty (Months)', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="warranty_months" id="warranty_months" class="regular-text" min="0" max="120" placeholder="e.g., 12">
                                <p class="description"><?php esc_html_e('Warranty period in months (max 120)', 'serial-validator'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="status"><?php esc_html_e('Status', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <select name="status" id="status" class="regular-text">
                                    <option value="active"><?php esc_html_e('Active', 'serial-validator'); ?></option>
                                    <option value="blocked"><?php esc_html_e('Blocked', 'serial-validator'); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="one_time_use"><?php esc_html_e('One-Time Use', 'serial-validator'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="one_time_use" id="one_time_use" value="1" checked>
                                    <?php esc_html_e('Codes can only be verified once', 'serial-validator'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="sv-generate-section sv-preview-section">
                <h2><?php esc_html_e('Code Preview', 'serial-validator'); ?></h2>
                <div class="sv-code-preview">
                    <p class="description"><?php esc_html_e('Sample codes based on your settings:', 'serial-validator'); ?></p>
                    <div id="preview-codes" class="preview-codes">
                        <code>SAMPLE12CODE</code>
                        <code>SAMPLE12CODE</code>
                        <code>SAMPLE12CODE</code>
                        <code>SAMPLE12CODE</code>
                        <code>SAMPLE12CODE</code>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <p class="submit">
                <button type="button" class="button" id="preview-btn">
                    <?php esc_html_e('Update Preview', 'serial-validator'); ?>
                </button>
                <button type="submit" name="sv_generate_codes" class="button button-primary" id="generate-btn">
                    <?php esc_html_e('Generate Codes', 'serial-validator'); ?>
                </button>
                <button type="button" class="button button-primary" id="generate-download-btn">
                    <?php esc_html_e('Generate & Download CSV', 'serial-validator'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.sv-generate-codes .sv-generate-container {
    max-width: 1200px;
    margin: 20px 0;
}

.sv-generate-codes .sv-generate-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.sv-generate-codes .sv-generate-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.sv-generate-codes .sv-preview-section {
    grid-column: 1 / -1;
}

.sv-generate-codes .sv-generate-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #dcdcde;
    font-size: 16px;
}

.sv-generate-codes .form-table th {
    font-weight: 600;
}

.sv-generate-codes .required {
    color: #d63638;
}

.sv-generate-codes .sv-code-preview {
    padding: 15px;
    background: #f6f7f7;
    border-radius: 4px;
}

.sv-generate-codes .preview-codes {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.sv-generate-codes .preview-codes code {
    display: inline-block;
    padding: 8px 12px;
    background: #2271b1;
    color: #fff;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 1px;
}

@media (max-width: 782px) {
    .sv-generate-codes .sv-generate-sections {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Generate preview codes
    function generatePreview() {
        const format = $('#code_format').val();
        const length = parseInt($('#code_length').val()) || 12;
        const prefix = $('#code_prefix').val().toUpperCase();
        const suffix = $('#code_suffix').val().toUpperCase();
        
        let characters;
        switch(format) {
            case 'numeric':
                characters = '0123456789';
                break;
            case 'alphabetic':
                characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        const actualLength = Math.max(4, length - prefix.length - suffix.length);
        const previewCodes = [];
        
        for (let i = 0; i < 5; i++) {
            let code = '';
            for (let j = 0; j < actualLength; j++) {
                code += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            previewCodes.push(prefix + code + suffix);
        }
        
        $('#preview-codes').html(
            previewCodes.map(code => '<code>' + code + '</code>').join('')
        );
    }
    
    // Update preview on input change
    $('#code_format, #code_length, #code_prefix, #code_suffix').on('input change', generatePreview);
    
    // Preview button
    $('#preview-btn').on('click', function(e) {
        e.preventDefault();
        generatePreview();
    });
    
    // Generate & Download button
    $('#generate-download-btn').on('click', function(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!$('#product_name').val()) {
            alert('<?php esc_html_e('Please enter a product name.', 'serial-validator'); ?>');
            $('#product_name').focus();
            return;
        }
        
        const quantity = parseInt($('#quantity').val());
        if (!quantity || quantity < 1 || quantity > 10000) {
            alert('<?php esc_html_e('Please enter a valid quantity (1-10,000).', 'serial-validator'); ?>');
            $('#quantity').focus();
            return;
        }
        
        // Set download flag and submit
        $('#download-field').val('1');
        $('#sv-generate-form').submit();
    });
    
    // Regular generate button
    $('#generate-btn').on('click', function(e) {
        // Validate required fields
        if (!$('#product_name').val()) {
            e.preventDefault();
            alert('<?php esc_html_e('Please enter a product name.', 'serial-validator'); ?>');
            $('#product_name').focus();
            return;
        }
        
        const quantity = parseInt($('#quantity').val());
        if (!quantity || quantity < 1 || quantity > 10000) {
            e.preventDefault();
            alert('<?php esc_html_e('Please enter a valid quantity (1-10,000).', 'serial-validator'); ?>');
            $('#quantity').focus();
            return;
        }
        
        // Set download flag to 0
        $('#download-field').val('0');
    });
    
    // Initialize preview
    generatePreview();
});
</script>

