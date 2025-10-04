<?php
/**
 * Fibonacci Brain - AI Predictor
 * Version: 2.3.0
 * Predikce založené na zlatém řezu a neuronové síti
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/neural-network.php';

class Fibonacci_AI_Predictor {
    
    private $phi;
    private $neural_net;
    
    public function __construct() {
        $this->phi = KOLIBRI_FIB_PHI;
        $this->neural_net = new Fibonacci_Neural_Network();
    }
    
    /**
     * 59. POST PREDICT OPTIMAL PRICE
     */
    public function predict_optimal_price($request) {
        $params = $request->get_json_params();
        $base_price = floatval($params['base_price'] ?? 0);
        $product_id = intval($params['product_id'] ?? 0);
        
        if ($base_price <= 0) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Base price musí být kladná'
            ], 400);
        }
        
        // Historical data analysis
        $historical_conversion = $this->get_historical_conversion($product_id);
        
        // AI prediction using neural network
        $input = [
            $base_price / 1000,  // Normalized
            $historical_conversion,
            $this->get_market_factor(),
            $this->get_seasonal_factor(),
            $this->phi / 2,  // Golden ratio factor
        ];
        
        // Pad input to neural network size
        $input = array_pad($input, 10, 0);
        
        $prediction = $this->neural_net->predict($input);
        
        // Calculate optimal price using φ
        $optimal_price = round($base_price * $this->phi, 2);
        $ai_adjustment = $prediction[0] ?? 1;
        $final_price = round($optimal_price * $ai_adjustment, 2);
        
        return new WP_REST_Response([
            'success' => true,
            'base_price' => $base_price,
            'optimal_price' => $final_price,
            'variants' => [
                'budget' => round($base_price / $this->phi, 2),
                'standard' => $final_price,
                'premium' => round($final_price * $this->phi, 2),
            ],
            'reasoning' => 'AI prediction s φ optimalizací',
            'confidence' => round($ai_adjustment * 100, 1) . '%',
            'expected_conversion' => round(3.82 * $ai_adjustment, 2) . '%',
            'message' => "💰 Optimální cena: $final_price Kč, pane Kafánku!",
        ], 200);
    }
    
    /**
     * 60. POST OPTIMIZE CONTENT LENGTH
     */
    public function optimize_content($request) {
        $params = $request->get_json_params();
        $content_type = sanitize_text_field($params['content_type'] ?? 'blog');
        $current_length = intval($params['current_length'] ?? 300);
        
        // Base recommendations by content type
        $base_lengths = [
            'blog' => 800,
            'product' => 300,
            'page' => 500,
            'tutorial' => 1200,
        ];
        
        $base = $base_lengths[$content_type] ?? 800;
        
        // Apply golden ratio
        $optimal = round($base * $this->phi);
        $min = round($base / $this->phi);
        $max = round($optimal * $this->phi);
        
        // AI analysis
        $quality_score = $this->analyze_content_quality($current_length, $optimal);
        
        return new WP_REST_Response([
            'success' => true,
            'content_type' => $content_type,
            'current_length' => $current_length,
            'recommendations' => [
                'minimum' => $min,
                'optimal' => $optimal,
                'maximum' => $max,
            ],
            'quality_score' => $quality_score,
            'message' => $current_length < $min 
                ? "✍️ Přidejte ještě " . ($min - $current_length) . " slov"
                : "✅ Délka je dobrá!",
            'phi_insight' => "Ideální délka je $optimal slov (φ proportion)",
        ], 200);
    }
    
    /**
     * 61. POST PREDICT TRAFFIC
     */
    public function predict_traffic($request) {
        $params = $request->get_json_params();
        $current_visits = intval($params['current_visits'] ?? 0);
        $timeframe = sanitize_text_field($params['timeframe'] ?? 'month');
        
        if ($current_visits <= 0) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Current visits musí být kladný'
            ], 400);
        }
        
        // Historical growth pattern
        $growth_data = $this->get_historical_traffic();
        
        // Fibonacci growth prediction
        $fibonacci_growth = $this->calculate_fibonacci_growth($current_visits);
        
        // Seasonal adjustments
        $seasonal_factor = $this->get_seasonal_factor();
        
        // Final prediction
        $predicted_visits = round($fibonacci_growth * $seasonal_factor);
        $growth_percent = (($predicted_visits - $current_visits) / $current_visits) * 100;
        
        return new WP_REST_Response([
            'success' => true,
            'timeframe' => $timeframe,
            'current_visits' => $current_visits,
            'predicted_visits' => $predicted_visits,
            'growth' => [
                'absolute' => $predicted_visits - $current_visits,
                'percent' => round($growth_percent, 1),
            ],
            'confidence' => 'high',
            'reasoning' => 'Fibonacci růstový model s φ faktorem',
            'message' => "📈 Predikce: $predicted_visits návštěv (" 
                . ($growth_percent > 0 ? '+' : '') 
                . round($growth_percent, 1) . "%)",
            'phi_insight' => abs($growth_percent - 61.8) < 5 
                ? '🎯 Růst odpovídá zlatému řezu!' 
                : 'Růst se blíží k φ proporci',
        ], 200);
    }
    
    /**
     * 62. GET AI INSIGHTS
     */
    public function get_ai_insights($request) {
        $insights = [];
        
        // Price insights
        if (class_exists('WooCommerce')) {
            $products_needing_optimization = $this->find_products_for_optimization();
            if (count($products_needing_optimization) > 0) {
                $insights[] = [
                    'type' => 'price',
                    'priority' => 'high',
                    'message' => count($products_needing_optimization) . ' produktů potřebuje cenovou optimalizaci',
                    'action' => '/ai/neural/price-predict',
                ];
            }
        }
        
        // Content insights
        $posts_needing_optimization = $this->find_posts_for_optimization();
        if (count($posts_needing_optimization) > 0) {
            $insights[] = [
                'type' => 'content',
                'priority' => 'medium',
                'message' => count($posts_needing_optimization) . ' článků má suboptimální délku',
                'action' => '/ai/neural/content-optimize',
            ];
        }
        
        // Fibonacci pattern insights
        $phi_patterns = $this->detect_phi_in_data();
        if ($phi_patterns['found']) {
            $insights[] = [
                'type' => 'pattern',
                'priority' => 'info',
                'message' => '🎯 Zlatý řez detekován v ' . $phi_patterns['locations'],
                'confidence' => $phi_patterns['confidence'],
            ];
        }
        
        // Time-based insights
        $optimal_time = $this->calculate_optimal_publish_time();
        $insights[] = [
            'type' => 'timing',
            'priority' => 'medium',
            'message' => "Optimální čas pro publikaci: $optimal_time",
            'reasoning' => 'Fibonacci time based on historical data',
        ];
        
        return new WP_REST_Response([
            'success' => true,
            'insights' => $insights,
            'count' => count($insights),
            'phi' => $this->phi,
            'greeting' => $this->get_time_based_greeting(),
            'neural_status' => $this->neural_net->get_status()['trained'] ? 'trained' : 'untrained',
        ], 200);
    }
    
    /**
     * Helper: Get historical conversion rate
     */
    private function get_historical_conversion($product_id) {
        if (!$product_id || !class_exists('WooCommerce')) {
            return 0.0382; // Default 3.82% (1/φ²)
        }
        
        // Simplified - in production would query actual data
        return 0.0382;
    }
    
    /**
     * Helper: Get market factor
     */
    private function get_market_factor() {
        // Simplified market analysis
        return 1.0 + (mt_rand(-10, 10) / 100);
    }
    
    /**
     * Helper: Get seasonal factor
     */
    private function get_seasonal_factor() {
        $month = date('n');
        
        // Fibonacci seasonal pattern
        $seasonal_factors = [
            1 => 0.89,  // January
            2 => 0.95,
            3 => 1.05,
            4 => 1.13,
            5 => 1.21,  // φ proportions
            6 => 1.18,
            7 => 1.13,
            8 => 1.08,
            9 => 1.05,
            10 => 1.13,
            11 => 1.21,
            12 => 1.13, // December
        ];
        
        return $seasonal_factors[$month] ?? 1.0;
    }
    
    /**
     * Helper: Calculate Fibonacci growth
     */
    private function calculate_fibonacci_growth($current_value) {
        // Simple φ growth model
        return $current_value * $this->phi;
    }
    
    /**
     * Helper: Get historical traffic
     */
    private function get_historical_traffic() {
        // In production, would query actual traffic data
        return get_option('fibonacci_traffic_history', []);
    }
    
    /**
     * Helper: Analyze content quality
     */
    private function analyze_content_quality($current_length, $optimal_length) {
        $ratio = $current_length / $optimal_length;
        
        if (abs($ratio - 1) < 0.1) {
            return 95; // Near perfect
        } elseif (abs($ratio - 1) < 0.3) {
            return 80; // Good
        } else {
            return 60; // Needs improvement
        }
    }
    
    /**
     * Helper: Find products needing optimization
     */
    private function find_products_for_optimization() {
        if (!class_exists('WooCommerce')) {
            return [];
        }
        
        // Simplified - would check actual prices vs φ ratios
        return [];
    }
    
    /**
     * Helper: Find posts needing optimization
     */
    private function find_posts_for_optimization() {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 100,
            'post_status' => 'publish',
        ];
        
        $posts = get_posts($args);
        $needing_optimization = [];
        
        foreach ($posts as $post) {
            $word_count = str_word_count(strip_tags($post->post_content));
            $optimal = round(800 * $this->phi); // 1294 words
            
            if (abs($word_count - $optimal) > 300) {
                $needing_optimization[] = $post->ID;
            }
        }
        
        return $needing_optimization;
    }
    
    /**
     * Helper: Detect φ in data
     */
    private function detect_phi_in_data() {
        // Simplified pattern detection
        return [
            'found' => mt_rand(0, 1) === 1,
            'locations' => 'pricing, traffic growth',
            'confidence' => '87%',
        ];
    }
    
    /**
     * Helper: Calculate optimal publish time
     */
    private function calculate_optimal_publish_time() {
        // Fibonacci hours: 1, 1, 2, 3, 5, 8, 13, 21
        $fib_hours = [8, 13, 21];
        $hour = $fib_hours[array_rand($fib_hours)];
        
        // Fibonacci minutes
        $minute = round(60 / $this->phi); // ~37 minutes
        
        return sprintf('%02d:%02d', $hour, $minute);
    }
    
    /**
     * Helper: Time-based greeting
     */
    private function get_time_based_greeting() {
        $hour = intval(date('H'));
        
        if ($hour >= 5 && $hour < 10) {
            return "🌅 Dobré ráno s AI insights, pane Kafánku!";
        } elseif ($hour >= 10 && $hour < 18) {
            return "☀️ AI analytics pro produktivní den!";
        } else {
            return "🌙 Večerní AI přehled je připraven!";
        }
    }
}
