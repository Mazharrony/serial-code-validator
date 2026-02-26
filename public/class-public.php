<?php
/**
 * Public-facing functionality.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Public {

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
     * Enqueue public styles.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'serial-validator-public',
            SERIAL_VALIDATOR_PLUGIN_URL . 'assets/css/public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue public scripts.
     */
    public function enqueue_scripts() {
        $settings = get_option('serial_validator_settings', array());
        
        // Enqueue reCAPTCHA if enabled.
        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key'])) {
            wp_enqueue_script(
                'google-recaptcha',
                'https://www.google.com/recaptcha/api.js',
                array(),
                null,
                true
            );
        }
        
        wp_enqueue_script(
            'serial-validator-public',
            SERIAL_VALIDATOR_PLUGIN_URL . 'assets/js/verification-form.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // Localize script with data.
        wp_localize_script('serial-validator-public', 'svData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sv_verify_nonce'),
            'recaptchaEnabled' => !empty($settings['recaptcha_enabled'])
        ));
    }
}
