<?php
/**
 * Fibonacci Elementor Integration
 * AI-powered Elementor widgets and page builder integration
 * 
 * @package Kolibri_Fibonacci_Brain
 * @subpackage Elementor_Integration
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kolibri_Fibonacci_Elementor_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Check if Elementor is installed
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    public function init() {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        // Add Elementor widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        
        // Add custom category for Fibonacci widgets
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_category']);
        
        // Register Elementor controls
        add_action('elementor/controls/register', [$this, 'register_controls']);
    }
    
    /**
     * Admin notice if Elementor is not active
     */
    public function admin_notice_missing_elementor() {
        if (!current_user_can('activate_plugins')) {
            return;
        }
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'kolibri-fibonacci-mcp'),
            '<strong>' . esc_html__('Fibonacci Elementor Integration', 'kolibri-fibonacci-mcp') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'kolibri-fibonacci-mcp') . '</strong>'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Add custom Elementor category
     */
    public function add_elementor_category($elements_manager) {
        $elements_manager->add_category(
            'fibonacci-ai',
            [
                'title' => esc_html__('Fibonacci AI', 'kolibri-fibonacci-mcp'),
                'icon' => 'fa fa-brain',
            ]
        );
    }
    
    /**
     * Register custom Elementor widgets
     */
    public function register_widgets($widgets_manager) {
        // Load widget files
        require_once __DIR__ . '/widgets/ai-content-generator.php';
        require_once __DIR__ . '/widgets/ai-price-optimizer.php';
        require_once __DIR__ . '/widgets/neural-insights.php';
        require_once __DIR__ . '/widgets/golden-ratio-section.php';
        
        // Register widgets
        $widgets_manager->register(new \Kolibri_Fibonacci_AI_Content_Widget());
        $widgets_manager->register(new \Kolibri_Fibonacci_Price_Optimizer_Widget());
        $widgets_manager->register(new \Kolibri_Fibonacci_Neural_Insights_Widget());
        $widgets_manager->register(new \Kolibri_Fibonacci_Golden_Ratio_Widget());
    }
    
    /**
     * Register custom controls (if needed)
     */
    public function register_controls($controls_manager) {
        // Custom controls can be added here
    }
    
    /**
     * Generate Elementor page from AI prompt
     * 
     * @param string $prompt AI prompt for page generation
     * @param string $page_title Page title
     * @return array Result with page ID and edit URL
     */
    public static function generate_page_from_prompt($prompt, $page_title = 'AI Generated Page') {
        // Create new page
        $page_id = wp_insert_post([
            'post_title' => sanitize_text_field($page_title),
            'post_status' => 'draft',
            'post_type' => 'page',
        ]);
        
        if (is_wp_error($page_id)) {
            return [
                'success' => false,
                'error' => $page_id->get_error_message()
            ];
        }
        
        // Enable Elementor for this page
        update_post_meta($page_id, '_elementor_edit_mode', 'builder');
        
        // Generate AI content based on prompt
        $elementor_data = self::generate_elementor_structure($prompt);
        
        // Save Elementor data
        update_post_meta($page_id, '_elementor_data', wp_json_encode($elementor_data));
        
        // Save Elementor version (with fallback)
        $elementor_version = defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.0.0';
        update_post_meta($page_id, '_elementor_version', $elementor_version);
        
        // Clear Elementor cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
        
        return [
            'success' => true,
            'page_id' => $page_id,
            'title' => $page_title,
            'url' => get_permalink($page_id),
            'edit_url' => admin_url('post.php?post=' . $page_id . '&action=elementor'),
            'elementor_data' => $elementor_data
        ];
    }
    
    /**
     * Generate Elementor structure from prompt
     * Uses AI to create page layout
     */
    private static function generate_elementor_structure($prompt) {
        // Basic Elementor structure with AI-powered sections
        
        // Analyze prompt for page type
        $has_hero = stripos($prompt, 'hero') !== false || stripos($prompt, 'banner') !== false;
        $has_features = stripos($prompt, 'features') !== false || stripos($prompt, 'benefits') !== false;
        $has_pricing = stripos($prompt, 'pricing') !== false || stripos($prompt, 'price') !== false;
        $has_cta = stripos($prompt, 'cta') !== false || stripos($prompt, 'call to action') !== false;
        
        $elements = [];
        
        // Add Hero Section if requested
        if ($has_hero) {
            $elements[] = self::create_hero_section($prompt);
        }
        
        // Add Features Section if requested
        if ($has_features) {
            $elements[] = self::create_features_section();
        }
        
        // Add Pricing Section if requested
        if ($has_pricing) {
            $elements[] = self::create_pricing_section();
        }
        
        // Add CTA Section if requested
        if ($has_cta || count($elements) > 0) {
            $elements[] = self::create_cta_section();
        }
        
        // If no specific sections requested, add basic content section
        if (empty($elements)) {
            $elements[] = self::create_content_section($prompt);
        }
        
        return $elements;
    }
    
    /**
     * Create Hero Section with Golden Ratio proportions
     */
    private static function create_hero_section($prompt) {
        return [
            'id' => wp_generate_uuid4(),
            'elType' => 'section',
            'settings' => [
                'layout' => 'boxed',
                'content_width' => 'full',
                'gap' => 'default',
                'height' => 'min-height',
                'height_mobile' => 'min-height',
                'custom_height' => ['size' => 618, 'unit' => 'px'], // Φ-based height
                'background_background' => 'gradient',
                'background_gradient_angle' => ['size' => 161, 'unit' => 'deg'], // Φ * 100
            ],
            'elements' => [
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 100],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'heading',
                            'settings' => [
                                'title' => 'AI-Generated Hero Section',
                                'header_size' => 'h1',
                                'align' => 'center',
                            ]
                        ],
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'text-editor',
                            'settings' => [
                                'editor' => 'Generated from: ' . esc_html($prompt),
                                'align' => 'center',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Create Features Section
     */
    private static function create_features_section() {
        return [
            'id' => wp_generate_uuid4(),
            'elType' => 'section',
            'settings' => [
                'layout' => 'boxed',
                'gap' => 'default',
            ],
            'elements' => [
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 33],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'icon-box',
                            'settings' => [
                                'title_text' => 'Feature 1',
                                'description_text' => 'AI-powered feature description',
                            ]
                        ]
                    ]
                ],
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 33],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'icon-box',
                            'settings' => [
                                'title_text' => 'Feature 2',
                                'description_text' => 'Neural network optimization',
                            ]
                        ]
                    ]
                ],
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 33],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'icon-box',
                            'settings' => [
                                'title_text' => 'Feature 3',
                                'description_text' => 'Golden Ratio design',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Create Pricing Section
     */
    private static function create_pricing_section() {
        return [
            'id' => wp_generate_uuid4(),
            'elType' => 'section',
            'settings' => [
                'layout' => 'boxed',
            ],
            'elements' => [
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 100],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'fibonacci-price-optimizer',
                            'settings' => [
                                'base_price' => 1000,
                                'show_calculations' => 'yes',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Create CTA Section
     */
    private static function create_cta_section() {
        return [
            'id' => wp_generate_uuid4(),
            'elType' => 'section',
            'settings' => [
                'layout' => 'boxed',
                'background_background' => 'classic',
                'background_color' => '#0073aa',
            ],
            'elements' => [
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 100],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'heading',
                            'settings' => [
                                'title' => 'Ready to Get Started?',
                                'align' => 'center',
                            ]
                        ],
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'button',
                            'settings' => [
                                'text' => 'Contact Us Today',
                                'align' => 'center',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Create basic Content Section
     */
    private static function create_content_section($prompt) {
        return [
            'id' => wp_generate_uuid4(),
            'elType' => 'section',
            'settings' => [
                'layout' => 'boxed',
            ],
            'elements' => [
                [
                    'id' => wp_generate_uuid4(),
                    'elType' => 'column',
                    'settings' => ['_column_size' => 100],
                    'elements' => [
                        [
                            'id' => wp_generate_uuid4(),
                            'elType' => 'widget',
                            'widgetType' => 'fibonacci-ai-content',
                            'settings' => [
                                'prompt' => $prompt,
                                'content_type' => 'paragraph',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Apply Golden Ratio optimization to existing Elementor page
     */
    public static function optimize_page_layout($page_id) {
        $elementor_data = get_post_meta($page_id, '_elementor_data', true);
        
        if (empty($elementor_data)) {
            return [
                'success' => false,
                'error' => 'No Elementor data found'
            ];
        }
        
        $data = json_decode($elementor_data, true);
        
        // Apply Golden Ratio to all sections
        foreach ($data as &$section) {
            if ($section['elType'] === 'section') {
                // Optimize section height
                if (!isset($section['settings']['custom_height'])) {
                    $section['settings']['custom_height'] = [
                        'size' => 618,
                        'unit' => 'px'
                    ];
                }
                
                // Optimize column widths using Fibonacci proportions
                if (isset($section['elements']) && count($section['elements']) === 2) {
                    $section['elements'][0]['settings']['_column_size'] = 61.8;
                    $section['elements'][1]['settings']['_column_size'] = 38.2;
                }
            }
        }
        
        // Save optimized data
        update_post_meta($page_id, '_elementor_data', wp_json_encode($data));
        
        // Clear cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
        
        return [
            'success' => true,
            'page_id' => $page_id,
            'optimizations_applied' => count($data),
            'phi' => KOLIBRI_FIB_PHI
        ];
    }
}

// Initialize
Kolibri_Fibonacci_Elementor_Integration::get_instance();
