<?php
/**
 * Admin functionality.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Admin {

    /**
     * Plugin version.
     */
    private $version;

    /**
     * Initialize the class.
     */
    public function __construct($version) {
        $this->version = $version;
    }

    /**
     * Enqueue admin styles.
     */
    public function enqueue_styles($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'serial-validator') === false) {
            return;
        }
        
        wp_enqueue_style(
            'serial-validator-admin',
            SERIAL_VALIDATOR_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts.
     */
    public function enqueue_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'serial-validator') === false) {
            return;
        }
        
        // Enqueue Chart.js for dashboard (local copy for WordPress.org compliance)
        if (strpos($hook, 'serial-validator') !== false) {
            wp_enqueue_script(
                'chart-js',
                SERIAL_VALIDATOR_PLUGIN_URL . 'assets/js/chart.min.js',
                array(),
                '3.9.1',
                true
            );
        }
        
        wp_enqueue_script(
            'serial-validator-admin',
            SERIAL_VALIDATOR_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'chart-js'),
            $this->version,
            true
        );
        
        wp_localize_script('serial-validator-admin', 'svAdminData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sv_admin_nonce')
        ));
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Serial Validator', 'serial-validator'),
            __('Serial Validator', 'serial-validator'),
            'manage_options',
            'serial-validator',
            array($this, 'display_dashboard'),
            'dashicons-lock',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'serial-validator',
            __('Dashboard', 'serial-validator'),
            __('Dashboard', 'serial-validator'),
            'manage_options',
            'serial-validator',
            array($this, 'display_dashboard')
        );
        
        // Codes submenu
        add_submenu_page(
            'serial-validator',
            __('Codes', 'serial-validator'),
            __('Codes', 'serial-validator'),
            'manage_options',
            'serial-validator-codes',
            array($this, 'display_codes')
        );
        
        // Import submenu
        add_submenu_page(
            'serial-validator',
            __('Import Codes', 'serial-validator'),
            __('Import Codes', 'serial-validator'),
            'manage_options',
            'serial-validator-import',
            array($this, 'display_import')
        );
        
        // Generate submenu
        add_submenu_page(
            'serial-validator',
            __('Generate Codes', 'serial-validator'),
            __('Generate Codes', 'serial-validator'),
            'manage_options',
            'serial-validator-generate',
            array($this, 'display_generate')
        );
        
        // Leads submenu
        add_submenu_page(
            'serial-validator',
            __('Leads', 'serial-validator'),
            __('Leads', 'serial-validator'),
            'manage_options',
            'serial-validator-leads',
            array($this, 'display_leads')
        );
        
        // Settings submenu
        add_submenu_page(
            'serial-validator',
            __('Settings', 'serial-validator'),
            __('Settings', 'serial-validator'),
            'manage_options',
            'serial-validator-settings',
            array($this, 'display_settings')
        );

        // User guide submenu
        add_submenu_page(
            'serial-validator',
            __('User Guide', 'serial-validator'),
            __('User Guide', 'serial-validator'),
            'manage_options',
            'serial-validator-guide',
            array($this, 'display_user_guide')
        );
    }

    /**
     * Display dashboard page.
     */
    public function display_dashboard() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Display codes page.
     */
    public function display_codes() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-codes-list-table.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/codes.php';
    }

    /**
     * Display import page.
     */
    public function display_import() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-csv-handler.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/import.php';
    }
    
    /**
     * Display generate page.
     */
    public function display_generate() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-csv-handler.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/generate.php';
    }

    /**
     * Display leads page.
     */
    public function display_leads() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-leads-list-table.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/leads.php';
    }

    /**
     * Display settings page.
     */
    public function display_settings() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-settings.php';
        $settings_page = new Serial_Validator_Settings();
        $settings_page->display();
    }

    /**
     * Display user guide page.
     */
    public function display_user_guide() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/views/user-guide.php';
    }
}
