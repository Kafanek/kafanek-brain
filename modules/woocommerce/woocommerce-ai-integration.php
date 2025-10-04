{{ ... }}
    /**
     * Calculate match percentage between products using ML
     */
    private function calculate_match_percentage($product1_id, $product2_id) {
        // Use Fibonacci caching for performance
        $cache_key = "match_{$product1_id}_{$product2_id}";
        
        return kafanek_fibonacci_cache($cache_key, function() use ($product1_id, $product2_id) {
            // Extract features using helper
            $features1 = Kafanek_Core_Helpers::extract_product_features($product1_id);
            $features2 = Kafanek_Core_Helpers::extract_product_features($product2_id);
            
            if (empty($features1) || empty($features2)) {
                return 0;
            }
            
            // Calculate cosine similarity
            $similarity = Kafanek_Core_Helpers::cosine_similarity($features1, $features2);
            
            // Convert to percentage
            return round($similarity * 100, 2);
        }, 'hourly'); // Cache for 21 minutes (Fibonacci)
    }
{{ ... }}
    /**
     * Dynamic pricing hook
     */
    public function dynamic_pricing($price, $product) {
        $product_id = $product->get_id();
        
        // Check if dynamic pricing is enabled for this product
        $enabled = get_post_meta($product_id, '_kafanek_dynamic_pricing', true);
        if ($enabled !== 'yes') {
            return $price;
        }
        
        // Use Fibonacci caching - recalculate every 5 seconds
        $cache_key = "dynamic_price_{$product_id}";
        
        return kafanek_fibonacci_cache($cache_key, function() use ($price, $product_id, $product) {
            // Get demand score from helper
            $demand_score = Kafanek_Core_Helpers::calculate_demand_score($product_id);
            
            // Get time factor from helper
            $time_factor = Kafanek_Core_Helpers::get_time_factor();
            
            // Calculate stock factor
            $stock = $product->get_stock_quantity();
            $stock_factor = 1.0;
            if ($stock !== null && $stock < 5) {
                $stock_factor = 1.05; // Low stock premium
            }
            
            // Demand multiplier based on score
            $demand_multiplier = 1.0;
            if ($demand_score > 50) {
                $demand_multiplier = 1.1; // High demand +10%
            } elseif ($demand_score < 10) {
                $demand_multiplier = 0.9; // Low demand -10%
            }
            
            // Calculate new price
            $new_price = $price * $demand_multiplier * $stock_factor * (1 + ($time_factor * 0.05));
            
            // Golden Ratio rounding
            $new_price = Kafanek_Core_Helpers::golden_ratio_price_round($new_price);
            
            // Ensure within ±10% of base price
            $min_price = $price * 0.9;
            $max_price = $price * 1.1;
            $new_price = max($min_price, min($max_price, $new_price));
            
            return $new_price;
        }, 'standard'); // Cache for 5 seconds (Fibonacci)
    }
{{ ... }}
    /**
     * AJAX: Optimize product price
     */
    public function ajax_optimize_price() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        if (!current_user_can('edit_products')) {
            wp_send_json_error(['message' => 'Nedostatečná oprávnění']);
        }
        
        $product_id = intval($_POST['product_id'] ?? 0);
        
        if (!$product_id) {
            wp_send_json_error(['message' => 'Neplatné ID produktu']);
        }
        
        // Use helper to calculate optimal price
        $optimal_price = Kafanek_Core_Helpers::calculate_optimal_price($product_id);
        
        $product = wc_get_product($product_id);
        $current_price = $product->get_price();
        
        $increase_percentage = (($optimal_price / $current_price) - 1) * 100;
        
        wp_send_json_success([
            'current_price' => $current_price,
            'optimal_price' => $optimal_price,
            'increase_percentage' => round($increase_percentage, 2),
            'formatted_current' => wc_price($current_price),
            'formatted_optimal' => wc_price($optimal_price),
            'recommendation' => $increase_percentage > 0 ? 'Zvýšit cenu' : 'Snížit cenu'
        ]);
    }
{{ ... }}
    /**
     * Get Fibonacci-based recommendations
     */
    private function get_fibonacci_recommendations($product_id, $limit = 5) {
        $product = wc_get_product($product_id);
        if (!$product) return [];
        
        // Get related products based on categories
        $categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
        
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $limit * 2,
            'post__not_in' => [$product_id],
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $categories,
                ]
            ],
            'orderby' => 'rand'
        ];
        
        $products = get_posts($args);
        
        // Score and sort by AI similarity
        $scored_products = [];
        foreach ($products as $p) {
            $score = $this->calculate_match_percentage($product_id, $p->ID);
            $scored_products[$p->ID] = $score;
        }
        
        arsort($scored_products);
        return array_slice(array_keys($scored_products), 0, $limit);
    }
{{ ... }}
    /**
     * Calculate demand score
     */
    private function calculate_demand_score($product_id) {
        global $wpdb;
        
        // Get recent views (last 7 days)
        $views = get_post_meta($product_id, '_kafanek_views_7d', true) ?: 0;
        
        // Get recent sales
        $sales = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_order_items oi
            JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
            JOIN {$wpdb->prefix}posts p ON oi.order_id = p.ID
            WHERE oim.meta_key = '_product_id' 
            AND oim.meta_value = %d
            AND p.post_date > DATE_SUB(NOW(), INTERVAL 7 DAY)
        ", $product_id));
        
        return ($views * 0.3) + ($sales * 0.7);
    }
{{ ... }}
    /**
     * Get time of day factor
     */
    private function get_time_factor() {
        $hour = (int) current_time('G');
        
        // Peak hours 10-12, 18-20
        if (($hour >= 10 && $hour <= 12) || ($hour >= 18 && $hour <= 20)) {
            return 1.0;
        }
        
        return 0.0;
    }
{{ ... }}
    /**
     * Helper: Get Fibonacci number
     */
    private function get_fibonacci_number($n) {
        if ($n <= 1) return $n;
        
        $fib = [0, 1];
        for ($i = 2; $i <= $n; $i++) {
            $fib[$i] = $fib[$i - 1] + $fib[$i - 2];
        }
        
        return $fib[$n];
    }
{{ ... }}
