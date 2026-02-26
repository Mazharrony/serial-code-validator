<?php
/**
 * Elementor Widget for Serial Validator.
 *
 * @package Serial_Validator
 */

// Check if Elementor is loaded
if (!did_action('elementor/loaded')) {
    return;
}

class Serial_Validator_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'serial-validator';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return __('Serial Validator', 'serial-validator');
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-lock-user';
    }

    /**
     * Get widget categories.
     */
    public function get_categories() {
        return ['general'];
    }

    /**
     * Get widget keywords.
     */
    public function get_keywords() {
        return ['serial', 'validator', 'verification', 'code'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        
        // Content Section - Form Fields
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Form Fields', 'serial-validator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_name_field',
            [
                'label' => __('Show Name Field', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'serial-validator'),
                'label_off' => __('No', 'serial-validator'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_email_field',
            [
                'label' => __('Show Email Field', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'serial-validator'),
                'label_off' => __('No', 'serial-validator'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_phone_field',
            [
                'label' => __('Show Phone Field', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'serial-validator'),
                'label_off' => __('No', 'serial-validator'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        // Style Section - Button
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => __('Button Style', 'serial-validator'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Button Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2271b1',
                'selectors' => [
                    '{{WRAPPER}} .sv-submit-btn' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Button Hover Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#135e96',
                'selectors' => [
                    '{{WRAPPER}} .sv-submit-btn:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Button Text Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sv-submit-btn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sv-submit-btn' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sv-submit-btn',
            ]
        );

        $this->end_controls_section();

        // Style Section - Form
        $this->start_controls_section(
            'form_style_section',
            [
                'label' => __('Form Style', 'serial-validator'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_background',
            [
                'label' => __('Form Background', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sv-verification-form' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_border_color',
            [
                'label' => __('Form Border Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .sv-verification-form' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_border_radius',
            [
                'label' => __('Input Border Radius', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sv-form-field input' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Messages
        $this->start_controls_section(
            'messages_style_section',
            [
                'label' => __('Messages Style', 'serial-validator'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'success_color',
            [
                'label' => __('Success Message Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#00a32a',
                'selectors' => [
                    '{{WRAPPER}} .sv-result.sv-success' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'error_color',
            [
                'label' => __('Error Message Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#d63638',
                'selectors' => [
                    '{{WRAPPER}} .sv-result.sv-error' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'warning_color',
            [
                'label' => __('Warning Message Color', 'serial-validator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#996800',
                'selectors' => [
                    '{{WRAPPER}} .sv-result.sv-warning' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $plugin_settings = get_option('serial_validator_settings', array());
        
        // Override plugin settings with widget settings
        $show_name = ($settings['show_name_field'] === 'yes');
        $show_email = ($settings['show_email_field'] === 'yes');
        $show_phone = ($settings['show_phone_field'] === 'yes');
        
        ?>
        <div class="sv-verification-form-wrapper">
            <form id="sv-verification-form" class="sv-verification-form">
                <div class="sv-form-field">
                    <label for="sv-code"><?php esc_html_e('Serial Code', 'serial-validator'); ?> <span class="sv-required">*</span></label>
                    <input type="text" id="sv-code" name="code" required placeholder="<?php esc_attr_e('Enter your serial code', 'serial-validator'); ?>">
                </div>
                
                <?php if ($show_name): ?>
                <div class="sv-form-field">
                    <label for="sv-name">
                        <?php esc_html_e('Name', 'serial-validator'); ?>
                        <?php if (!empty($plugin_settings['lead_require_name'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="sv-name" name="name" placeholder="<?php esc_attr_e('Your name', 'serial-validator'); ?>" <?php echo !empty($plugin_settings['lead_require_name']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if ($show_email): ?>
                <div class="sv-form-field">
                    <label for="sv-email">
                        <?php esc_html_e('Email', 'serial-validator'); ?>
                        <?php if (!empty($plugin_settings['lead_require_email'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="email" id="sv-email" name="email" placeholder="<?php esc_attr_e('Your email', 'serial-validator'); ?>" <?php echo !empty($plugin_settings['lead_require_email']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if ($show_phone): ?>
                <div class="sv-form-field">
                    <label for="sv-phone">
                        <?php esc_html_e('Phone', 'serial-validator'); ?>
                        <?php if (!empty($plugin_settings['lead_require_phone'])): ?>
                            <span class="sv-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="tel" id="sv-phone" name="phone" placeholder="<?php esc_attr_e('Your phone number', 'serial-validator'); ?>" <?php echo !empty($plugin_settings['lead_require_phone']) ? 'required' : ''; ?>>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($plugin_settings['recaptcha_enabled']) && !empty($plugin_settings['recaptcha_site_key'])): ?>
                <div class="sv-form-field">
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($plugin_settings['recaptcha_site_key']); ?>"></div>
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
    }
}
