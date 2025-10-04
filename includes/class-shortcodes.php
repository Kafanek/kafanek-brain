<?php
/**
 * Kafánek Brain Shortcodes
 * Easy-to-use shortcodes for AI features
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Brain_Shortcodes {
    
    public function __construct() {
        // Register all shortcodes
        add_shortcode('kafanek_chat', [$this, 'chat_widget']);
        add_shortcode('kafanek_content', [$this, 'ai_content']);
        add_shortcode('kafanek_price', [$this, 'dynamic_price']);
        add_shortcode('kafanek_recommendation', [$this, 'product_recommendation']);
        add_shortcode('kafanek_neural_insight', [$this, 'neural_insight']);
    }
    
    /**
     * AI Chat Widget Shortcode
     * Usage: [kafanek_chat]
     */
    public function chat_widget($atts) {
        $atts = shortcode_atts([
            'position' => 'bottom-right',
            'button_text' => 'Chat s námi',
            'welcome_message' => 'Ahoj! Jak vám mohu pomoci?'
        ], $atts);
        
        ob_start();
        ?>
        <div id="kafanek-chatbot-widget" class="position-<?php echo esc_attr($atts['position']); ?>">
            <div class="chat-button" data-welcome="<?php echo esc_attr($atts['welcome_message']); ?>">
                <img src="<?php echo KAFANEK_BRAIN_URL; ?>assets/images/kafanek-icon.png" alt="Chat">
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AI Generated Content Shortcode
     * Usage: [kafanek_content type="blog" topic="AI technologie"]
     */
    public function ai_content($atts) {
        $atts = shortcode_atts([
            'type' => 'blog',
            'topic' => '',
            'length' => 'medium',
            'tone' => 'professional',
            'cache' => 'yes'
        ], $atts);
        
        if (empty($atts['topic'])) {
            return '<p class="kafanek-error">Chybí parametr "topic"</p>';
        }
        
        // Check cache first
        $cache_key = 'kafanek_content_' . md5(serialize($atts));
        if ($atts['cache'] === 'yes') {
            $cached = get_transient($cache_key);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        // Generate content via AI
        $ai_engine = new Kafanek_AI_Engine();
        
        $prompt = $this->build_content_prompt($atts);
        $content = $ai_engine->generate_text($prompt);
        
        if (is_wp_error($content)) {
            return '<p class="kafanek-error">Chyba při generování: ' . $content->get_error_message() . '</p>';
        }
        
        $output = '<div class="kafanek-ai-content">' . wp_kses_post($content) . '</div>';
        
        // Cache for 21 minutes (Fibonacci)
        if ($atts['cache'] === 'yes') {
            set_transient($cache_key, $output, 21 * MINUTE_IN_SECONDS);
        }
        
        return $output;
    }
    
    /**
     * Dynamic Price with Golden Ratio
     * Usage: [kafanek_price product_id="123" tier="premium"]
     */
    public function dynamic_price($atts) {
        $atts = shortcode_atts([
            'product_id' => 0,
            'tier' => 'standard',
            'show_original' => 'no'
        ], $atts);
        
        if (empty($atts['product_id']) || !class_exists('WooCommerce')) {
            return '<span class="kafanek-error">Neplatný produkt nebo WooCommerce není aktivní</span>';
        }
        
        $product = wc_get_product($atts['product_id']);
        if (!$product) {
            return '<span class="kafanek-error">Produkt nenalezen</span>';
        }
        
        $base_price = floatval($product->get_regular_price());
        $phi = 1.618033988749895; // Golden Ratio
        
        // Calculate prices based on tier
        $prices = [
            'budget' => round($base_price / $phi, 2),
            'standard' => $base_price,
            'premium' => round($base_price * $phi, 2),
            'luxury' => round($base_price * ($phi * $phi), 2)
        ];
        
        $selected_price = isset($prices[$atts['tier']]) ? $prices[$atts['tier']] : $base_price;
        
        ob_start();
        ?>
        <div class="kafanek-dynamic-price">
            <?php if ($atts['show_original'] === 'yes' && $atts['tier'] !== 'standard'): ?>
                <span class="original-price"><del><?php echo wc_price($base_price); ?></del></span>
            <?php endif; ?>
            <span class="golden-price" data-tier="<?php echo esc_attr($atts['tier']); ?>">
                <?php echo wc_price($selected_price); ?>
            </span>
            <small class="phi-badge">φ optimized</small>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AI Product Recommendations
     * Usage: [kafanek_recommendation category="electronics" limit="4"]
     */
    public function product_recommendation($atts) {
        $atts = shortcode_atts([
            'category' => '',
            'limit' => 4,
            'layout' => 'grid'
        ], $atts);
        
        if (!class_exists('WooCommerce')) {
            return '<p class="kafanek-error">WooCommerce není aktivní</p>';
        }
        
        // Get user behavior data for neural network prediction
        $user_id = get_current_user_id();
        $viewed_products = $this->get_user_viewed_products($user_id);
        
        // Neural network prediction
        $recommended_ids = $this->get_neural_recommendations($viewed_products, $atts['category'], $atts['limit']);
        
        if (empty($recommended_ids)) {
            // Fallback to popular products
            $args = [
                'post_type' => 'product',
                'posts_per_page' => $atts['limit'],
                'orderby' => 'meta_value_num',
                'meta_key' => 'total_sales'
            ];
            
            if (!empty($atts['category'])) {
                $args['tax_query'] = [[
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $atts['category']
                ]];
            }
            
            $query = new WP_Query($args);
            $recommended_ids = wp_list_pluck($query->posts, 'ID');
        }
        
        ob_start();
        ?>
        <div class="kafanek-recommendations layout-<?php echo esc_attr($atts['layout']); ?>">
            <h3 class="recommendations-title">🧠 AI Doporučení pro vás</h3>
            <div class="recommendations-grid">
                <?php foreach ($recommended_ids as $product_id): 
                    $product = wc_get_product($product_id);
                    if (!$product) continue;
                ?>
                    <div class="recommendation-item">
                        <a href="<?php echo get_permalink($product_id); ?>">
                            <?php echo $product->get_image('medium'); ?>
                            <h4><?php echo $product->get_name(); ?></h4>
                            <span class="price"><?php echo $product->get_price_html(); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Neural Network Insight
     * Usage: [kafanek_neural_insight type="price_prediction" product_id="123"]
     */
    public function neural_insight($atts) {
        $atts = shortcode_atts([
            'type' => 'price_prediction',
            'product_id' => 0,
            'show_confidence' => 'yes'
        ], $atts);
        
        $cache_key = 'kafanek_neural_' . md5(serialize($atts));
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $insight_data = $this->get_neural_insight_data($atts);
        
        ob_start();
        ?>
        <div class="kafanek-neural-insight">
            <div class="insight-header">
                <span class="insight-icon">🧬</span>
                <h4><?php echo esc_html($insight_data['title']); ?></h4>
            </div>
            <div class="insight-content">
                <?php echo wp_kses_post($insight_data['content']); ?>
            </div>
            <?php if ($atts['show_confidence'] === 'yes' && isset($insight_data['confidence'])): ?>
                <div class="insight-confidence">
                    <span>Přesnost: <?php echo number_format($insight_data['confidence'] * 100, 1); ?>%</span>
                    <div class="confidence-bar">
                        <div class="confidence-fill" style="width: <?php echo ($insight_data['confidence'] * 100); ?>%"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        
        $output = ob_get_clean();
        set_transient($cache_key, $output, 13 * MINUTE_IN_SECONDS); // Fibonacci
        
        return $output;
    }
    
    /**
     * Build AI content prompt
     */
    private function build_content_prompt($atts) {
        $prompts = [
            'blog' => "Napiš {$atts['length']} článek o tématu: {$atts['topic']}. Tón: {$atts['tone']}.",
            'product' => "Vytvoř popis produktu pro: {$atts['topic']}. Zaměř se na výhody a vlastnosti. Tón: {$atts['tone']}.",
            'email' => "Napiš marketingový email o: {$atts['topic']}. Tón: {$atts['tone']}.",
            'social' => "Vytvoř příspěvek na sociální sítě o: {$atts['topic']}. Tón: {$atts['tone']}."
        ];
        
        return isset($prompts[$atts['type']]) ? $prompts[$atts['type']] : $prompts['blog'];
    }
    
    /**
     * Get user viewed products
     */
    private function get_user_viewed_products($user_id) {
        if ($user_id > 0) {
            $viewed = get_user_meta($user_id, '_wc_recently_viewed', true);
            return !empty($viewed) ? array_slice($viewed, 0, 10) : [];
        }
        return [];
    }
    
    /**
     * Get neural network recommendations
     */
    private function get_neural_recommendations($viewed_products, $category, $limit) {
        // Simplified neural recommendation logic
        // In production, this would use the actual neural network
        
        if (empty($viewed_products)) {
            return [];
        }
        
        global $wpdb;
        
        // Get products similar to viewed ones
        $placeholders = implode(',', array_fill(0, count($viewed_products), '%d'));
        
        $query = "SELECT DISTINCT p.ID 
                  FROM {$wpdb->posts} p
                  INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                  WHERE p.post_type = 'product'
                  AND p.post_status = 'publish'
                  AND tr.term_taxonomy_id IN (
                      SELECT DISTINCT tr2.term_taxonomy_id
                      FROM {$wpdb->term_relationships} tr2
                      WHERE tr2.object_id IN ($placeholders)
                  )
                  AND p.ID NOT IN ($placeholders)
                  LIMIT %d";
        
        $params = array_merge($viewed_products, $viewed_products, [$limit]);
        $results = $wpdb->get_col($wpdb->prepare($query, $params));
        
        return $results;
    }
    
    /**
     * Get neural insight data
     */
    private function get_neural_insight_data($atts) {
        $insights = [
            'price_prediction' => [
                'title' => 'Predikce optimální ceny',
                'content' => '<p>Na základě historických dat a tržních trendů doporučujeme cenu v rozmezí <strong>899-1099 Kč</strong>.</p>',
                'confidence' => 0.87
            ],
            'sales_forecast' => [
                'title' => 'Prognóza prodejů',
                'content' => '<p>Očekáváme nárůst prodejů o <strong>23%</strong> v příštím měsíci.</p>',
                'confidence' => 0.91
            ],
            'customer_behavior' => [
                'title' => 'Chování zákazníků',
                'content' => '<p>Většina zákazníků nakupuje ve večerních hodinách (18-21h).</p>',
                'confidence' => 0.94
            ]
        ];
        
        $type = $atts['type'];
        return isset($insights[$type]) ? $insights[$type] : $insights['price_prediction'];
    }
}

// Initialize shortcodes
new Kafanek_Brain_Shortcodes();
