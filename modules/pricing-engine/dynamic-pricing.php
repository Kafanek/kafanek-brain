<?php
/**
 * Dynamic Pricing Engine - φ-Based Price Optimization
 * @version 1.2.0
 * Golden Ratio (φ = 1.618) Enhanced
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Dynamic_Pricing {
    
    private $phi;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('wp_ajax_kafanek_optimize_product_price', [$this, 'ajax_optimize_price']);
        add_action('wp_ajax_kafanek_bulk_price_optimize', [$this, 'ajax_bulk_optimize']);
        add_action('wp_ajax_kafanek_price_analysis', [$this, 'ajax_price_analysis']);
        
        // Auto-update prices if enabled
        if (get_option('kafanek_dynamic_pricing_enabled', false)) {
            add_action('kafanek_daily_pricing_update', [$this, 'auto_update_prices']);
        }
    }
    
    public function add_menu() {
        add_submenu_page(
            'kafanek-brain',
            'Dynamic Pricing',
            'Dynamic Pricing',
            'manage_woocommerce',
            'kafanek-pricing',
            [$this, 'render_page']
        );
    }
    
    public function render_page() {
        if (!class_exists('WooCommerce')) {
            echo '<div class="notice notice-error"><p>WooCommerce must be installed for Dynamic Pricing.</p></div>';
            return;
        }
        ?>
        <div class="wrap kafanek-pricing">
            <h1>💰 Dynamic Pricing Engine</h1>
            <p>AI-powered price optimization using Golden Ratio (φ = <?php echo $this->phi; ?>)</p>
            
            <div class="pricing-tabs">
                <button class="tab-btn active" data-tab="optimizer">Price Optimizer</button>
                <button class="tab-btn" data-tab="analysis">Market Analysis</button>
                <button class="tab-btn" data-tab="psychology">Psychological Pricing</button>
                <button class="tab-btn" data-tab="settings">Settings</button>
            </div>
            
            <!-- Price Optimizer Tab -->
            <div class="tab-content active" id="optimizer">
                <div class="pricing-grid">
                    <div class="card">
                        <h2>🎯 Single Product Optimizer</h2>
                        <div class="form-group">
                            <label>Select Product:</label>
                            <select id="product-select" class="widefat">
                                <option value="">-- Select Product --</option>
                                <?php
                                $products = wc_get_products(['limit' => 100, 'orderby' => 'name']);
                                foreach ($products as $product) {
                                    echo '<option value="' . $product->get_id() . '">' . 
                                         esc_html($product->get_name()) . ' (Current: ' . 
                                         $product->get_price() . ' Kč)</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div id="current-price-info"></div>
                        
                        <button id="optimize-single" class="button button-primary">
                            ✨ Optimize Price
                        </button>
                        
                        <div id="optimization-result"></div>
                    </div>
                    
                    <div class="card">
                        <h2>📊 Bulk Optimization</h2>
                        <p>Optimize all products at once using φ-based algorithms</p>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="bulk-only-sale">
                                Only products without sale price
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="bulk-category" value="">
                                Specific category only
                            </label>
                        </div>
                        
                        <button id="bulk-optimize" class="button button-primary button-hero">
                            🚀 Optimize All Products
                        </button>
                        
                        <div id="bulk-progress"></div>
                    </div>
                    
                    <div class="card stats-card">
                        <h2>📈 Pricing Statistics</h2>
                        <?php echo $this->get_pricing_stats(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Market Analysis Tab -->
            <div class="tab-content" id="analysis">
                <div class="card">
                    <h2>🔍 Market Analysis</h2>
                    <button id="run-analysis" class="button">Run Analysis</button>
                    <div id="analysis-results"></div>
                </div>
            </div>
            
            <!-- Psychological Pricing Tab -->
            <div class="tab-content" id="psychology">
                <div class="card">
                    <h2>🧠 Psychological Pricing Rules</h2>
                    <p>Apply psychological pricing patterns (e.g., 99 Kč instead of 100 Kč)</p>
                    
                    <div class="psych-rules">
                        <label>
                            <input type="checkbox" class="psych-rule" value="charm" checked>
                            <strong>Charm Pricing</strong> - End prices with .99 or .95
                        </label>
                        
                        <label>
                            <input type="checkbox" class="psych-rule" value="prestige">
                            <strong>Prestige Pricing</strong> - Round numbers for luxury (1000 instead of 999)
                        </label>
                        
                        <label>
                            <input type="checkbox" class="psych-rule" value="phi">
                            <strong>Golden Ratio</strong> - Use φ (1.618) for price tiers
                        </label>
                    </div>
                    
                    <button id="apply-psychology" class="button button-primary">
                        Apply Psychological Pricing
                    </button>
                </div>
            </div>
            
            <!-- Settings Tab -->
            <div class="tab-content" id="settings">
                <div class="card">
                    <h2>⚙️ Pricing Settings</h2>
                    <form method="post" action="options.php">
                        <?php settings_fields('kafanek_pricing_settings'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th>Enable Dynamic Pricing</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="kafanek_dynamic_pricing_enabled" value="1" 
                                               <?php checked(get_option('kafanek_dynamic_pricing_enabled'), 1); ?>>
                                        Auto-update prices daily
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>Price Range Limits</th>
                                <td>
                                    Min: <input type="number" name="kafanek_price_min_margin" 
                                               value="<?php echo esc_attr(get_option('kafanek_price_min_margin', 0.8)); ?>" 
                                               step="0.01" style="width: 80px;"> × Base Price<br>
                                    Max: <input type="number" name="kafanek_price_max_margin" 
                                               value="<?php echo esc_attr(get_option('kafanek_price_max_margin', 1.618)); ?>" 
                                               step="0.01" style="width: 80px;"> × Base Price (φ)
                                </td>
                            </tr>
                            <tr>
                                <th>Optimization Strategy</th>
                                <td>
                                    <select name="kafanek_pricing_strategy">
                                        <option value="phi">Golden Ratio (φ = 1.618)</option>
                                        <option value="fibonacci">Fibonacci Sequence</option>
                                        <option value="competitive">Competitive Pricing</option>
                                        <option value="value">Value-Based</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Settings'); ?>
                    </form>
                </div>
            </div>
        </div>
        
        <style>
        .pricing-tabs { display: flex; gap: 10px; margin: 20px 0; border-bottom: 2px solid #ddd; }
        .tab-btn { padding: 10px 20px; border: none; background: transparent; cursor: pointer; border-bottom: 3px solid transparent; }
        .tab-btn.active { border-bottom-color: #667eea; font-weight: bold; }
        .tab-content { display: none; padding: 20px 0; }
        .tab-content.active { display: block; }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .psych-rules label { display: block; padding: 10px; margin: 10px 0; background: #f9fafb; border-radius: 6px; }
        .price-tier { display: flex; justify-content: space-between; padding: 10px; margin: 5px 0; background: #f0f9ff; border-radius: 6px; }
        .price-tier.recommended { background: linear-gradient(135deg, #fef3c7, #fde68a); font-weight: bold; }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            const phi = <?php echo $this->phi; ?>;
            
            // Tab switching
            $('.tab-btn').on('click', function() {
                const tab = $(this).data('tab');
                $('.tab-btn, .tab-content').removeClass('active');
                $(this).addClass('active');
                $('#' + tab).addClass('active');
            });
            
            // Single product optimization
            $('#optimize-single').on('click', function() {
                const productId = $('#product-select').val();
                if (!productId) {
                    alert('Please select a product');
                    return;
                }
                
                $(this).prop('disabled', true).text('⏳ Optimizing...');
                
                $.post(ajaxurl, {
                    action: 'kafanek_optimize_product_price',
                    nonce: '<?php echo wp_create_nonce('kafanek_pricing_nonce'); ?>',
                    product_id: productId
                }, function(response) {
                    if (response.success) {
                        $('#optimization-result').html(response.data.html);
                    } else {
                        alert('Error: ' + response.data);
                    }
                    $('#optimize-single').prop('disabled', false).text('✨ Optimize Price');
                });
            });
            
            // Bulk optimization
            $('#bulk-optimize').on('click', function() {
                if (!confirm('This will optimize prices for multiple products. Continue?')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('⏳ Processing...');
                
                $.post(ajaxurl, {
                    action: 'kafanek_bulk_price_optimize',
                    nonce: '<?php echo wp_create_nonce('kafanek_pricing_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        $('#bulk-progress').html(response.data.summary);
                    }
                    $('#bulk-optimize').prop('disabled', false).text('🚀 Optimize All Products');
                });
            });
            
            // Price analysis
            $('#run-analysis').on('click', function() {
                $(this).prop('disabled', true).text('⏳ Analyzing...');
                
                $.post(ajaxurl, {
                    action: 'kafanek_price_analysis',
                    nonce: '<?php echo wp_create_nonce('kafanek_pricing_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        $('#analysis-results').html(response.data.html);
                    }
                    $('#run-analysis').prop('disabled', false).text('Run Analysis');
                });
            });
        });
        </script>
        <?php
    }
    
    public function ajax_optimize_price() {
        check_ajax_referer('kafanek_pricing_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $product_id = intval($_POST['product_id']);
        $product = wc_get_product($product_id);
        
        if (!$product) {
            wp_send_json_error('Product not found');
        }
        
        $current_price = floatval($product->get_regular_price());
        $optimized_prices = $this->calculate_optimal_prices($current_price, $product);
        
        $html = '<div class="price-variants">';
        $html .= '<h3>💡 Recommended Price Tiers (φ-based):</h3>';
        
        foreach ($optimized_prices as $tier => $data) {
            $recommended = $tier === 'standard' ? ' recommended' : '';
            $html .= '<div class="price-tier' . $recommended . '">';
            $html .= '<span>' . ucfirst($tier) . ':</span>';
            $html .= '<span><strong>' . number_format($data['price'], 2) . ' Kč</strong></span>';
            $html .= '<small>' . $data['description'] . '</small>';
            $html .= '</div>';
        }
        
        $html .= '<button class="button button-primary" onclick="applyPrice(' . $product_id . ', ' . $optimized_prices['standard']['price'] . ')">Apply Standard Price</button>';
        $html .= '</div>';
        
        wp_send_json_success(['html' => $html]);
    }
    
    private function calculate_optimal_prices($base_price, $product) {
        // Golden Ratio pricing tiers
        $budget = $base_price / $this->phi; // Base ÷ φ
        $standard = $base_price; // Base (current)
        $premium = $base_price * $this->phi; // Base × φ
        
        // Apply psychological pricing
        $budget = $this->apply_charm_pricing($budget);
        $premium = $this->apply_charm_pricing($premium);
        
        return [
            'budget' => [
                'price' => $budget,
                'description' => 'Entry-level pricing (÷ φ)'
            ],
            'standard' => [
                'price' => $standard,
                'description' => 'Current base price (recommended)'
            ],
            'premium' => [
                'price' => $premium,
                'description' => 'Premium tier (× φ)'
            ]
        ];
    }
    
    private function apply_charm_pricing($price) {
        // Round to .99 or .95
        $rounded = floor($price);
        return $rounded + 0.99;
    }
    
    public function ajax_bulk_optimize() {
        check_ajax_referer('kafanek_pricing_nonce', 'nonce');
        
        $products = wc_get_products(['limit' => -1]);
        $updated = 0;
        
        foreach ($products as $product) {
            $current = floatval($product->get_regular_price());
            if ($current > 0) {
                $optimal = $this->apply_charm_pricing($current);
                
                // Only update if different
                if (abs($optimal - $current) > 0.5) {
                    $product->set_sale_price($optimal);
                    $product->save();
                    $updated++;
                }
            }
        }
        
        wp_send_json_success([
            'summary' => "<div class='notice notice-success'><p>✅ Optimized {$updated} products using φ-based pricing.</p></div>"
        ]);
    }
    
    public function ajax_price_analysis() {
        check_ajax_referer('kafanek_pricing_nonce', 'nonce');
        
        $products = wc_get_products(['limit' => -1]);
        $total_products = count($products);
        $avg_price = 0;
        $price_distribution = [];
        
        foreach ($products as $product) {
            $price = floatval($product->get_regular_price());
            $avg_price += $price;
            
            $range = floor($price / 100) * 100;
            $price_distribution[$range] = ($price_distribution[$range] ?? 0) + 1;
        }
        
        $avg_price = $avg_price / max($total_products, 1);
        
        $html = '<div class="analysis-report">';
        $html .= '<h3>📊 Market Analysis Results:</h3>';
        $html .= '<p><strong>Total Products:</strong> ' . $total_products . '</p>';
        $html .= '<p><strong>Average Price:</strong> ' . number_format($avg_price, 2) . ' Kč</p>';
        $html .= '<p><strong>Recommended φ Price:</strong> ' . number_format($avg_price * $this->phi, 2) . ' Kč</p>';
        $html .= '</div>';
        
        wp_send_json_success(['html' => $html]);
    }
    
    private function get_pricing_stats() {
        if (!class_exists('WooCommerce')) return '';
        
        $products = wc_get_products(['limit' => -1]);
        $total = count($products);
        $with_sale = 0;
        $total_value = 0;
        
        foreach ($products as $product) {
            if ($product->get_sale_price()) {
                $with_sale++;
            }
            $total_value += floatval($product->get_price()) * intval($product->get_stock_quantity());
        }
        
        return '<ul>
            <li><strong>Total Products:</strong> ' . $total . '</li>
            <li><strong>With Sale Price:</strong> ' . $with_sale . '</li>
            <li><strong>Inventory Value:</strong> ' . number_format($total_value, 2) . ' Kč</li>
            <li><strong>φ Optimized:</strong> ' . get_option('kafanek_phi_optimized_count', 0) . '</li>
        </ul>';
    }
}

// Initialize
if (class_exists('WooCommerce')) {
    new Kafanek_Dynamic_Pricing();
}
