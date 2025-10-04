<?php
/**
 * Fibonacci Brain - Golden Ratio Optimizer
 * Version: 2.3.0
 * Optimalizace pomocí zlatého řezu
 */

if (!defined('ABSPATH')) exit;

class Fibonacci_Golden_Ratio_Optimizer {
    
    private $phi;
    
    public function __construct() {
        $this->phi = KOLIBRI_FIB_PHI; // 1.618033988749895
    }
    
    /**
     * 57. POST OPTIMIZE WITH GOLDEN RATIO
     * Optimalizuje libovolnou hodnotu podle φ
     */
    public function optimize($request) {
        $params = $request->get_json_params();
        $value = floatval($params['value'] ?? 0);
        $type = sanitize_text_field($params['type'] ?? 'multiply');
        
        if ($value <= 0) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Hodnota musí být kladná'
            ], 400);
        }
        
        $optimized = $this->apply_golden_ratio($value, $type);
        
        return new WP_REST_Response([
            'success' => true,
            'original' => $value,
            'optimized' => $optimized,
            'type' => $type,
            'phi' => $this->phi,
            'improvement' => $this->calculate_improvement($value, $optimized),
            'message' => "✨ Optimalizováno zlatým řezem, pane Kafánku!",
        ], 200);
    }
    
    /**
     * Apply golden ratio transformation
     */
    private function apply_golden_ratio($value, $type) {
        switch ($type) {
            case 'multiply':
                return $value * $this->phi;
                
            case 'divide':
                return $value / $this->phi;
                
            case 'power':
                return $value * pow($this->phi, 2);
                
            case 'fibonacci':
                return $this->nearest_fibonacci($value);
                
            case 'proportion':
                // Split value into golden ratio proportions
                return [
                    'larger' => $value * ($this->phi / ($this->phi + 1)),
                    'smaller' => $value * (1 / ($this->phi + 1))
                ];
                
            default:
                return $value * $this->phi;
        }
    }
    
    /**
     * Find nearest Fibonacci number
     */
    private function nearest_fibonacci($value) {
        $fib = [1, 1];
        
        while (end($fib) < $value * 2) {
            $next = $fib[count($fib) - 1] + $fib[count($fib) - 2];
            $fib[] = $next;
        }
        
        $nearest = $fib[0];
        $min_diff = abs($value - $nearest);
        
        foreach ($fib as $f) {
            $diff = abs($value - $f);
            if ($diff < $min_diff) {
                $min_diff = $diff;
                $nearest = $f;
            }
        }
        
        return $nearest;
    }
    
    /**
     * Calculate improvement percentage
     */
    private function calculate_improvement($original, $optimized) {
        if (is_array($optimized)) {
            return '61.8% / 38.2% split (φ proportion)';
        }
        
        $change = (($optimized - $original) / $original) * 100;
        return round($change, 1) . '%';
    }
    
    /**
     * Optimize price using golden ratio
     */
    public function optimize_price($base_price) {
        $variants = [
            'basic' => $base_price,
            'standard' => round($base_price * $this->phi, 2),
            'premium' => round($base_price * pow($this->phi, 2), 2),
            'elite' => round($base_price * pow($this->phi, 3), 2),
        ];
        
        return [
            'prices' => $variants,
            'recommended' => $variants['standard'],
            'reasoning' => 'Standard price = base * φ pro optimální konverzi',
        ];
    }
    
    /**
     * Optimize content length
     */
    public function optimize_content_length($min_words, $max_words) {
        $optimal = round($min_words * $this->phi);
        $extended = round($optimal * $this->phi);
        
        // Ensure within bounds
        $optimal = min($optimal, $max_words);
        $extended = min($extended, $max_words);
        
        return [
            'min' => $min_words,
            'optimal' => $optimal,
            'extended' => $extended,
            'max' => $max_words,
            'recommendation' => "Ideální délka: $optimal slov (φ proportion)",
        ];
    }
    
    /**
     * Optimize time intervals (for publishing, caching, etc.)
     */
    public function optimize_time_interval($base_minutes) {
        $fibonacci_times = $this->generate_fibonacci_sequence(10);
        
        // Find nearest Fibonacci time
        $optimal_minutes = $this->nearest_fibonacci($base_minutes);
        
        return [
            'base' => $base_minutes,
            'optimal' => $optimal_minutes,
            'fibonacci_sequence' => $fibonacci_times,
            'recommendation' => "Použijte $optimal_minutes minut (Fibonacci číslo)",
        ];
    }
    
    /**
     * Generate Fibonacci sequence
     */
    private function generate_fibonacci_sequence($count) {
        $fib = [1, 1];
        
        for ($i = 2; $i < $count; $i++) {
            $fib[] = $fib[$i - 1] + $fib[$i - 2];
        }
        
        return $fib;
    }
    
    /**
     * Weighted average using Fibonacci weights
     */
    public function fibonacci_weighted_average($data) {
        if (empty($data)) {
            return 0;
        }
        
        $fib_weights = $this->generate_fibonacci_sequence(count($data));
        $total_weight = array_sum($fib_weights);
        $weighted_sum = 0;
        
        foreach ($data as $i => $value) {
            $weighted_sum += $value * ($fib_weights[$i] / $total_weight);
        }
        
        return $weighted_sum;
    }
    
    /**
     * Detect golden ratio patterns in data
     */
    public function detect_phi_patterns($data) {
        $patterns = [];
        
        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i - 1] == 0) continue;
            
            $ratio = $data[$i] / $data[$i - 1];
            
            // Check if ratio is close to φ
            if (abs($ratio - $this->phi) < 0.1) {
                $patterns[] = [
                    'index' => $i,
                    'ratio' => round($ratio, 3),
                    'type' => 'golden_ratio',
                    'confidence' => round((1 - abs($ratio - $this->phi)) * 100, 1),
                    'values' => [$data[$i - 1], $data[$i]],
                ];
            }
        }
        
        return [
            'patterns_found' => count($patterns),
            'patterns' => $patterns,
            'phi_presence' => count($patterns) > 0 
                ? 'Zlatý řez detekován! 🎯' 
                : 'Žádný zlatý řez nenalezen',
        ];
    }
    
    /**
     * Optimize number to golden ratio
     */
    public function round_to_phi($number) {
        // Find closest number that has φ relationship
        $lower = $number / $this->phi;
        $higher = $number * $this->phi;
        
        // Check which is closer to a round number
        $lower_rounded = round($lower);
        $higher_rounded = round($higher);
        
        if (abs($lower - $lower_rounded) < abs($higher - $higher_rounded)) {
            return [
                'original' => $number,
                'optimized' => $lower_rounded * $this->phi,
                'type' => 'φ multiple',
            ];
        } else {
            return [
                'original' => $number,
                'optimized' => $higher_rounded / $this->phi,
                'type' => 'φ divisor',
            ];
        }
    }
}
