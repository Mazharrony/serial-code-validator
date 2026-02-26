<?php
/**
 * Dashboard view.
 *
 * @package Serial_Validator
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Get statistics
$stats_7 = Serial_Validator_Database::get_stats(7);
$stats_30 = Serial_Validator_Database::get_stats(30);
$daily_counts = Serial_Validator_Database::get_daily_counts(7);

// Prepare chart data
$chart_labels = array();
$chart_data = array();

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chart_labels[] = date('M j', strtotime($date));
    
    $count = 0;
    foreach ($daily_counts as $daily) {
        if ($daily->date === $date) {
            $count = $daily->count;
            break;
        }
    }
    $chart_data[] = $count;
}
?>

<div class="wrap sv-dashboard">
    <h1><?php esc_html_e('Serial Validator Dashboard', 'serial-validator'); ?></h1>
    
    <div class="sv-stats-period-toggle">
        <button class="button sv-period-btn active" data-period="7"><?php esc_html_e('Last 7 Days', 'serial-validator'); ?></button>
        <button class="button sv-period-btn" data-period="30"><?php esc_html_e('Last 30 Days', 'serial-validator'); ?></button>
    </div>
    
    <div class="sv-stats-cards" data-stats-7='<?php echo esc_attr(json_encode($stats_7)); ?>' data-stats-30='<?php echo esc_attr(json_encode($stats_30)); ?>'>
        <div class="sv-stat-card sv-stat-total">
            <div class="sv-stat-icon">📊</div>
            <div class="sv-stat-content">
                <div class="sv-stat-value"><?php echo esc_html($stats_7['total']); ?></div>
                <div class="sv-stat-label"><?php esc_html_e('Total Verifications', 'serial-validator'); ?></div>
            </div>
        </div>
        
        <div class="sv-stat-card sv-stat-valid">
            <div class="sv-stat-icon">✓</div>
            <div class="sv-stat-content">
                <div class="sv-stat-value"><?php echo esc_html($stats_7['valid']); ?></div>
                <div class="sv-stat-label"><?php esc_html_e('Valid Codes', 'serial-validator'); ?></div>
            </div>
        </div>
        
        <div class="sv-stat-card sv-stat-invalid">
            <div class="sv-stat-icon">✗</div>
            <div class="sv-stat-content">
                <div class="sv-stat-value"><?php echo esc_html($stats_7['invalid']); ?></div>
                <div class="sv-stat-label"><?php esc_html_e('Invalid Attempts', 'serial-validator'); ?></div>
            </div>
        </div>
        
        <div class="sv-stat-card sv-stat-used">
            <div class="sv-stat-icon">⚠</div>
            <div class="sv-stat-content">
                <div class="sv-stat-value"><?php echo esc_html($stats_7['used']); ?></div>
                <div class="sv-stat-label"><?php esc_html_e('Already Used', 'serial-validator'); ?></div>
            </div>
        </div>
        
        <div class="sv-stat-card sv-stat-blocked">
            <div class="sv-stat-icon">🚫</div>
            <div class="sv-stat-content">
                <div class="sv-stat-value"><?php echo esc_html($stats_7['blocked']); ?></div>
                <div class="sv-stat-label"><?php esc_html_e('Blocked Codes', 'serial-validator'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="sv-chart-container">
        <h2><?php esc_html_e('Verification Trend', 'serial-validator'); ?></h2>
        <canvas id="sv-verification-chart" 
                data-labels='<?php echo esc_attr(json_encode($chart_labels)); ?>' 
                data-values='<?php echo esc_attr(json_encode($chart_data)); ?>'></canvas>
    </div>
    
    <div class="sv-quick-links">
        <h2><?php esc_html_e('Quick Links', 'serial-validator'); ?></h2>
        <div class="sv-links-grid">
            <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-codes')); ?>" class="sv-quick-link">
                <span class="dashicons dashicons-list-view"></span>
                <?php esc_html_e('Manage Codes', 'serial-validator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-import')); ?>" class="sv-quick-link">
                <span class="dashicons dashicons-upload"></span>
                <?php esc_html_e('Import Codes', 'serial-validator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-leads')); ?>" class="sv-quick-link">
                <span class="dashicons dashicons-groups"></span>
                <?php esc_html_e('View Leads', 'serial-validator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-settings')); ?>" class="sv-quick-link">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php esc_html_e('Settings', 'serial-validator'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=serial-validator-guide')); ?>" class="sv-quick-link">
                <span class="dashicons dashicons-book-alt"></span>
                <?php esc_html_e('User Guide', 'serial-validator'); ?>
            </a>
        </div>
    </div>
    
    <div class="sv-usage-info">
        <h2><?php esc_html_e('Usage', 'serial-validator'); ?></h2>
        <div class="sv-info-box">
            <h3><?php esc_html_e('Shortcode', 'serial-validator'); ?></h3>
            <code>[serial_validator]</code>
            <p><?php esc_html_e('Add this shortcode to any page or post to display the verification form.', 'serial-validator'); ?></p>
        </div>
        
        <div class="sv-info-box">
            <h3><?php esc_html_e('QR Code URL', 'serial-validator'); ?></h3>
            <code><?php echo esc_html(home_url('/verify/?code=XXXX')); ?></code>
            <p><?php esc_html_e('Generate QR codes pointing to this URL format. Replace XXXX with the serial code.', 'serial-validator'); ?></p>
        </div>
        
        <?php if (did_action('elementor/loaded')): ?>
        <div class="sv-info-box">
            <h3><?php esc_html_e('Elementor Widget', 'serial-validator'); ?></h3>
            <p><?php esc_html_e('Search for "Serial Validator" in the Elementor widget panel to add the verification form.', 'serial-validator'); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
