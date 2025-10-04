<?php
/**
 * Fibonacci AI Content Generator Widget
 * Generates content using AI based on prompts
 */

if (!defined('ABSPATH')) exit;

class Kolibri_Fibonacci_AI_Content_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'fibonacci-ai-content';
    }
    
    public function get_title() {
        return esc_html__('AI Content Generator', 'kolibri-fibonacci-mcp');
    }
    
    public function get_icon() {
        return 'eicon-ai';
    }
    
    public function get_categories() {
        return ['fibonacci-ai'];
    }
    
    public function get_keywords() {
        return ['ai', 'content', 'generator', 'fibonacci', 'neural'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('AI Settings', 'kolibri-fibonacci-mcp'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'prompt',
            [
                'label' => esc_html__('AI Prompt', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('Write engaging content about AI and machine learning', 'kolibri-fibonacci-mcp'),
                'placeholder' => esc_html__('Describe what content you want to generate...', 'kolibri-fibonacci-mcp'),
                'rows' => 5,
            ]
        );
        
        $this->add_control(
            'content_type',
            [
                'label' => esc_html__('Content Type', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'paragraph',
                'options' => [
                    'paragraph' => esc_html__('Paragraph', 'kolibri-fibonacci-mcp'),
                    'list' => esc_html__('List', 'kolibri-fibonacci-mcp'),
                    'heading' => esc_html__('Heading + Text', 'kolibri-fibonacci-mcp'),
                    'cta' => esc_html__('Call to Action', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'length',
            [
                'label' => esc_html__('Content Length', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['words'],
                'range' => [
                    'words' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'words',
                    'size' => 150,
                ],
            ]
        );
        
        $this->add_control(
            'tone',
            [
                'label' => esc_html__('Tone', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'professional',
                'options' => [
                    'professional' => esc_html__('Professional', 'kolibri-fibonacci-mcp'),
                    'casual' => esc_html__('Casual', 'kolibri-fibonacci-mcp'),
                    'friendly' => esc_html__('Friendly', 'kolibri-fibonacci-mcp'),
                    'formal' => esc_html__('Formal', 'kolibri-fibonacci-mcp'),
                    'humorous' => esc_html__('Humorous', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'language',
            [
                'label' => esc_html__('Language', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cs',
                'options' => [
                    'cs' => esc_html__('Czech', 'kolibri-fibonacci-mcp'),
                    'en' => esc_html__('English', 'kolibri-fibonacci-mcp'),
                    'sk' => esc_html__('Slovak', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'auto_generate',
            [
                'label' => esc_html__('Auto Generate on Save', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'no',
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
            'text_color',
            [
                'label' => esc_html__('Text Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-ai-content' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Typography', 'kolibri-fibonacci-mcp'),
                'selector' => '{{WRAPPER}} .fibonacci-ai-content',
            ]
        );
        
        $this->add_responsive_control(
            'text_align',
            [
                'label' => esc_html__('Alignment', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'kolibri-fibonacci-mcp'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'kolibri-fibonacci-mcp'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'kolibri-fibonacci-mcp'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justified', 'kolibri-fibonacci-mcp'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-ai-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $prompt = $settings['prompt'];
        $content_type = $settings['content_type'];
        $length = $settings['length']['size'];
        $tone = $settings['tone'];
        $language = $settings['language'];
        
        // Generate or retrieve AI content
        $content = $this->get_ai_content($prompt, $content_type, $length, $tone, $language);
        
        ?>
        <div class="fibonacci-ai-content" data-widget-type="<?php echo esc_attr($content_type); ?>">
            <?php echo wp_kses_post($content); ?>
            <?php if (\Elementor\Plugin::$instance->editor->is_edit_mode()): ?>
                <div class="fibonacci-ai-badge" style="position: absolute; top: 0; right: 0; background: #9b51e0; color: white; padding: 5px 10px; font-size: 11px; border-radius: 3px;">
                    🤖 AI Generated
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get AI-generated content
     * In production, this would call your AI API
     */
    private function get_ai_content($prompt, $type, $length, $tone, $language) {
        // Cache key for this specific content
        $cache_key = 'fib_ai_' . md5($prompt . $type . $length . $tone . $language);
        
        // Check cache first
        $cached = get_transient($cache_key);
        if ($cached !== false && !is_admin()) {
            return $cached;
        }
        
        // In editor mode, show placeholder with instructions
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $content = $this->get_placeholder_content($type, $prompt, $tone, $language);
        } else {
            // In production, this would call the Neural Network API
            // For now, return structured placeholder
            $content = $this->generate_demo_content($prompt, $type, $length, $tone, $language);
        }
        
        // Cache for 1 hour
        set_transient($cache_key, $content, HOUR_IN_SECONDS);
        
        return $content;
    }
    
    /**
     * Generate demo/placeholder content
     */
    private function generate_demo_content($prompt, $type, $length, $tone, $language) {
        $lang_map = [
            'cs' => 'česky',
            'en' => 'in English',
            'sk' => 'slovensky'
        ];
        
        switch ($type) {
            case 'heading':
                return sprintf(
                    '<h2>%s</h2><p>%s</p>',
                    esc_html__('AI-Generated Heading', 'kolibri-fibonacci-mcp'),
                    sprintf(
                        esc_html__('This content will be generated %s with a %s tone based on: "%s"', 'kolibri-fibonacci-mcp'),
                        $lang_map[$language],
                        $tone,
                        esc_html($prompt)
                    )
                );
                
            case 'list':
                return sprintf(
                    '<h3>%s</h3><ul><li>%s</li><li>%s</li><li>%s</li></ul>',
                    esc_html__('Key Points', 'kolibri-fibonacci-mcp'),
                    esc_html__('AI-generated point 1', 'kolibri-fibonacci-mcp'),
                    esc_html__('AI-generated point 2', 'kolibri-fibonacci-mcp'),
                    esc_html__('AI-generated point 3', 'kolibri-fibonacci-mcp')
                );
                
            case 'cta':
                return sprintf(
                    '<div class="fibonacci-cta"><h3>%s</h3><p>%s</p><a href="#" class="button">%s</a></div>',
                    esc_html__('Take Action Now', 'kolibri-fibonacci-mcp'),
                    esc_html__('AI-generated compelling call to action message', 'kolibri-fibonacci-mcp'),
                    esc_html__('Get Started', 'kolibri-fibonacci-mcp')
                );
                
            default: // paragraph
                return sprintf(
                    '<p>%s <strong>%s</strong>. %s: <em>%s</em>. %s: %d %s.</p>',
                    esc_html__('This is AI-generated content with', 'kolibri-fibonacci-mcp'),
                    $tone,
                    esc_html__('Prompt', 'kolibri-fibonacci-mcp'),
                    esc_html($prompt),
                    esc_html__('Target length', 'kolibri-fibonacci-mcp'),
                    $length,
                    esc_html__('words', 'kolibri-fibonacci-mcp')
                );
        }
    }
    
    /**
     * Editor placeholder content
     */
    private function get_placeholder_content($type, $prompt, $tone, $language) {
        return sprintf(
            '<div style="border: 2px dashed #9b51e0; padding: 20px; background: #f9f3ff; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #9b51e0;">🤖 AI Content Generator</h4>
                <p><strong>Type:</strong> %s</p>
                <p><strong>Prompt:</strong> %s</p>
                <p><strong>Tone:</strong> %s</p>
                <p><strong>Language:</strong> %s</p>
                <p style="margin-bottom: 0;"><em>Content will be generated when page is published or via MCP API.</em></p>
            </div>',
            esc_html($type),
            esc_html($prompt),
            esc_html($tone),
            esc_html($language)
        );
    }
    
    protected function content_template() {
        ?>
        <#
        var contentType = settings.content_type;
        var prompt = settings.prompt;
        #>
        <div class="fibonacci-ai-content">
            <div style="border: 2px dashed #9b51e0; padding: 20px; background: #f9f3ff; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #9b51e0;">🤖 AI Content Generator</h4>
                <p><strong>Type:</strong> {{ contentType }}</p>
                <p><strong>Prompt:</strong> {{ prompt }}</p>
                <p style="margin-bottom: 0;"><em>Live preview in editor. Content will be generated on save.</em></p>
            </div>
        </div>
        <?php
    }
}
