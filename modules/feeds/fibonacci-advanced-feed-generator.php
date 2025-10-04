<?php
/**
 * Fibonacci Advanced Feed Generator
 * 
 * AI-optimized product feeds pro:
 * - Google Shopping
 * - Heureka.cz
 * - Zboží.cz
 * - Facebook Catalog
 * - Glami.cz
 * - Árukereső.hu
 * 
 * @package Kolibri_Fibonacci_Brain
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fibonacci_Advanced_Feed_Generator {
    
    private $ai_predictor;
    private $cache_duration = 3600; // 1 hour
    
    private $feed_types = array(
        'google_shopping' => 'Google Shopping XML',
        'heureka' => 'Heureka.cz XML',
        'zbozi' => 'Zboží.cz XML',
        'facebook' => 'Facebook Catalog CSV',
        'glami' => 'Glami.cz XML',
        'arukereso' => 'Árukereső.hu XML',
    );
    
    public function __construct() {
        if (class_exists('Fibonacci_AI_Predictor')) {
            $this->ai_predictor = new Fibonacci_AI_Predictor();
        }
        
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('init', array($this, 'schedule_feed_generation'));
        add_action('fibonacci_generate_feeds', array($this, 'generate_all_feeds_cron'));
        
        error_log("Fibonacci Feed Generator: Initialized ⚡");
    }
    
    public function register_routes() {
        $namespace = 'kolibri-fibonacci/v1';
        
        register_rest_route($namespace, '/feeds/(?P<type>[a-z_]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'generate_feed'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route($namespace, '/feeds/generate-all', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_all_feeds'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/feeds/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_feed_status'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/feeds/(?P<type>[a-z_]+)/optimize', array(
            'methods' => 'POST',
            'callback' => array($this, 'optimize_feed'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
    }
    
    public function generate_feed($request) {
        $type = $request->get_param('type');
        $use_cache = $request->get_param('cache') !== 'false';
        $optimize = $request->get_param('optimize') === 'true';
        
        if (!isset($this->feed_types[$type])) {
            return new WP_REST_Response(['error' => 'Invalid feed type'], 400);
        }
        
        if ($use_cache) {
            $cached = $this->get_cached_feed($type);
            if ($cached) {
                header('Content-Type: ' . $this->get_feed_content_type($type));
                echo $cached;
                exit;
            }
        }
        
        $products = $this->get_products_for_feed($type, $optimize);
        $feed = $this->generate_feed_content($type, $products);
        
        $this->cache_feed($type, $feed);
        
        header('Content-Type: ' . $this->get_feed_content_type($type));
        header('Content-Disposition: attachment; filename="' . $type . '_feed.' . $this->get_feed_extension($type) . '"');
        
        echo $feed;
        exit;
    }
    
    private function generate_feed_content($type, $products) {
        switch ($type) {
            case 'google_shopping': return $this->generate_google_shopping_xml($products);
            case 'heureka': return $this->generate_heureka_xml($products);
            case 'zbozi': return $this->generate_zbozi_xml($products);
            case 'facebook': return $this->generate_facebook_csv($products);
            case 'glami': return $this->generate_glami_xml($products);
            case 'arukereso': return $this->generate_arukereso_xml($products);
            default: return '';
        }
    }
    
    private function get_products_for_feed($feed_type, $optimize = false) {
        if (!class_exists('WC_Product')) return array();
        
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => -1,
            'stock_status' => 'instock',
        ));
        
        $feed_products = array();
        foreach ($products as $product) {
            $product_data = $this->prepare_product_data($product, $feed_type);
            
            if ($optimize && $this->ai_predictor) {
                $product_data = $this->optimize_product_for_feed($product_data, $feed_type);
            }
            
            $feed_products[] = $product_data;
        }
        
        return $feed_products;
    }
    
    private function prepare_product_data($product, $feed_type) {
        return array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'description' => wp_strip_all_tags($product->get_description()),
            'short_description' => wp_strip_all_tags($product->get_short_description()),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'sku' => $product->get_sku(),
            'url' => $product->get_permalink(),
            'image' => wp_get_attachment_url($product->get_image_id()),
            'stock' => $product->get_stock_quantity(),
            'in_stock' => $product->is_in_stock(),
            'categories' => $this->get_product_categories($product),
            'brand' => $this->get_product_brand($product),
            'gtin' => get_post_meta($product->get_id(), '_gtin', true),
            'mpn' => get_post_meta($product->get_id(), '_mpn', true),
            'condition' => 'new',
            'availability' => $this->get_availability($product),
        );
    }
    
    private function optimize_product_for_feed($product_data, $feed_type) {
        $product_data['optimized_title'] = $this->optimize_title($product_data['name'], $feed_type);
        $product_data['optimized_description'] = $this->optimize_description($product_data['description'], $feed_type);
        
        if ($this->ai_predictor) {
            $price_prediction = $this->ai_predictor->predict_optimal_price(array(
                'current_price' => $product_data['price'],
                'product_id' => $product_data['id'],
            ));
            
            $product_data['ai_recommended_price'] = $price_prediction['predicted_price'];
        }
        
        return $product_data;
    }
    
    private function optimize_title($title, $feed_type) {
        $title = wp_strip_all_tags($title);
        $max_lengths = array('google_shopping' => 150, 'heureka' => 100, 'zbozi' => 100, 'facebook' => 200);
        $max_length = isset($max_lengths[$feed_type]) ? $max_lengths[$feed_type] : 150;
        
        if (mb_strlen($title) > $max_length) {
            $title = mb_substr($title, 0, $max_length - 3) . '...';
        }
        
        return $title;
    }
    
    private function optimize_description($description, $feed_type) {
        $description = wp_strip_all_tags($description);
        $description = preg_replace('/\s+/', ' ', $description);
        
        $max_lengths = array('google_shopping' => 5000, 'heureka' => 2000, 'zbozi' => 2000, 'facebook' => 5000);
        $max_length = isset($max_lengths[$feed_type]) ? $max_lengths[$feed_type] : 2000;
        
        if (mb_strlen($description) > $max_length) {
            $description = mb_substr($description, 0, $max_length - 3) . '...';
        }
        
        return $description;
    }
    
    private function generate_google_shopping_xml($products) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', get_bloginfo('name'));
        $channel->addChild('link', home_url());
        
        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars($product['name']));
            $item->addChild('link', $product['url']);
            $item->addChild('g:id', $product['id'], 'http://base.google.com/ns/1.0');
            $item->addChild('g:price', number_format($product['price'], 2) . ' CZK', 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', $product['availability'], 'http://base.google.com/ns/1.0');
            
            if ($product['image']) {
                $item->addChild('g:image_link', $product['image'], 'http://base.google.com/ns/1.0');
            }
        }
        
        return $xml->asXML();
    }
    
    private function generate_heureka_xml($products) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><shop></shop>');
        
        foreach ($products as $product) {
            $item = $xml->addChild('SHOPITEM');
            $item->addChild('ITEM_ID', $product['id']);
            $item->addChild('PRODUCTNAME', htmlspecialchars($product['name']));
            $item->addChild('URL', $product['url']);
            $item->addChild('PRICE_VAT', number_format($product['price'], 2));
            
            if ($product['image']) $item->addChild('IMGURL', $product['image']);
            if ($product['brand']) $item->addChild('MANUFACTURER', $product['brand']);
        }
        
        return $xml->asXML();
    }
    
    private function generate_zbozi_xml($products) {
        return $this->generate_heureka_xml($products); // Similar format
    }
    
    private function generate_facebook_csv($products) {
        $csv = "id,title,description,availability,condition,price,link,image_link,brand\n";
        
        foreach ($products as $product) {
            $row = array(
                $product['id'],
                '"' . str_replace('"', '""', $product['name']) . '"',
                '"' . str_replace('"', '""', $product['short_description']) . '"',
                $product['in_stock'] ? 'in stock' : 'out of stock',
                'new',
                number_format($product['price'], 2) . ' CZK',
                $product['url'],
                $product['image'],
                $product['brand'],
            );
            
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
    
    private function generate_glami_xml($products) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><SHOP></SHOP>');
        
        foreach ($products as $product) {
            $item = $xml->addChild('SHOPITEM');
            $item->addChild('ITEM_ID', $product['id']);
            $item->addChild('PRODUCTNAME', htmlspecialchars($product['name']));
            $item->addChild('URL', $product['url']);
            $item->addChild('IMGURL', $product['image']);
            $item->addChild('PRICE_VAT', number_format($product['price'], 2));
        }
        
        return $xml->asXML();
    }
    
    private function generate_arukereso_xml($products) {
        return $this->generate_heureka_xml($products);
    }
    
    public function generate_all_feeds($request) {
        $results = array();
        
        foreach (array_keys($this->feed_types) as $type) {
            try {
                $products = $this->get_products_for_feed($type, true);
                $feed = $this->generate_feed_content($type, $products);
                $this->cache_feed($type, $feed);
                
                $results[$type] = array(
                    'success' => true,
                    'products_count' => count($products),
                    'size' => strlen($feed),
                    'url' => home_url('/wp-json/kolibri-fibonacci/v1/feeds/' . $type),
                );
            } catch (Exception $e) {
                $results[$type] = array('success' => false, 'error' => $e->getMessage());
            }
        }
        
        return new WP_REST_Response(['success' => true, 'feeds' => $results], 200);
    }
    
    public function get_feed_status($request) {
        $status = array();
        
        foreach (array_keys($this->feed_types) as $type) {
            $cached = $this->get_cached_feed($type);
            
            $status[$type] = array(
                'name' => $this->feed_types[$type],
                'cached' => !empty($cached),
                'cache_size' => $cached ? strlen($cached) : 0,
                'url' => home_url('/wp-json/kolibri-fibonacci/v1/feeds/' . $type),
                'last_generated' => get_option('fibonacci_feed_' . $type . '_generated', 'never'),
            );
        }
        
        return new WP_REST_Response(['success' => true, 'feeds' => $status], 200);
    }
    
    public function optimize_feed($request) {
        $type = $request->get_param('type');
        $products = $this->get_products_for_feed($type, false);
        $optimizations = array();
        
        foreach (array_slice($products, 0, 10) as $product) {
            $suggestions = array();
            
            if (mb_strlen($product['name']) > 100) {
                $suggestions[] = 'Shorten title to improve CTR';
            }
            
            if (empty($product['gtin']) && empty($product['mpn'])) {
                $suggestions[] = 'Add GTIN or MPN for better visibility';
            }
            
            if (!empty($suggestions)) {
                $optimizations[$product['id']] = array(
                    'product' => $product['name'],
                    'suggestions' => $suggestions,
                );
            }
        }
        
        return new WP_REST_Response([
            'success' => true,
            'feed_type' => $type,
            'optimizations' => $optimizations,
        ], 200);
    }
    
    // Helper functions
    private function get_product_categories($product) {
        $terms = get_the_terms($product->get_id(), 'product_cat');
        if (empty($terms)) return array();
        return array_map(function($term) { return $term->name; }, $terms);
    }
    
    private function get_product_brand($product) {
        $brand = get_post_meta($product->get_id(), '_brand', true);
        if ($brand) return $brand;
        
        $terms = get_the_terms($product->get_id(), 'product_brand');
        return !empty($terms) ? $terms[0]->name : '';
    }
    
    private function get_availability($product) {
        if (!$product->is_in_stock()) return 'out of stock';
        $stock = $product->get_stock_quantity();
        return ($stock && $stock < 5) ? 'limited availability' : 'in stock';
    }
    
    private function get_feed_content_type($type) {
        return ($type === 'facebook') ? 'text/csv' : 'application/xml';
    }
    
    private function get_feed_extension($type) {
        return ($type === 'facebook') ? 'csv' : 'xml';
    }
    
    private function cache_feed($type, $content) {
        set_transient('fibonacci_feed_' . $type, $content, $this->cache_duration);
        update_option('fibonacci_feed_' . $type . '_generated', current_time('mysql'));
    }
    
    private function get_cached_feed($type) {
        return get_transient('fibonacci_feed_' . $type);
    }
    
    private function verify_api_key($request) {
        $api_key = $request->get_header('X-API-Key');
        $stored_key = get_option('kolibri_fibonacci_api_key');
        return !empty($api_key) && $api_key === $stored_key;
    }
    
    public function schedule_feed_generation() {
        if (!wp_next_scheduled('fibonacci_generate_feeds')) {
            wp_schedule_event(time(), 'hourly', 'fibonacci_generate_feeds');
        }
    }
    
    public function generate_all_feeds_cron() {
        foreach (array_keys($this->feed_types) as $type) {
            try {
                $products = $this->get_products_for_feed($type, false);
                $feed = $this->generate_feed_content($type, $products);
                $this->cache_feed($type, $feed);
            } catch (Exception $e) {
                error_log("Feed generation failed for {$type}: " . $e->getMessage());
            }
        }
    }
}

// Initialize
new Fibonacci_Advanced_Feed_Generator();
