<?php
/**
 * Settings page.
 *
 * @package Serial_Validator
 */

class Serial_Validator_Settings {

    /**
     * Option name.
     */
    private $option_name = 'serial_validator_settings';

    /**
     * Display settings page.
     */
    public function display() {
        // Handle form submission
        if (isset($_POST['submit'])) {
            check_admin_referer('sv_settings');
            $this->save_settings();
            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'serial-validator') . '</p></div>';
        }
        
        $settings = get_option($this->option_name, array());
        
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Serial Validator Settings', 'serial-validator'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('sv_settings'); ?>
                
                <div class="sv-settings-tabs">
                    <nav class="nav-tab-wrapper">
                        <a href="#form-settings" class="nav-tab nav-tab-active"><?php esc_html_e('Form Settings', 'serial-validator'); ?></a>
                        <a href="#verification-settings" class="nav-tab"><?php esc_html_e('Verification', 'serial-validator'); ?></a>
                        <a href="#security-settings" class="nav-tab"><?php esc_html_e('Security', 'serial-validator'); ?></a>
                        <a href="#messages-settings" class="nav-tab"><?php esc_html_e('Messages', 'serial-validator'); ?></a>
                        <a href="#leads-settings" class="nav-tab"><?php esc_html_e('Leads', 'serial-validator'); ?></a>
                    </nav>
                    
                    <div id="form-settings" class="sv-tab-content">
                        <h2><?php esc_html_e('Form Field Settings', 'serial-validator'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('Show Name Field', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="form_enable_name" value="1" <?php checked(!empty($settings['form_enable_name'])); ?>>
                                        <?php esc_html_e('Enable name field on verification form', 'serial-validator'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Show Email Field', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="form_enable_email" value="1" <?php checked(!empty($settings['form_enable_email'])); ?>>
                                        <?php esc_html_e('Enable email field on verification form', 'serial-validator'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Show Phone Field', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="form_enable_phone" value="1" <?php checked(!empty($settings['form_enable_phone'])); ?>>
                                        <?php esc_html_e('Enable phone field on verification form', 'serial-validator'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="verification-settings" class="sv-tab-content" style="display: none;">
                        <h2><?php esc_html_e('Verification Settings', 'serial-validator'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('One-Time Use', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_one_time_use" value="1" <?php checked(!empty($settings['enable_one_time_use'])); ?>>
                                        <?php esc_html_e('Enable one-time use by default for new codes', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('When enabled, codes can only be verified once successfully.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Allow Re-verification', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="allow_reverification" value="1" <?php checked(!empty($settings['allow_reverification'])); ?>>
                                        <?php esc_html_e('Allow users to re-check already verified codes', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Shows "Already Used" status instead of blocking re-verification.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="security-settings" class="sv-tab-content" style="display: none;">
                        <h2><?php esc_html_e('Security Settings', 'serial-validator'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('Rate Limiting', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="rate_limit_enabled" value="1" <?php checked(!empty($settings['rate_limit_enabled'])); ?>>
                                        <?php esc_html_e('Enable rate limiting', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Limit verification attempts per IP address.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Rate Limit Attempts', 'serial-validator'); ?></th>
                                <td>
                                    <input type="number" name="rate_limit_attempts" value="<?php echo esc_attr($settings['rate_limit_attempts'] ?? 5); ?>" min="1" max="100" class="small-text">
                                    <?php esc_html_e('attempts per hour', 'serial-validator'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Enable reCAPTCHA', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="recaptcha_enabled" value="1" <?php checked(!empty($settings['recaptcha_enabled'])); ?>>
                                        <?php esc_html_e('Enable Google reCAPTCHA v2', 'serial-validator'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('reCAPTCHA Site Key', 'serial-validator'); ?></th>
                                <td>
                                    <input type="text" name="recaptcha_site_key" value="<?php echo esc_attr($settings['recaptcha_site_key'] ?? ''); ?>" class="regular-text">
                                    <p class="description">
                                        <?php esc_html_e('Get your keys from', 'serial-validator'); ?>
                                        <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA</a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('reCAPTCHA Secret Key', 'serial-validator'); ?></th>
                                <td>
                                    <input type="text" name="recaptcha_secret_key" value="<?php echo esc_attr($settings['recaptcha_secret_key'] ?? ''); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="messages-settings" class="sv-tab-content" style="display: none;">
                        <h2><?php esc_html_e('Custom Messages', 'serial-validator'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('Valid Code Message', 'serial-validator'); ?></th>
                                <td>
                                    <textarea name="message_valid" rows="3" class="large-text"><?php echo esc_textarea($settings['message_valid'] ?? __('Authentic Product! This code is valid.', 'serial-validator')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Invalid Code Message', 'serial-validator'); ?></th>
                                <td>
                                    <textarea name="message_invalid" rows="3" class="large-text"><?php echo esc_textarea($settings['message_invalid'] ?? __('Code not found. Please check and try again.', 'serial-validator')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Already Used Message', 'serial-validator'); ?></th>
                                <td>
                                    <textarea name="message_used" rows="3" class="large-text"><?php echo esc_textarea($settings['message_used'] ?? __('This code has already been verified.', 'serial-validator')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Blocked Code Message', 'serial-validator'); ?></th>
                                <td>
                                    <textarea name="message_blocked" rows="3" class="large-text"><?php echo esc_textarea($settings['message_blocked'] ?? __('This code is not valid. Please contact support.', 'serial-validator')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Rate Limit Message', 'serial-validator'); ?></th>
                                <td>
                                    <textarea name="message_rate_limit" rows="3" class="large-text"><?php echo esc_textarea($settings['message_rate_limit'] ?? __('Too many attempts. Please try again later.', 'serial-validator')); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="leads-settings" class="sv-tab-content" style="display: none;">
                        <h2><?php esc_html_e('Lead Collection Settings', 'serial-validator'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('Create Leads', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="radio" name="lead_creation_rule" value="valid_only" <?php checked(($settings['lead_creation_rule'] ?? 'valid_only') === 'valid_only'); ?>>
                                        <?php esc_html_e('Only on valid verification', 'serial-validator'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="radio" name="lead_creation_rule" value="all" <?php checked(($settings['lead_creation_rule'] ?? 'valid_only') === 'all'); ?>>
                                        <?php esc_html_e('On every attempt', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Choose when to create lead records.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Name Field Required', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="lead_require_name" value="1" <?php checked(!empty($settings['lead_require_name'])); ?>>
                                        <?php esc_html_e('Make name field required for verification', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Users must enter their name to verify a code.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Email Field Required', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="lead_require_email" value="1" <?php checked(!empty($settings['lead_require_email'])); ?>>
                                        <?php esc_html_e('Make email field required for verification', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Users must enter their email to verify a code.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Phone Field Required', 'serial-validator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="lead_require_phone" value="1" <?php checked(!empty($settings['lead_require_phone'])); ?>>
                                        <?php esc_html_e('Make phone field required for verification', 'serial-validator'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Users must enter their phone number to verify a code.', 'serial-validator'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'serial-validator'); ?>">
                </p>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.sv-tab-content').hide();
                $($(this).attr('href')).show();
            });
        });
        </script>
        <?php
    }

    /**
     * Save settings.
     */
    private function save_settings() {
        // Nonce is verified by check_admin_referer() in the caller before this method is invoked.
        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $sv_raw = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $settings = array(
            'form_enable_name'      => isset( $sv_raw['form_enable_name'] ),
            'form_enable_email'     => isset( $sv_raw['form_enable_email'] ),
            'form_enable_phone'     => isset( $sv_raw['form_enable_phone'] ),
            'enable_one_time_use'   => isset( $sv_raw['enable_one_time_use'] ),
            'allow_reverification'  => isset( $sv_raw['allow_reverification'] ),
            'rate_limit_enabled'    => isset( $sv_raw['rate_limit_enabled'] ),
            'rate_limit_attempts'   => isset( $sv_raw['rate_limit_attempts'] ) ? intval( $sv_raw['rate_limit_attempts'] ) : 5,
            'rate_limit_duration'   => 3600,
            'recaptcha_enabled'     => isset( $sv_raw['recaptcha_enabled'] ),
            'recaptcha_site_key'    => sanitize_text_field( $sv_raw['recaptcha_site_key'] ?? '' ),
            'recaptcha_secret_key'  => sanitize_text_field( $sv_raw['recaptcha_secret_key'] ?? '' ),
            'lead_creation_rule'    => sanitize_text_field( $sv_raw['lead_creation_rule'] ?? 'valid_only' ),
            'lead_require_name'     => isset( $sv_raw['lead_require_name'] ),
            'lead_require_email'    => isset( $sv_raw['lead_require_email'] ),
            'lead_require_phone'    => isset( $sv_raw['lead_require_phone'] ),
            'message_valid'         => sanitize_textarea_field( $sv_raw['message_valid'] ?? '' ),
            'message_invalid'       => sanitize_textarea_field( $sv_raw['message_invalid'] ?? '' ),
            'message_used'          => sanitize_textarea_field( $sv_raw['message_used'] ?? '' ),
            'message_blocked'       => sanitize_textarea_field( $sv_raw['message_blocked'] ?? '' ),
            'message_rate_limit'    => sanitize_textarea_field( $sv_raw['message_rate_limit'] ?? '' ),
        );
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        update_option( $this->option_name, $settings );
    }
}
