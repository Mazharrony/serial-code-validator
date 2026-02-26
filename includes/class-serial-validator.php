<?php
/**
 * Core plugin class.
 *
 * @package Serial_Validator
 */

class Serial_Validator {

    /**
     * Plugin loader.
     */
    protected $loader;
    
    /**
     * Plugin version.
     */
    protected $version;

    /**
     * Initialize the plugin.
     */
    public function __construct() {
        $this->version = SERIAL_VALIDATOR_VERSION;
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load required dependencies.
     */
    private function load_dependencies() {
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-loader.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-database.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-ajax-handler.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-shortcode.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'includes/class-privacy.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-admin.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'admin/class-csv-handler.php';
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'public/class-public.php';
        
        $this->loader = new Serial_Validator_Loader();
    }

    /**
     * Load plugin text domain for internationalization.
     */
    private function set_locale() {
        $this->loader->add_action('plugins_loaded', $this, 'load_plugin_textdomain');
    }

    /**
     * Register admin hooks.
     */
    private function define_admin_hooks() {
        $admin = new Serial_Validator_Admin($this->version);
        
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $admin, 'add_admin_menu');
    }

    /**
     * Register public-facing hooks.
     */
    private function define_public_hooks() {
        $public = new Serial_Validator_Public($this->version);
        
        $this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_scripts');
        
        // Register shortcode.
        $shortcode = new Serial_Validator_Shortcode();
        $this->loader->add_action('init', $shortcode, 'register_shortcode');
        
        // Register AJAX handlers.
        $ajax = new Serial_Validator_Ajax_Handler();
        $this->loader->add_action('wp_ajax_sv_verify_code', $ajax, 'verify_code');
        $this->loader->add_action('wp_ajax_nopriv_sv_verify_code', $ajax, 'verify_code');
        
        // Register privacy hooks.
        $privacy = new Serial_Validator_Privacy();
        $this->loader->add_action('admin_init', $privacy, 'add_privacy_policy');
        $this->loader->add_filter('wp_privacy_personal_data_exporters', $privacy, 'register_exporter');
        $this->loader->add_filter('wp_privacy_personal_data_erasers', $privacy, 'register_eraser');
        
        // Load Elementor widget on elementor/widgets/register action
        $this->loader->add_action('elementor/widgets/register', $this, 'register_elementor_widget');
    }
    
    /**
     * Register Elementor widget.
     */
    public function register_elementor_widget($widgets_manager) {
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        require_once SERIAL_VALIDATOR_PLUGIN_DIR . 'widgets/class-elementor-widget.php';
        
        if (class_exists('Serial_Validator_Elementor_Widget')) {
            $widgets_manager->register(new Serial_Validator_Elementor_Widget());
        }
    }

    /**
     * Run the loader.
     */
    public function run() {
        $this->loader->run();
    }
    
    /**
     * Load plugin text domain.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'serial-validator',
            false,
            dirname(SERIAL_VALIDATOR_PLUGIN_BASENAME) . '/languages/'
        );
    }
}
