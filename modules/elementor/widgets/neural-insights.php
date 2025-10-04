<?php
/**
 * Fibonacci Neural Insights Widget
 * Displays real-time analytics and predictions powered by Neural Network
 */

if (!defined('ABSPATH')) exit;

class Kolibri_Fibonacci_Neural_Insights_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'fibonacci-neural-insights';
    }
    
    public function get_title() {
        return esc_html__('Neural Insights', 'kolibri-fibonacci-mcp');
    }
    
    public function get_icon() {
        return 'eicon-lightbox-expand';
    }
    
    public function get_categories() {
        return ['fibonacci-ai'];
    }
    
    public function get_keywords() {
        return ['neural', 'insights', 'analytics', 'ai', 'predictions', 'fibonacci'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Insights Settings', 'kolibri-fibonacci-mcp'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'insight_type',
            [
                'label' => esc_html__('Insight Type', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'sales',
                'options' => [
                    'sales' => esc_html__('Sales Predictions', 'kolibri-fibonacci-mcp'),
                    'traffic' => esc_html__('Traffic Analytics', 'kolibri-fibonacci-mcp'),
                    'conversion' => esc_html__('Conversion Rate', 'kolibri-fibonacci-mcp'),
                    'products' => esc_html__('Product Performance', 'kolibri-fibonacci-mcp'),
                    'users' => esc_html__('User Behavior', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'time_period',
            [
                'label' => esc_html__('Time Period', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '7days',
                'options' => [
                    'today' => esc_html__('Today', 'kolibri-fibonacci-mcp'),
                    '7days' => esc_html__('Last 7 Days', 'kolibri-fibonacci-mcp'),
                    '30days' => esc_html__('Last 30 Days', 'kolibri-fibonacci-mcp'),
                    'quarter' => esc_html__('This Quarter', 'kolibri-fibonacci-mcp'),
                ],
            ]
        );
        
        $this->add_control(
            'show_predictions',
            [
                'label' => esc_html__('Show Future Predictions', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_recommendations',
            [
                'label' => esc_html__('Show AI Recommendations', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'kolibri-fibonacci-mcp'),
                'label_off' => esc_html__('No', 'kolibri-fibonacci-mcp'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'chart_type',
            [
                'label' => esc_html__('Chart Type', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'line',
                'options' => [
                    'line' => esc_html__('Line Chart', 'kolibri-fibonacci-mcp'),
                    'bar' => esc_html__('Bar Chart', 'kolibri-fibonacci-mcp'),
                    'donut' => esc_html__('Donut Chart', 'kolibri-fibonacci-mcp'),
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
            'primary_color',
            [
                'label' => esc_html__('Primary Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9b51e0',
            ]
        );
        
        $this->add_control(
            'success_color',
            [
                'label' => esc_html__('Success Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4CAF50',
            ]
        );
        
        $this->add_control(
            'warning_color',
            [
                'label' => esc_html__('Warning Color', 'kolibri-fibonacci-mcp'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FF9800',
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $insight_type = $settings['insight_type'];
        $time_period = $settings['time_period'];
        $show_predictions = $settings['show_predictions'] === 'yes';
        $show_recommendations = $settings['show_recommendations'] === 'yes';
        $chart_type = $settings['chart_type'];
        
        // Get insights data
        $insights = $this->get_neural_insights($insight_type, $time_period);
        
        ?>
        <div class="fibonacci-neural-insights" data-insight-type="<?php echo esc_attr($insight_type); ?>">
            
            <!-- Header -->
            <div class="fibonacci-insights-header">
                <h3>
                    <?php echo $this->get_insight_title($insight_type); ?>
                    <span class="fibonacci-neural-badge">🧠 Neural Network</span>
                </h3>
                <p class="fibonacci-time-period"><?php echo $this->get_period_label($time_period); ?></p>
            </div>
            
            <!-- Key Metrics -->
            <div class="fibonacci-metrics-grid">
                <?php foreach ($insights['metrics'] as $metric): ?>
                <div class="fibonacci-metric-card">
                    <div class="fibonacci-metric-icon"><?php echo $metric['icon']; ?></div>
                    <div class="fibonacci-metric-value"><?php echo esc_html($metric['value']); ?></div>
                    <div class="fibonacci-metric-label"><?php echo esc_html($metric['label']); ?></div>
                    <div class="fibonacci-metric-trend <?php echo esc_attr($metric['trend_direction']); ?>">
                        <?php echo $metric['trend_icon']; ?> <?php echo esc_html($metric['trend']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Chart Placeholder -->
            <div class="fibonacci-chart-container">
                <div class="fibonacci-chart-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 300px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                    <div style="text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 10px;">📊</div>
                        <div style="font-size: 18px; font-weight: bold;"><?php echo esc_html(ucfirst($chart_type)); ?> Chart</div>
                        <div style="font-size: 14px; opacity: 0.8; margin-top: 5px;">
                            <?php esc_html_e('Neural Network Data Visualization', 'kolibri-fibonacci-mcp'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($show_predictions): ?>
            <!-- Predictions -->
            <div class="fibonacci-predictions">
                <h4><?php esc_html_e('Neural Network Predictions', 'kolibri-fibonacci-mcp'); ?></h4>
                <div class="fibonacci-prediction-list">
                    <?php foreach ($insights['predictions'] as $prediction): ?>
                    <div class="fibonacci-prediction-item">
                        <div class="fibonacci-prediction-label"><?php echo esc_html($prediction['label']); ?></div>
                        <div class="fibonacci-prediction-bar">
                            <div class="fibonacci-prediction-fill" style="width: <?php echo esc_attr($prediction['confidence']); ?>%; background-color: <?php echo esc_attr($settings['primary_color']); ?>"></div>
                        </div>
                        <div class="fibonacci-prediction-value">
                            <?php echo esc_html($prediction['value']); ?>
                            <span class="fibonacci-confidence">(<?php echo esc_html($prediction['confidence']); ?>% confidence)</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($show_recommendations): ?>
            <!-- AI Recommendations -->
            <div class="fibonacci-recommendations">
                <h4><?php esc_html_e('AI-Powered Recommendations', 'kolibri-fibonacci-mcp'); ?></h4>
                <div class="fibonacci-recommendation-list">
                    <?php foreach ($insights['recommendations'] as $index => $recommendation): ?>
                    <div class="fibonacci-recommendation-item">
                        <div class="fibonacci-recommendation-priority priority-<?php echo esc_attr($recommendation['priority']); ?>">
                            <?php echo esc_html($recommendation['priority']); ?>
                        </div>
                        <div class="fibonacci-recommendation-content">
                            <strong><?php echo esc_html($recommendation['title']); ?></strong>
                            <p><?php echo esc_html($recommendation['description']); ?></p>
                        </div>
                        <div class="fibonacci-recommendation-impact">
                            <span class="impact-label"><?php esc_html_e('Impact:', 'kolibri-fibonacci-mcp'); ?></span>
                            <?php echo esc_html($recommendation['impact']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <style>
            .fibonacci-neural-insights {
                background: #f8f9fa;
                padding: 30px;
                border-radius: 16px;
            }
            .fibonacci-insights-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .fibonacci-insights-header h3 {
                font-size: 28px;
                margin: 0 0 10px 0;
                color: #333;
            }
            .fibonacci-neural-badge {
                display: inline-block;
                background: #9b51e0;
                color: white;
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: normal;
                margin-left: 10px;
            }
            .fibonacci-time-period {
                color: #666;
                font-size: 14px;
            }
            .fibonacci-metrics-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            .fibonacci-metric-card {
                background: white;
                padding: 20px;
                border-radius: 12px;
                text-align: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }
            .fibonacci-metric-icon {
                font-size: 32px;
                margin-bottom: 10px;
            }
            .fibonacci-metric-value {
                font-size: 32px;
                font-weight: bold;
                color: #9b51e0;
                margin: 10px 0;
            }
            .fibonacci-metric-label {
                color: #666;
                font-size: 14px;
                margin-bottom: 10px;
            }
            .fibonacci-metric-trend {
                font-size: 13px;
                font-weight: 600;
            }
            .fibonacci-metric-trend.up {
                color: #4CAF50;
            }
            .fibonacci-metric-trend.down {
                color: #f44336;
            }
            .fibonacci-chart-container {
                margin: 30px 0;
            }
            .fibonacci-predictions,
            .fibonacci-recommendations {
                margin-top: 30px;
            }
            .fibonacci-predictions h4,
            .fibonacci-recommendations h4 {
                color: #333;
                margin-bottom: 20px;
                font-size: 20px;
            }
            .fibonacci-prediction-item {
                background: white;
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 15px;
            }
            .fibonacci-prediction-label {
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
            }
            .fibonacci-prediction-bar {
                height: 8px;
                background: #e0e0e0;
                border-radius: 4px;
                overflow: hidden;
                margin: 10px 0;
            }
            .fibonacci-prediction-fill {
                height: 100%;
                transition: width 0.3s ease;
            }
            .fibonacci-prediction-value {
                font-size: 18px;
                font-weight: bold;
                color: #9b51e0;
            }
            .fibonacci-confidence {
                font-size: 12px;
                color: #999;
                font-weight: normal;
            }
            .fibonacci-recommendation-item {
                background: white;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .fibonacci-recommendation-priority {
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .fibonacci-recommendation-priority.priority-high {
                background: #ffebee;
                color: #c62828;
            }
            .fibonacci-recommendation-priority.priority-medium {
                background: #fff3e0;
                color: #ef6c00;
            }
            .fibonacci-recommendation-priority.priority-low {
                background: #e8f5e9;
                color: #2e7d32;
            }
            .fibonacci-recommendation-content {
                flex: 1;
            }
            .fibonacci-recommendation-content strong {
                display: block;
                color: #333;
                margin-bottom: 5px;
            }
            .fibonacci-recommendation-content p {
                margin: 0;
                color: #666;
                font-size: 14px;
            }
            .fibonacci-recommendation-impact {
                text-align: right;
                font-weight: 600;
                color: #9b51e0;
            }
            .impact-label {
                display: block;
                font-size: 11px;
                color: #999;
                font-weight: normal;
            }
        </style>
        <?php
    }
    
    /**
     * Get neural insights data
     */
    private function get_neural_insights($type, $period) {
        // In production, this would query the Neural Network API
        // For now, return demo data
        
        $insights = [
            'metrics' => [],
            'predictions' => [],
            'recommendations' => []
        ];
        
        switch ($type) {
            case 'sales':
                $insights['metrics'] = [
                    ['icon' => '💰', 'value' => '45,280 Kč', 'label' => 'Total Revenue', 'trend' => '+12.5%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                    ['icon' => '🛒', 'value' => '127', 'label' => 'Orders', 'trend' => '+8.3%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                    ['icon' => '📊', 'value' => '356 Kč', 'label' => 'Avg Order Value', 'trend' => '+3.2%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                ];
                $insights['predictions'] = [
                    ['label' => 'Next 7 Days Revenue', 'value' => '52,100 Kč', 'confidence' => 87],
                    ['label' => 'Next Month Orders', 'value' => '510 orders', 'confidence' => 82],
                ];
                $insights['recommendations'] = [
                    ['priority' => 'high', 'title' => 'Optimize Product Prices', 'description' => 'Adjust prices using Golden Ratio for 15% revenue increase', 'impact' => '+15%'],
                    ['priority' => 'medium', 'title' => 'Launch Email Campaign', 'description' => 'Target inactive customers for reactivation', 'impact' => '+8%'],
                ];
                break;
                
            case 'traffic':
                $insights['metrics'] = [
                    ['icon' => '👥', 'value' => '3,245', 'label' => 'Visitors', 'trend' => '+18.2%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                    ['icon' => '📄', 'value' => '12,890', 'label' => 'Page Views', 'trend' => '+22.1%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                    ['icon' => '⏱️', 'value' => '3:42', 'label' => 'Avg Session', 'trend' => '+1:15', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                ];
                $insights['predictions'] = [
                    ['label' => 'Next Week Traffic', 'value' => '3,890 visitors', 'confidence' => 91],
                    ['label' => 'Peak Traffic Day', 'value' => 'Wednesday', 'confidence' => 85],
                ];
                $insights['recommendations'] = [
                    ['priority' => 'high', 'title' => 'Optimize Landing Pages', 'description' => 'AI suggests improving CTAs on top 3 pages', 'impact' => '+25%'],
                    ['priority' => 'low', 'title' => 'Mobile Experience', 'description' => 'Enhance mobile loading speed', 'impact' => '+5%'],
                ];
                break;
                
            default:
                $insights['metrics'] = [
                    ['icon' => '📈', 'value' => '4.2%', 'label' => 'Conversion Rate', 'trend' => '+0.8%', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                    ['icon' => '✅', 'value' => '89', 'label' => 'Conversions', 'trend' => '+12', 'trend_direction' => 'up', 'trend_icon' => '↑'],
                ];
                $insights['predictions'] = [
                    ['label' => 'Next Week Conversions', 'value' => '95 conversions', 'confidence' => 84],
                ];
                $insights['recommendations'] = [
                    ['priority' => 'medium', 'title' => 'A/B Test Checkout', 'description' => 'Test simplified checkout flow', 'impact' => '+10%'],
                ];
        }
        
        return $insights;
    }
    
    private function get_insight_title($type) {
        $titles = [
            'sales' => __('Sales & Revenue Insights', 'kolibri-fibonacci-mcp'),
            'traffic' => __('Traffic Analytics', 'kolibri-fibonacci-mcp'),
            'conversion' => __('Conversion Performance', 'kolibri-fibonacci-mcp'),
            'products' => __('Product Performance', 'kolibri-fibonacci-mcp'),
            'users' => __('User Behavior Analysis', 'kolibri-fibonacci-mcp'),
        ];
        return $titles[$type] ?? __('Neural Insights', 'kolibri-fibonacci-mcp');
    }
    
    private function get_period_label($period) {
        $labels = [
            'today' => __('Today', 'kolibri-fibonacci-mcp'),
            '7days' => __('Last 7 Days', 'kolibri-fibonacci-mcp'),
            '30days' => __('Last 30 Days', 'kolibri-fibonacci-mcp'),
            'quarter' => __('This Quarter', 'kolibri-fibonacci-mcp'),
        ];
        return $labels[$period] ?? __('Analysis Period', 'kolibri-fibonacci-mcp');
    }
}
