<?php
/**
 * Fibonacci AI Price Optimizer Widget
 * Displays optimized prices using Golden Ratio and Neural Network predictions
 */

if (!defined('ABSPATH')) exit;

class Kolibri_Fibonacci_Price_Optimizer_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'fibonacci-price-optimizer';
    }
    
    public function get_title() {
        return esc_html__('AI Price Optimizer', 'kolibri-fibonacci-mcp');
    }
    
    public function get_icon() {
        return 'eicon-price-table';
    }
    
    public function get_categories() {
        return ['fibonacci-ai'];
    }
    
    public function get_keywords() {
        return ['price', 'optimizer', 'fibonacci', 'golden ratio', 'ai', 'woocommerce'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Price Settings', 'kolibri-fibonacci-mcp'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'source_type',
            [
                'label' => esc_html__('Price Source', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'manual',
                'options' => [
                    'manual' => esc_html__('Manual Input', 'kolibri-fibonacci-mcp'),
                    'product' => esc_html__('WooCommerce Product', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'base_price',
            [
                'label' => esc_html__('Base Price', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1000,
                'min' => 0,
                'step' => 1,
                'condition' => [
                    'source_type' => 'manual',
                ],
            ]
        );
        
        $this->add_control(
            'product_id',
            [
                'label' => esc_html__('Product ID', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '',
                'condition' => [
                    'source_type' => 'product',
                ],
            ]
        );
        
        $this->add_control(
            'currency',
            [
                'label' => esc_html__('Currency', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Kč',
            ]
        );
        
        $this->add_control(
            'show_calculations',
            [
                'label' => esc_html__('Show Calculations', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_neural_prediction',
            [
                'label' => esc_html__('Show Neural Network Prediction', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'optimization_type',
            [
                'label' => esc_html__('Optimization Goal', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'balanced',
                'options' => [
                    'profit' => esc_html__('Maximum Profit', 'kolibri-fibonacci-mcp'),
                    'balanced' => esc_html__('Balanced (Recommended)', 'kolibri-fibonacci-mcp'),
                    'volume' => esc_html__('Maximum Sales Volume', 'kolibri-fibonacci-mcp'),
                ],
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
            'layout',
            [
                'label' => esc_html__('Layout', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cards',
                'options' => [
                    'cards' => esc_html__('Cards', 'kolibri-fibonacci-mcp'),
                    'table' => esc_html__('Table', 'kolibri-fibonacci-mcp'),
                    'minimal' => esc_html__('Minimal', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Primary Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9b51e0',
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-price-card.optimal' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .fibonacci-price-badge' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Card Background', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fibonacci-price-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Get base price
        if ($settings['source_type'] === 'product' && !empty($settings['product_id'])) {
            $product = wc_get_product($settings['product_id']);
            $base_price = $product ? floatval($product->get_price()) : floatval($settings['base_price']);
        } else {
            $base_price = floatval($settings['base_price']);
        }
        
        $currency = $settings['currency'];
        $show_calculations = $settings['show_calculations'] === 'yes';
        $show_neural = $settings['show_neural_prediction'] === 'yes';
        $layout = $settings['layout'];
        $optimization = $settings['optimization_type'];
        
        // Calculate optimized prices using Golden Ratio
        $phi = 1.618033988749895;
        
        $prices = [
            'current' => $base_price,
            'optimal_lower' => round($base_price / $phi, 2),
            'optimal' => $base_price,
            'optimal_higher' => round($base_price * $phi, 2),
            'premium' => round($base_price * ($phi * $phi), 2),
        ];
        
        // Neural Network prediction (simplified)
        if ($show_neural) {
            $neural_multiplier = $this->get_neural_prediction($base_price, $optimization);
            $prices['neural_recommended'] = round($base_price * $neural_multiplier, 2);
        }
        
        ?>
        <div class="fibonacci-price-optimizer fibonacci-layout-<?php echo esc_attr($layout); ?>">
            
            <?php if ($show_calculations): ?>
            <div class="fibonacci-price-info">
                <h3><?php esc_html_e('Price Optimization Analysis', 'kolibri-fibonacci-mcp'); ?></h3>
                <p class="fibonacci-phi">Φ (Golden Ratio) = <?php echo number_format($phi, 6); ?></p>
                <p class="fibonacci-optimization">
                    <?php esc_html_e('Optimization Goal:', 'kolibri-fibonacci-mcp'); ?> 
                    <strong><?php echo esc_html(ucfirst($optimization)); ?></strong>
                </p>
            </div>
            <?php endif; ?>
            
            <div class="fibonacci-price-grid">
                
                <!-- Budget Option -->
                <div class="fibonacci-price-card">
                    <div class="fibonacci-price-header">
                        <h4><?php esc_html_e('Budget', 'kolibri-fibonacci-mcp'); ?></h4>
                    </div>
                    <div class="fibonacci-price-amount">
                        <?php echo number_format($prices['optimal_lower'], 0, ',', ' '); ?> <?php echo esc_html($currency); ?>
                    </div>
                    <div class="fibonacci-price-calculation">
                        <?php if ($show_calculations): ?>
                        <small>Base / Φ = <?php echo number_format($base_price, 0); ?> / <?php echo number_format($phi, 2); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="fibonacci-price-description">
                        <?php esc_html_e('Entry-level pricing for maximum reach', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                </div>
                
                <!-- Standard/Optimal Option -->
                <div class="fibonacci-price-card optimal">
                    <div class="fibonacci-price-badge">
                        <?php esc_html_e('Optimal', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                    <div class="fibonacci-price-header">
                        <h4><?php esc_html_e('Standard', 'kolibri-fibonacci-mcp'); ?></h4>
                    </div>
                    <div class="fibonacci-price-amount">
                        <?php echo number_format($prices['optimal'], 0, ',', ' '); ?> <?php echo esc_html($currency); ?>
                    </div>
                    <div class="fibonacci-price-calculation">
                        <?php if ($show_calculations): ?>
                        <small><?php esc_html_e('Base Price (Recommended)', 'kolibri-fibonacci-mcp'); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="fibonacci-price-description">
                        <?php esc_html_e('Balanced value and quality', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                </div>
                
                <!-- Premium Option -->
                <div class="fibonacci-price-card">
                    <div class="fibonacci-price-header">
                        <h4><?php esc_html_e('Premium', 'kolibri-fibonacci-mcp'); ?></h4>
                    </div>
                    <div class="fibonacci-price-amount">
                        <?php echo number_format($prices['optimal_higher'], 0, ',', ' '); ?> <?php echo esc_html($currency); ?>
                    </div>
                    <div class="fibonacci-price-calculation">
                        <?php if ($show_calculations): ?>
                        <small>Base × Φ = <?php echo number_format($base_price, 0); ?> × <?php echo number_format($phi, 2); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="fibonacci-price-description">
                        <?php esc_html_e('Enhanced features and benefits', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                </div>
                
                <?php if ($show_neural && isset($prices['neural_recommended'])): ?>
                <!-- Neural Network Recommendation -->
                <div class="fibonacci-price-card neural">
                    <div class="fibonacci-price-badge neural-badge">
                        🤖 <?php esc_html_e('AI Prediction', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                    <div class="fibonacci-price-header">
                        <h4><?php esc_html_e('Neural Network', 'kolibri-fibonacci-mcp'); ?></h4>
                    </div>
                    <div class="fibonacci-price-amount">
                        <?php echo number_format($prices['neural_recommended'], 0, ',', ' '); ?> <?php echo esc_html($currency); ?>
                    </div>
                    <div class="fibonacci-price-calculation">
                        <?php if ($show_calculations): ?>
                        <small><?php esc_html_e('Based on market analysis & predictions', 'kolibri-fibonacci-mcp'); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="fibonacci-price-description">
                        <?php esc_html_e('AI-optimized for current market conditions', 'kolibri-fibonacci-mcp'); ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
            
            <?php if (\Elementor\Plugin::$instance->editor->is_edit_mode()): ?>
            <div class="fibonacci-ai-badge" style="margin-top: 20px; background: #9b51e0; color: white; padding: 10px; text-align: center; border-radius: 5px;">
                🧮 Fibonacci Price Optimizer Active
            </div>
            <?php endif; ?>
        </div>
        
        <style>
            .fibonacci-price-optimizer {
                padding: 20px 0;
            }
            .fibonacci-price-info {
                text-align: center;
                margin-bottom: 30px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .fibonacci-price-info h3 {
                margin-top: 0;
                color: #333;
            }
            .fibonacci-phi {
                font-family: monospace;
                font-size: 18px;
                color: #9b51e0;
                font-weight: bold;
            }
            .fibonacci-price-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .fibonacci-price-card {
                background: white;
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                padding: 30px 20px;
                text-align: center;
                position: relative;
                transition: all 0.3s ease;
            }
            .fibonacci-price-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            .fibonacci-price-card.optimal {
                border-color: #9b51e0;
                border-width: 3px;
            }
            .fibonacci-price-card.neural {
                border-color: #4CAF50;
            }
            .fibonacci-price-badge {
                position: absolute;
                top: -12px;
                left: 50%;
                transform: translateX(-50%);
                background: #9b51e0;
                color: white;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .fibonacci-price-badge.neural-badge {
                background: #4CAF50;
            }
            .fibonacci-price-header h4 {
                margin: 10px 0;
                color: #333;
                font-size: 20px;
            }
            .fibonacci-price-amount {
                font-size: 36px;
                font-weight: bold;
                color: #9b51e0;
                margin: 15px 0;
            }
            .fibonacci-price-calculation {
                min-height: 20px;
                color: #666;
                font-size: 13px;
                margin: 10px 0;
            }
            .fibonacci-price-description {
                color: #777;
                font-size: 14px;
                line-height: 1.6;
                margin-top: 15px;
            }
        </style>
        <?php
    }
    
    /**
     * Get Neural Network price prediction
     * Simplified version - in production would call actual Neural Network
     */
    private function get_neural_prediction($base_price, $optimization) {
        // Simplified neural prediction based on optimization goal
        switch ($optimization) {
            case 'profit':
                return 1.382; // Higher price for profit
            case 'volume':
                return 0.786; // Lower price for volume
            default: // balanced
                return 1.15; // Moderate increase
        }
    }
    
    protected function content_template() {
        ?>
        <#
        var basePrice = settings.base_price;
        var currency = settings.currency;
        var phi = 1.618;
        #>
        <div class="fibonacci-price-optimizer">
            <div class="fibonacci-price-info">
                <h3>Price Optimization Analysis</h3>
                <p class="fibonacci-phi">Φ (Golden Ratio) = 1.618034</p>
            </div>
            <div class="fibonacci-price-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="fibonacci-price-card" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 30px 20px; text-align: center;">
                    <h4>Budget</h4>
                    <div style="font-size: 36px; color: #9b51e0; font-weight: bold;">
                        {{ Math.round(basePrice / phi) }} {{ currency }}
                    </div>
                    <p>Entry-level pricing</p>
                </div>
                <div class="fibonacci-price-card optimal" style="border: 3px solid #9b51e0; border-radius: 12px; padding: 30px 20px; text-align: center;">
                    <div style="position: absolute; top: -12px; background: #9b51e0; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px;">OPTIMAL</div>
                    <h4>Standard</h4>
                    <div style="font-size: 36px; color: #9b51e0; font-weight: bold;">
                        {{ basePrice }} {{ currency }}
                    </div>
                    <p>Recommended price</p>
                </div>
                <div class="fibonacci-price-card" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 30px 20px; text-align: center;">
                    <h4>Premium</h4>
                    <div style="font-size: 36px; color: #9b51e0; font-weight: bold;">
                        {{ Math.round(basePrice * phi) }} {{ currency }}
                    </div>
                    <p>Premium pricing</p>
                </div>
            </div>
        </div>
        <?php
    }
}
