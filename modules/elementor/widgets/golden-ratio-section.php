<?php
/**
 * Fibonacci Golden Ratio Section Widget
 * Automatically creates perfectly proportioned sections using Phi (φ)
 */

if (!defined('ABSPATH')) exit;

class Kolibri_Fibonacci_Golden_Ratio_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'fibonacci-golden-ratio-section';
    }
    
    public function get_title() {
        return esc_html__('Golden Ratio Section', 'kolibri-fibonacci-mcp');
    }
    
    public function get_icon() {
        return 'eicon-section';
    }
    
    public function get_categories() {
        return ['fibonacci-ai'];
    }
    
    public function get_keywords() {
        return ['golden', 'ratio', 'phi', 'fibonacci', 'section', 'layout'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Golden Ratio Settings', 'kolibri-fibonacci-mcp'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'section_layout',
            [
                'label' => esc_html__('Layout Type', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '61_38',
                'options' => [
                    '61_38' => esc_html__('61.8% / 38.2% (Classic Golden Ratio)', 'kolibri-fibonacci-mcp'),
                    '38_61' => esc_html__('38.2% / 61.8% (Reverse)', 'kolibri-fibonacci-mcp'),
                    'single_618' => esc_html__('Single Column (61.8% width)', 'kolibri-fibonacci-mcp'),
                    'fibonacci_sequence' => esc_html__('Fibonacci Sequence (3 columns)', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'content_alignment',
            [
                'label' => esc_html__('Content Alignment', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => esc_html__('Image Left, Text Right', 'kolibri-fibonacci-mcp'),
                    'right' => esc_html__('Text Left, Image Right', 'kolibri-fibonacci-mcp'),
                ],
                'condition' => [
                    'section_layout' => ['61_38', '38_61'],
                ],
            ]
        );
        
        $this->add_control(
            'left_content',
            [
                'label' => esc_html__('Left Column Content', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<h2>Golden Ratio Design</h2><p>This section uses the Golden Ratio (φ = 1.618) for perfect proportions that are naturally pleasing to the eye.</p>',
                'condition' => [
                    'section_layout' => ['61_38', '38_61', 'fibonacci_sequence'],
                ],
            ]
        );
        
        $this->add_control(
            'right_content',
            [
                'label' => esc_html__('Right Column Content', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p>The Golden Ratio appears throughout nature and has been used in art and architecture for thousands of years.</p>',
                'condition' => [
                    'section_layout' => ['61_38', '38_61', 'fibonacci_sequence'],
                ],
            ]
        );
        
        $this->add_control(
            'center_content',
            [
                'label' => esc_html__('Center Column Content', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p>Middle content with perfect proportions.</p>',
                'condition' => [
                    'section_layout' => 'fibonacci_sequence',
                ],
            ]
        );
        
        $this->add_control(
            'show_phi_badge',
            [
                'label' => esc_html__('Show Phi Badge', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Display a badge showing this section uses Golden Ratio', 'kolibri-fibonacci-mcp'),
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'kolibri-fibonacci-mcp'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'section_height',
            [
                'label' => esc_html__('Section Height', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => esc_html__('Auto', 'kolibri-fibonacci-mcp'),
                    'phi_618' => esc_html__('618px (φ × 382)', 'kolibri-fibonacci-mcp'),
                    'phi_382' => esc_html__('382px (φ ÷ 618)', 'kolibri-fibonacci-mcp'),
                    'custom' => esc_html__('Custom', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'custom_height',
            [
                'label' => esc_html__('Custom Height', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'section_height' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-golden-section' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-golden-section' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'section_shadow',
                'label' => esc_html__('Box Shadow', 'kolibri-fibonacci-mcp'),
                'selector' => '{{WRAPPER}} .fibonacci-golden-section',
            ]
        );
        
        $this->add_responsive_control(
            'section_padding',
            [
                'label' => esc_html__('Padding', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '38',
                    'right' => '62',
                    'bottom' => '38',
                    'left' => '62',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-golden-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 38,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-columns' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $layout = $settings['section_layout'];
        $alignment = $settings['content_alignment'] ?? 'left';
        $show_badge = $settings['show_phi_badge'] === 'yes';
        $height = $settings['section_height'];
        
        $height_class = '';
        if ($height === 'phi_618') {
            $height_class = 'height-618';
        } elseif ($height === 'phi_382') {
            $height_class = 'height-382';
        }
        
        ?>
        <div class="fibonacci-golden-section fibonacci-layout-<?php echo esc_attr($layout); ?> <?php echo esc_attr($height_class); ?>">
            
            <?php if ($show_badge): ?>
            <div class="fibonacci-phi-badge">
                <span class="phi-symbol">φ</span>
                <span class="phi-value">1.618</span>
            </div>
            <?php endif; ?>
            
            <?php if ($layout === '61_38' || $layout === '38_61'): ?>
                <!-- Two Column Layout -->
                <div class="fibonacci-columns fibonacci-columns-2 alignment-<?php echo esc_attr($alignment); ?>">
                    <?php if ($layout === '61_38'): ?>
                        <div class="fibonacci-column fibonacci-col-major">
                            <?php echo wp_kses_post($settings['left_content']); ?>
                        </div>
                        <div class="fibonacci-column fibonacci-col-minor">
                            <?php echo wp_kses_post($settings['right_content']); ?>
                        </div>
                    <?php else: ?>
                        <div class="fibonacci-column fibonacci-col-minor">
                            <?php echo wp_kses_post($settings['left_content']); ?>
                        </div>
                        <div class="fibonacci-column fibonacci-col-major">
                            <?php echo wp_kses_post($settings['right_content']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php elseif ($layout === 'fibonacci_sequence'): ?>
                <!-- Three Column Fibonacci Sequence (21.1% / 34.1% / 44.8%) -->
                <div class="fibonacci-columns fibonacci-columns-3">
                    <div class="fibonacci-column fibonacci-col-fib-small">
                        <?php echo wp_kses_post($settings['left_content']); ?>
                    </div>
                    <div class="fibonacci-column fibonacci-col-fib-medium">
                        <?php echo wp_kses_post($settings['center_content']); ?>
                    </div>
                    <div class="fibonacci-column fibonacci-col-fib-large">
                        <?php echo wp_kses_post($settings['right_content']); ?>
                    </div>
                </div>
                
            <?php else: // single_618 ?>
                <!-- Single Centered Column -->
                <div class="fibonacci-columns fibonacci-columns-1">
                    <div class="fibonacci-column fibonacci-col-centered">
                        <?php echo wp_kses_post($settings['left_content']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (\Elementor\Plugin::$instance->editor->is_edit_mode()): ?>
            <div class="fibonacci-debug-info">
                <small>
                    <?php echo sprintf(
                        esc_html__('Layout: %s | Golden Ratio Applied', 'kolibri-fibonacci-mcp'),
                        esc_html($layout)
                    ); ?>
                </small>
            </div>
            <?php endif; ?>
            
        </div>
        
        <style>
            .fibonacci-golden-section {
                position: relative;
                width: 100%;
                border-radius: 12px;
                overflow: hidden;
            }
            .fibonacci-golden-section.height-618 {
                min-height: 618px;
            }
            .fibonacci-golden-section.height-382 {
                min-height: 382px;
            }
            .fibonacci-phi-badge {
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(155, 81, 224, 0.95);
                color: white;
                padding: 10px 15px;
                border-radius: 8px;
                font-family: 'Georgia', serif;
                z-index: 10;
                box-shadow: 0 4px 12px rgba(155, 81, 224, 0.3);
            }
            .fibonacci-phi-badge .phi-symbol {
                font-size: 24px;
                font-weight: bold;
                display: block;
                text-align: center;
            }
            .fibonacci-phi-badge .phi-value {
                font-size: 12px;
                display: block;
                text-align: center;
                opacity: 0.9;
            }
            .fibonacci-columns {
                display: flex;
                align-items: stretch;
                width: 100%;
                height: 100%;
            }
            .fibonacci-columns-2.alignment-left .fibonacci-col-major {
                order: 1;
            }
            .fibonacci-columns-2.alignment-left .fibonacci-col-minor {
                order: 2;
            }
            .fibonacci-columns-2.alignment-right .fibonacci-col-major {
                order: 2;
            }
            .fibonacci-columns-2.alignment-right .fibonacci-col-minor {
                order: 1;
            }
            
            /* Golden Ratio Column Widths */
            .fibonacci-col-major {
                flex: 0 0 61.8%;
                max-width: 61.8%;
            }
            .fibonacci-col-minor {
                flex: 0 0 38.2%;
                max-width: 38.2%;
            }
            
            /* Fibonacci Sequence Columns */
            .fibonacci-col-fib-small {
                flex: 0 0 21.1%;
                max-width: 21.1%;
            }
            .fibonacci-col-fib-medium {
                flex: 0 0 34.1%;
                max-width: 34.1%;
            }
            .fibonacci-col-fib-large {
                flex: 0 0 44.8%;
                max-width: 44.8%;
            }
            
            /* Centered Single Column */
            .fibonacci-columns-1 {
                justify-content: center;
            }
            .fibonacci-col-centered {
                flex: 0 0 61.8%;
                max-width: 61.8%;
            }
            
            .fibonacci-column {
                padding: 20px;
            }
            
            .fibonacci-debug-info {
                position: absolute;
                bottom: 10px;
                left: 10px;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 11px;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .fibonacci-columns-2,
                .fibonacci-columns-3 {
                    flex-direction: column;
                }
                .fibonacci-col-major,
                .fibonacci-col-minor,
                .fibonacci-col-fib-small,
                .fibonacci-col-fib-medium,
                .fibonacci-col-fib-large {
                    flex: 1 1 100%;
                    max-width: 100%;
                }
            }
        </style>
        <?php
    }
    
    protected function content_template() {
        ?>
        <#
        var layout = settings.section_layout;
        var showBadge = settings.show_phi_badge === 'yes';
        #>
        <div class="fibonacci-golden-section fibonacci-layout-{{ layout }}">
            
            <# if (showBadge) { #>
            <div class="fibonacci-phi-badge">
                <span class="phi-symbol">φ</span>
                <span class="phi-value">1.618</span>
            </div>
            <# } #>
            
            <# if (layout === '61_38' || layout === '38_61') { #>
                <div class="fibonacci-columns fibonacci-columns-2">
                    <# if (layout === '61_38') { #>
                        <div class="fibonacci-column fibonacci-col-major">
                            {{{ settings.left_content }}}
                        </div>
                        <div class="fibonacci-column fibonacci-col-minor">
                            {{{ settings.right_content }}}
                        </div>
                    <# } else { #>
                        <div class="fibonacci-column fibonacci-col-minor">
                            {{{ settings.left_content }}}
                        </div>
                        <div class="fibonacci-column fibonacci-col-major">
                            {{{ settings.right_content }}}
                        </div>
                    <# } #>
                </div>
            <# } else if (layout === 'fibonacci_sequence') { #>
                <div class="fibonacci-columns fibonacci-columns-3">
                    <div class="fibonacci-column fibonacci-col-fib-small">
                        {{{ settings.left_content }}}
                    </div>
                    <div class="fibonacci-column fibonacci-col-fib-medium">
                        {{{ settings.center_content }}}
                    </div>
                    <div class="fibonacci-column fibonacci-col-fib-large">
                        {{{ settings.right_content }}}
                    </div>
                </div>
            <# } else { #>
                <div class="fibonacci-columns fibonacci-columns-1">
                    <div class="fibonacci-column fibonacci-col-centered">
                        {{{ settings.left_content }}}
                    </div>
                </div>
            <# } #>
            
            <div class="fibonacci-debug-info">
                <small>Layout: {{ layout }} | Golden Ratio Applied</small>
            </div>
        </div>
        <?php
    }
}
