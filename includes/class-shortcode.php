<?php
/**
 * Shortcode functionality.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Shortcode {

    /**
     * Register shortcode.
     */
    public function register_shortcode() {
        add_shortcode('serial_validator', array($this, 'render_shortcode'));
    }

    /**
     * Render shortcode output.
     */
    public function render_shortcode($atts = array()) {
        $settings = get_option('serial_validator_settings', array());
        
        ob_start();
        ?>
        <div class="sv-verification-form-wrapper">
            <form id="sv-verification-form" class="sv-verification-form">
                <div class="sv-form-field">
                    <label for="sv-code"><?php esc_html_e('Serial Code', 'serial-validator'); ?> <span class="sv-required">*</span></label>
                    <input type="text" id="sv-code" name="code" required placeholder="<?php esc_attr_e('Enter your serial code', 'serial-validator'); ?>">
                </div>
                
                <?php if (!empty($settings['form_enable_name'])): ?>
                <div class="sv-form-field">
                    <label for="sv-name">
                        <?php esc_html_e('Name', 'serial-validator'); ?>
                        <?php if (!empty($settings['lead_require_name'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="sv-name" name="name" placeholder="<?php esc_attr_e('Your name', 'serial-validator'); ?>" <?php echo !empty($settings['lead_require_name']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($settings['form_enable_email'])): ?>
                <div class="sv-form-field">
                    <label for="sv-email">
                        <?php esc_html_e('Email', 'serial-validator'); ?>
                        <?php if (!empty($settings['lead_require_email'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="email" id="sv-email" name="email" placeholder="<?php esc_attr_e('Your email', 'serial-validator'); ?>" <?php echo !empty($settings['lead_require_email']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($settings['form_enable_phone'])): ?>
                <div class="sv-form-field">
                    <label for="sv-phone">
                        <?php esc_html_e('Phone', 'serial-validator'); ?>
                        <?php if (!empty($settings['lead_require_phone'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="tel" id="sv-phone" name="phone" placeholder="<?php esc_attr_e('Your phone number', 'serial-validator'); ?>" <?php echo !empty($settings['lead_require_phone']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key'])): ?>
                <div class="sv-form-field">
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($settings['recaptcha_site_key']); ?>"></div>
                </div>
                <?php endif; ?>
                
                <div class="sv-form-field">
                    <button type="submit" class="sv-submit-btn"><?php esc_html_e('Verify Code', 'serial-validator'); ?></button>
                </div>
            </form>
            
            <div id="sv-result" class="sv-result" style="display: none;"></div>
            <div id="sv-loading" class="sv-loading" style="display: none;">
                <span class="sv-spinner"></span>
                <?php esc_html_e('Verifying...', 'serial-validator'); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
