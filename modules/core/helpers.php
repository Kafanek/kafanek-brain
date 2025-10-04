<?php
/**
 * Kafánek Brain - Core Helper Functions
 * Centralizované helper funkce pro všechny moduly
 * 
 * @package Kafanek_Brain
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Core_Helpers {
    
    /**
     * Fibonacci cache levels (in seconds)
     */
    private static $fibonacci_cache_levels = [
        'instant' => 1,      // 1 second
        'quick' => 1,        // 1 second  
        'short' => 2,        // 2 seconds
        'medium' => 3,       // 3 seconds
        'standard' => 5,     // 5 seconds
        'long' => 8,         // 8 seconds
        'extended' => 13,    // 13 seconds
        'hourly' => 21 * 60, // 21 minutes
        'daily' => 34 * 3600 // 34 hours
    ];
    
    /**
     * Fibonacci sequence generator
     * Generuje Fibonacci číslo na pozici n
     * 
     * @param int $n Pozice v sekvenci
     * @return int Fibonacci číslo
     */
    public static function get_fibonacci_number($n) {
        if ($n <= 0) return 0;
        if ($n == 1) return 1;
        
        $fib = [0, 1];
        for ($i = 2; $i <= $n; $i++) {
            $fib[$i] = $fib[$i-1] + $fib[$i-2];
        }
        
        return $fib[$n];
    }
    
    /**
     * Generate Fibonacci sequence up to n numbers
     * 
     * @param int $count Počet čísel
     * @return array Fibonacci sekvence
     */
    public static function get_fibonacci_sequence($count) {
        if ($count <= 0) return [];
        if ($count == 1) return [0];
        
        $sequence = [0, 1];
        for ($i = 2; $i < $count; $i++) {
            $sequence[$i] = $sequence[$i-1] + $sequence[$i-2];
        }
        
        return $sequence;
    }
    
    /**
     * Cosine similarity pro dva vektory
     * Používá se pro ML similarity matching
     * 
     * @param array $vec1 První vektor
     * @param array $vec2 Druhý vektor
     * @return float Similarity score (0-1)
     */
    public static function cosine_similarity($vec1, $vec2) {
        if (empty($vec1) || empty($vec2)) return 0;
        if (count($vec1) !== count($vec2)) return 0;
        
        $dot_product = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        for ($i = 0; $i < count($vec1); $i++) {
            $dot_product += $vec1[$i] * $vec2[$i];
            $magnitude1 += pow($vec1[$i], 2);
            $magnitude2 += pow($vec2[$i], 2);
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 * $magnitude2 == 0) {
            return 0;
        }
        
        return $dot_product / ($magnitude1 * $magnitude2);
    }
    
    /**
     * Extract product features for ML
     * Extrahuje numerické vlastnosti produktu pro ML algoritmy
     * 
     * @param int $product_id WooCommerce product ID
     * @return array Feature vector
     */
    public static function extract_product_features($product_id) {
        if (!class_exists('WooCommerce')) {
            return [];
        }
        
        $product = wc_get_product($product_id);
        if (!$product) return [];
        
        // Základní features
        $features = [
            'price' => (float) $product->get_price() ?: 0,
            'rating' => (float) $product->get_average_rating() ?: 0,
            'review_count' => (int) $product->get_review_count() ?: 0,
            'stock' => (int) $product->get_stock_quantity() ?: 0,
            'sales' => (int) get_post_meta($product_id, 'total_sales', true) ?: 0,
        ];
        
        // Kategorie (první kategorie ID)
        $categories = $product->get_category_ids();
        $features['category_id'] = !empty($categories) ? $categories[0] : 0;
        
        // Rozměry (pokud existují)
        $features['weight'] = (float) $product->get_weight() ?: 0;
        $features['length'] = (float) $product->get_length() ?: 0;
        $features['width'] = (float) $product->get_width() ?: 0;
        $features['height'] = (float) $product->get_height() ?: 0;
        
        // Normalizace do vektoru
        return array_values($features);
    }
    
    /**
     * Generate golden ratio sequence
     * 
     * @param int $length Length of sequence
     * @return array Golden ratio sequence
     */
    public static function generate_golden_sequence($length) {
        $sequence = [1];
        for ($i = 1; $i < $length; $i++) {
            $sequence[] = round($sequence[$i-1] * KAFANEK_BRAIN_PHI);
        }
        return $sequence;
    }
    
    /**
     * Generate golden grid for layouts
     * Pro Elementor layouty
     * 
     * @param int $columns Number of columns
     * @return array Column widths in percentages
     */
    public static function generate_golden_grid($columns) {
        $grid = [];
        $total = 100;
        $phi_inverse = 1 / KAFANEK_BRAIN_PHI; // 0.618
        
        for ($i = 0; $i < $columns; $i++) {
            if ($i === $columns - 1) {
                $grid[] = $total;
            } else {
                $width = round($total * $phi_inverse, 2);
                $grid[] = $width;
                $total -= $width;
            }
        }
        
        return $grid;
    }
    
    /**
     * Golden Ratio calculator
     * Aplikuje Golden Ratio (φ = 1.618) na hodnotu
     * 
     * @param float $value Vstupní hodnota
     * @param string $operation multiply|divide|inverse
     * @return float Výsledná hodnota
     */
    public static function golden_ratio($value, $operation = 'multiply') {
        $phi = KAFANEK_BRAIN_PHI; // 1.618033988749895
        
        switch($operation) {
            case 'multiply':
                return $value * $phi;
            case 'divide':
                return $value / $phi;
            case 'inverse':
                return $value * (1 / $phi); // 0.618
            case 'square':
                return $value * ($phi * $phi); // φ²
            default:
                return $value;
        }
    }
    
    /**
     * Golden Ratio price rounding
     * Zaokrouhlí cenu podle Golden Ratio pro psychologickou optimalizaci
     * 
     * @param float $price Původní cena
     * @return float Zaokrouhlená cena
     */
    public static function golden_ratio_price_round($price) {
        $phi = KAFANEK_BRAIN_PHI;
        
        // Zaokrouhli na nejbližší Golden Ratio násobek
        $base = round($price / $phi);
        $rounded = $base * $phi;
        
        // Pokud je rozdíl příliš velký, použij standardní zaokrouhlení
        if (abs($rounded - $price) > ($price * 0.05)) {
            return round($price, 2);
        }
        
        return round($rounded, 2);
    }
    
    /**
     * Calculate demand score
     * Vypočítá demand score produktu na základě views a sales
     * 
     * @param int $product_id Product ID
     * @return float Demand score
     */
    public static function calculate_demand_score($product_id) {
        global $wpdb;
        
        // Get recent views (last 7 days)
        $views = (int) get_post_meta($product_id, '_kafanek_views_7d', true) ?: 0;
        
        // Get recent sales (last 7 days)
        $sales = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_order_items oi
            JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
            JOIN {$wpdb->prefix}posts p ON oi.order_id = p.ID
            WHERE oim.meta_key = '_product_id' 
            AND oim.meta_value = %d
            AND p.post_date > DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND p.post_status IN ('wc-completed', 'wc-processing')
        ", $product_id)) ?: 0;
        
        // Weighted score: views 30%, sales 70%
        return ($views * 0.3) + ($sales * 0.7);
    }
    
    /**
     * Normalize vector
     * Normalizuje vektor na rozsah 0-1
     * 
     * @param array $vector Vstupní vektor
     * @return array Normalizovaný vektor
     */
    public static function normalize_vector($vector) {
        if (empty($vector)) return [];
        
        $min = min($vector);
        $max = max($vector);
        $range = $max - $min;
        
        if ($range == 0) return array_fill(0, count($vector), 0);
        
        return array_map(function($val) use ($min, $range) {
            return ($val - $min) / $range;
        }, $vector);
    }
    
    /**
     * Calculate Euclidean distance
     * Vypočítá Euklidovskou vzdálenost mezi dvěma vektory
     * 
     * @param array $vec1 První vektor
     * @param array $vec2 Druhý vektor
     * @return float Distance
     */
    public static function euclidean_distance($vec1, $vec2) {
        if (count($vec1) !== count($vec2)) return INF;
        
        $sum = 0;
        for ($i = 0; $i < count($vec1); $i++) {
            $sum += pow($vec1[$i] - $vec2[$i], 2);
        }
        
        return sqrt($sum);
    }
    
    /**
     * Sigmoid activation function
     * Pro neural network aktivaci
     * 
     * @param float $x Vstup
     * @return float Výstup (0-1)
     */
    public static function sigmoid($x) {
        return 1 / (1 + exp(-$x));
    }
    
    /**
     * ReLU activation function
     * 
     * @param float $x Vstup
     * @return float Výstup
     */
    public static function relu($x) {
        return max(0, $x);
    }
    
    /**
     * Sanitize AI prompt
     * Očistí a připraví prompt pro AI API
     * 
     * @param string $prompt Surový prompt
     * @return string Sanitizovaný prompt
     */
    public static function sanitize_ai_prompt($prompt) {
        // Remove HTML tags
        $prompt = strip_tags($prompt);
        
        // Remove extra whitespace
        $prompt = preg_replace('/\s+/', ' ', $prompt);
        
        // Trim
        $prompt = trim($prompt);
        
        // Limit length (max 4000 chars for most AI APIs)
        if (strlen($prompt) > 4000) {
            $prompt = substr($prompt, 0, 4000) . '...';
        }
        
        return $prompt;
    }
    
    /**
     * Format price Czech style
     * Formátuje cenu v českém stylu (1 234,50 Kč)
     * 
     * @param float $price Cena
     * @param bool $include_currency Zahrnout měnu
     * @return string Formátovaná cena
     */
    public static function format_price_czech($price, $include_currency = true) {
        $formatted = number_format($price, 2, ',', ' ');
        
        if ($include_currency) {
            $formatted .= ' Kč';
        }
        
        return $formatted;
    }
    
    /**
     * Log to Kafánek debug
     * Logování pro debugging
     * 
     * @param string $message Zpráva
     * @param string $level info|warning|error
     * @param array $context Kontext
     */
    public static function log($message, $level = 'info', $context = []) {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $log_entry = [
            'time' => current_time('mysql'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        error_log('[Kafánek Brain ' . strtoupper($level) . '] ' . $message);
        
        // Store in database for admin viewing
        $logs = get_option('kafanek_brain_debug_logs', []);
        $logs[] = $log_entry;
        
        // Keep only last 100 logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        update_option('kafanek_brain_debug_logs', $logs, false);
    }
    
    /**
     * Get time factor for dynamic pricing
     * Vrací faktor podle času dne (peak hours)
     * 
     * @return float Factor (0.0 - 1.0)
     */
    public static function get_time_factor() {
        $hour = (int) current_time('G');
        
        // Peak hours: 10-12, 18-20
        if (($hour >= 10 && $hour <= 12) || ($hour >= 18 && $hour <= 20)) {
            return 1.0;
        }
        
        // Medium traffic: 8-10, 12-18, 20-22
        if (($hour >= 8 && $hour < 10) || ($hour > 12 && $hour < 18) || ($hour > 20 && $hour <= 22)) {
            return 0.5;
        }
        
        // Low traffic: night hours
        return 0.0;
    }
    
    /**
     * Calculate optimal price
     * Vypočítá optimální cenu na základě několika faktorů
     * 
     * @param int $product_id Product ID
     * @param array $options Možnosti kalkulace
     * @return float Optimální cena
     */
    public static function calculate_optimal_price($product_id, $options = []) {
        $product = wc_get_product($product_id);
        if (!$product) return 0;
        
        $base_price = (float) $product->get_regular_price();
        $current_price = (float) $product->get_price();
        
        // Demand score
        $demand_score = self::calculate_demand_score($product_id);
        
        // Demand multiplier (0.9 - 1.1)
        $demand_multiplier = 1.0;
        if ($demand_score > 50) {
            $demand_multiplier = 1.1; // High demand, increase 10%
        } elseif ($demand_score < 10) {
            $demand_multiplier = 0.9; // Low demand, decrease 10%
        }
        
        // Stock multiplier
        $stock = $product->get_stock_quantity();
        $stock_multiplier = 1.0;
        if ($stock !== null && $stock < 5) {
            $stock_multiplier = 1.05; // Low stock, slight increase
        }
        
        // Time factor
        $time_multiplier = 1.0 + (self::get_time_factor() * 0.05);
        
        // Calculate optimal
        $optimal_price = $base_price * $demand_multiplier * $stock_multiplier * $time_multiplier;
        
        // Golden ratio rounding
        $optimal_price = self::golden_ratio_price_round($optimal_price);
        
        // Limit to ±15% from base price
        $min_price = $base_price * 0.85;
        $max_price = $base_price * 1.15;
        
        return max($min_price, min($max_price, $optimal_price));
    }
    
    /**
     * Generate cache key
     * Generuje cache key pro různé typy dat
     * 
     * @param string $type Typ cache
     * @param array $params Parametry
     * @return string Cache key
     */
    public static function generate_cache_key($type, $params = []) {
        $key_parts = [$type];
        
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = md5(serialize($value));
            }
            $key_parts[] = $key . '_' . $value;
        }
        
        return 'kafanek_' . implode('_', $key_parts);
    }
    
    /**
     * Get cached or compute
     * Zkusí získat z cache, jinak vypočítá a uloží
     * 
     * @param string $cache_key Cache key
     * @param callable $callback Funkce pro výpočet
     * @param int $expiration Expirace v sekundách
     * @return mixed Výsledek
     */
    public static function get_cached_or_compute($cache_key, $callback, $expiration = 3600) {
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $result = call_user_func($callback);
        
        set_transient($cache_key, $result, $expiration);
        
        return $result;
    }
    
    /**
     * Fibonacci caching - get cached or compute
     * 
     * @param string $cache_key Cache key
     * @param callable $callback Function to compute if not cached
     * @param string $level Cache level (instant, quick, short, medium, standard, long, extended, hourly, daily)
     * @return mixed Result
     */
    public static function fibonacci_cache($cache_key, $callback, $level = 'standard') {
        $expiration = self::$fibonacci_cache_levels[$level] ?? 5;
        
        $cached = get_transient('kafanek_' . $cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $result = call_user_func($callback);
        
        set_transient('kafanek_' . $cache_key, $result, $expiration);
        
        return $result;
    }
    
    /**
     * Retry with Fibonacci delays
     * Pro API volání s exponenciálním backoff
     * 
     * @param callable $callback Function to retry
     * @param array $args Arguments for callback
     * @param int $max_retries Maximum retry attempts
     * @return mixed Result or false on failure
     */
    public static function retry_with_fibonacci($callback, $args = [], $max_retries = 3) {
        $fibonacci = [1, 1, 2, 3, 5, 8];
        
        for ($i = 0; $i <= $max_retries; $i++) {
            try {
                $result = call_user_func_array($callback, $args);
                
                if ($result !== false) {
                    return $result;
                }
            } catch (Exception $e) {
                self::log(
                    "Retry {$i}/{$max_retries}: " . $e->getMessage(),
                    'warning',
                    ['attempt' => $i, 'max' => $max_retries]
                );
                
                if ($i < $max_retries) {
                    sleep($fibonacci[$i] ?? 8);
                }
            }
        }
        
        return false;
    }
}

// Initialize helper
if (!function_exists('kafanek_helpers')) {
    function kafanek_helpers() {
        return Kafanek_Core_Helpers::class;
    }
}

// Quick access functions
if (!function_exists('kafanek_fibonacci_cache')) {
    function kafanek_fibonacci_cache($key, $callback, $level = 'standard') {
        return Kafanek_Core_Helpers::fibonacci_cache($key, $callback, $level);
    }
}

if (!function_exists('kafanek_golden_ratio')) {
    function kafanek_golden_ratio($value, $operation = 'multiply') {
        return Kafanek_Core_Helpers::golden_ratio($value, $operation);
    }
}
