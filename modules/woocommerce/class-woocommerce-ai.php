<?php
/**
 * WooCommerce AI Integration
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_WooCommerce_AI {
    
    private $ai_engine;
    
    public function __construct() {
        $this->ai_engine = new Kafanek_AI_Engine();
        
        // Product AI features
        add_action('add_meta_boxes', [$this, 'add_ai_metabox']);
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_ai_pricing_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_ai_product_meta']);
        
        // AJAX handlers
        add_action('wp_ajax_kafanek_generate_product_description', [$this, 'ajax_generate_description']);
        add_action('wp_ajax_kafanek_optimize_price', [$this, 'ajax_optimize_price']);
        
        // Frontend
        add_action('woocommerce_after_single_product_summary', [$this, 'display_ai_recommendations'], 15);
    }
    
    /**
     * Add AI assistant metabox
     */
    public function add_ai_metabox() {
        add_meta_box(
            'kafanek_ai_assistant',
            '🧠 Kafánkův AI Asistent',
            [$this, 'render_ai_metabox'],
            'product',
            'side',
            'high'
        );
    }
    
    /**
     * Render AI metabox
     */
    public function render_ai_metabox($post) {
        wp_nonce_field('kafanek_ai_meta', 'kafanek_ai_nonce');
        ?>
        <div class="kafanek-ai-tools">
            <p>
                <button type="button" class="button button-primary button-large" id="kafanek-generate-desc" style="width:100%;">
                    📝 Generovat popis
                </button>
            </p>
            <p>
                <button type="button" class="button button-secondary button-large" id="kafanek-optimize-price" style="width:100%;">
                    💰 Optimalizovat cenu
                </button>
            </p>
            <p>
                <button type="button" class="button button-secondary" id="kafanek-generate-tags" style="width:100%;" disabled>
                    🏷️ Generovat tagy
                </button>
            </p>
            <div id="kafanek-ai-status" style="margin-top:10px;"></div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#kafanek-generate-desc').on('click', function() {
                var button = $(this);
                var productId = $('#post_ID').val();
                var status = $('#kafanek-ai-status');
                
                button.prop('disabled', true).text('⏳ Generuji...');
                status.html('<em>Generování popisu pomocí AI...</em>');
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'kafanek_generate_product_description',
                        product_id: productId,
                        nonce: '<?php echo wp_create_nonce('kafanek_ai_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Insert into description editor
                            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                tinymce.get('content').setContent(response.data.description);
                            }
                            $('#excerpt').val(response.data.short_description);
                            
                            status.html('<span style="color:green;">✅ Popis vygenerován!</span>');
                        } else {
                            status.html('<span style="color:red;">❌ ' + response.data + '</span>');
                        }
                    },
                    error: function() {
                        status.html('<span style="color:red;">❌ Chyba připojení</span>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('📝 Generovat popis');
                    }
                });
            });
            
            $('#kafanek-optimize-price').on('click', function() {
                var button = $(this);
                var productId = $('#post_ID').val();
                var currentPrice = $('#_regular_price').val();
                var status = $('#kafanek-ai-status');
                
                if (!currentPrice) {
                    status.html('<span style="color:red;">❌ Nejdříve nastavte základní cenu</span>');
                    return;
                }
                
                button.prop('disabled', true).text('⏳ Počítám...');
                status.html('<em>Optimalizace pomocí Golden Ratio...</em>');
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'kafanek_optimize_price',
                        product_id: productId,
                        current_price: currentPrice,
                        nonce: '<?php echo wp_create_nonce('kafanek_ai_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var msg = 'Doporučená cena: ' + response.data.optimized_price + ' Kč<br>';
                            msg += 'Změna: ' + response.data.change_percent + '%';
                            
                            if (confirm('Aplikovat optimalizovanou cenu?\n\nStará: ' + currentPrice + ' Kč\nNová: ' + response.data.optimized_price + ' Kč')) {
                                $('#_regular_price').val(response.data.optimized_price);
                                status.html('<span style="color:green;">✅ Cena optimalizována!</span>');
                            } else {
                                status.html('<span style="color:blue;">ℹ️ ' + msg + '</span>');
                            }
                        } else {
                            status.html('<span style="color:red;">❌ ' + response.data + '</span>');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false).text('💰 Optimalizovat cenu');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Add AI dynamic pricing field
     */
    public function add_ai_pricing_field() {
        global $post;
        
        $ai_pricing_enabled = get_post_meta($post->ID, '_kafanek_ai_pricing', true);
        
        woocommerce_wp_checkbox([
            'id' => '_kafanek_ai_pricing',
            'label' => '🤖 AI Dynamické ceně',
            'description' => 'Povolit automatickou optimalizaci ceny pomocí AI',
            'value' => $ai_pricing_enabled ? 'yes' : 'no'
        ]);
    }
    
    /**
     * Save AI product meta
     */
    public function save_ai_product_meta($product_id) {
        if (isset($_POST['_kafanek_ai_pricing'])) {
            update_post_meta($product_id, '_kafanek_ai_pricing', 'yes');
        } else {
            delete_post_meta($product_id, '_kafanek_ai_pricing');
        }
    }
    
    /**
     * AJAX: Generate product description
     */
    public function ajax_generate_description() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        if (!current_user_can('edit_products')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error('Invalid product ID');
        }
        
        $result = $this->ai_engine->generate_product_description($product_id);
        
        if (isset($result['error'])) {
            wp_send_json_error($result['error']);
        }
        
        wp_send_json_success($result);
    }
    
    /**
     * AJAX: Optimize price using Golden Ratio
     */
    public function ajax_optimize_price() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        if (!current_user_can('edit_products')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $current_price = floatval($_POST['current_price']);
        
        if ($current_price <= 0) {
            wp_send_json_error('Invalid price');
        }
        
        // Golden Ratio optimization
        $phi = KAFANEK_BRAIN_PHI; // 1.618
        $optimized_price = round($current_price * $phi, 2);
        $change_percent = round((($optimized_price - $current_price) / $current_price) * 100, 1);
        
        wp_send_json_success([
            'optimized_price' => $optimized_price,
            'change_percent' => ($change_percent > 0 ? '+' : '') . $change_percent,
            'phi' => $phi
        ]);
    }
    
    /**
     * Display AI recommendations on product page
     */
    public function display_ai_recommendations() {
        global $product;
        
        if (!$product) return;
        
        // Get cached recommendations
        $cache_key = 'kafanek_recommendations_' . $product->get_id();
        $recommendations = get_transient($cache_key);
        
        if ($recommendations === false) {
            $recommendations = $this->get_product_recommendations($product->get_id());
            set_transient($cache_key, $recommendations, 6 * HOUR_IN_SECONDS);
        }
        
        if (empty($recommendations)) return;
        
        ?>
        <div class="kafanek-ai-recommendations">
            <h3>🤖 AI Doporučuje také</h3>
            <div class="recommendations-grid">
                <?php foreach ($recommendations as $rec_id) : 
                    $rec_product = wc_get_product($rec_id);
                    if (!$rec_product) continue;
                ?>
                    <div class="recommendation-item">
                        <a href="<?php echo get_permalink($rec_id); ?>">
                            <?php echo $rec_product->get_image('thumbnail'); ?>
                            <h4><?php echo $rec_product->get_name(); ?></h4>
                            <span class="price"><?php echo $rec_product->get_price_html(); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get AI product recommendations
     */
    private function get_product_recommendations($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) return [];
        
        // Get products from same category
        $categories = $product->get_category_ids();
        
        if (empty($categories)) return [];
        
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 4,
            'post__not_in' => [$product_id],
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $categories
                ]
            ],
            'orderby' => 'rand'
        ];
        
        $query = new WP_Query($args);
        $recommendations = wp_list_pluck($query->posts, 'ID');
        
        return $recommendations;
    }
}

// Initialize
if (class_exists('WooCommerce')) {
    new Kafanek_WooCommerce_AI();
}
