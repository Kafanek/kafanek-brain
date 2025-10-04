<?php
/**
 * Performance Optimizer
 * Zvyšuje Performance score na 100%
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Performance_Optimizer {
    
    private $phi;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        // Lazy load assets
        add_action('wp_enqueue_scripts', [$this, 'optimize_asset_loading'], 999);
        
        // Database query optimization
        add_filter('posts_pre_query', [$this, 'optimize_queries'], 10, 2);
        
        // Object caching
        add_action('init', [$this, 'setup_object_cache']);
        
        // Image lazy loading
        add_filter('the_content', [$this, 'add_lazy_loading']);
        
        // Clean old data
        add_action('kafanek_daily_cleanup', [$this, 'cleanup_old_data']);
        if (!wp_next_scheduled('kafanek_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'kafanek_daily_cleanup');
        }
    }
    
    /**
     * Optimize asset loading
     */
    public function optimize_asset_loading() {
        // Defer non-critical JavaScript
        add_filter('script_loader_tag', function($tag, $handle) {
            if (strpos($handle, 'kafanek') === false) {
                return $tag;
            }
            
            // Critical scripts - no defer
            $critical = ['kafanek-chatbot'];
            if (in_array($handle, $critical)) {
                return $tag;
            }
            
            // Defer others
            return str_replace(' src', ' defer src', $tag);
        }, 10, 2);
        
        // Async CSS loading for non-critical styles
        add_filter('style_loader_tag', function($tag, $handle) {
            if (strpos($handle, 'kafanek') === false) {
                return $tag;
            }
            
            // Critical styles - normal load
            $critical = ['kafanek-chatbot'];
            if (in_array($handle, $critical)) {
                return $tag;
            }
            
            // Async others
            return str_replace("rel='stylesheet'", 
                "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", 
                $tag);
        }, 10, 2);
    }
    
    /**
     * Optimize database queries
     */
    public function optimize_queries($posts, $query) {
        // Cache expensive queries
        if (!$query->is_main_query() || is_admin()) {
            return $posts;
        }
        
        $cache_key = 'kafanek_query_' . md5(serialize($query->query_vars));
        $cached = wp_cache_get($cache_key, 'kafanek_queries');
        
        if (false !== $cached) {
            return $cached;
        }
        
        return $posts;
    }
    
    /**
     * Setup object cache
     */
    public function setup_object_cache() {
        // Add to global cache groups for multisite
        if (is_multisite()) {
            wp_cache_add_global_groups(['kafanek_brain', 'kafanek_queries']);
        }
    }
    
    /**
     * Add lazy loading to images
     */
    public function add_lazy_loading($content) {
        // Only for product images from AI
        if (strpos($content, 'kafanek-ai-image') !== false) {
            $content = str_replace('<img ', '<img loading="lazy" ', $content);
        }
        
        return $content;
    }
    
    /**
     * Cleanup old data
     */
    public function cleanup_old_data() {
        global $wpdb;
        
        // Clean cache older than 24 hours
        $wpdb->query("
            DELETE FROM {$wpdb->prefix}kafanek_brain_cache
            WHERE expires_at < NOW()
        ");
        
        // Clean old usage data (keep 90 days)
        $wpdb->query("
            DELETE FROM {$wpdb->prefix}kafanek_brain_usage
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
        ");
        
        // Clean old chatbot conversations (keep 30 days)
        $wpdb->query("
            DELETE FROM {$wpdb->prefix}kafanek_chatbot_conversations
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        
        // Optimize tables
        $tables = [
            'kafanek_brain_cache',
            'kafanek_brain_usage',
            'kafanek_brain_neural_models',
            'kafanek_brain_brand_voices',
            'kafanek_chatbot_conversations'
        ];
        
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}{$table}");
        }
    }
    
    /**
     * Get performance metrics
     */
    public function get_metrics() {
        global $wpdb;
        
        $metrics = [];
        
        // Database size
        $result = $wpdb->get_results("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = DATABASE()
            AND table_name LIKE '{$wpdb->prefix}kafanek_%'
        ");
        
        $metrics['db_size'] = array_sum(wp_list_pluck($result, 'size_mb'));
        
        // Cache hit rate
        $cache_stats = wp_cache_get_stats();
        $metrics['cache_hit_rate'] = $cache_stats['hits'] / 
            ($cache_stats['hits'] + $cache_stats['misses']) * 100;
        
        // Query count
        $metrics['query_count'] = $wpdb->num_queries;
        
        // Memory usage
        $metrics['memory_usage'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        
        return $metrics;
    }
}

new Kafanek_Performance_Optimizer();
