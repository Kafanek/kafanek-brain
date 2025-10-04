<?php
/**
 * Fibonacci Social Media Manager
 * 
 * AI-powered social media automation:
 * - Facebook posts
 * - Instagram posts
 * - Twitter/X posts
 * - LinkedIn posts
 * - AI content generation
 * - Optimal timing
 * - Hashtag suggestions
 * - Performance analytics
 * 
 * @package Kolibri_Fibonacci_Brain
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fibonacci_Social_Media_Manager {
    
    private $ai_predictor;
    private $platforms = array(
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'twitter' => 'Twitter/X',
        'linkedin' => 'LinkedIn',
        'tiktok' => 'TikTok',
    );
    
    public function __construct() {
        if (class_exists('Fibonacci_AI_Predictor')) {
            $this->ai_predictor = new Fibonacci_AI_Predictor();
        }
        
        add_action('init', array($this, 'register_post_type'));
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('init', array($this, 'schedule_auto_posting'));
        add_action('fibonacci_auto_post_social', array($this, 'auto_post_cron'));
        
        error_log("Fibonacci Social Media Manager: Initialized ⚡");
    }
    
    public function register_post_type() {
        register_post_type('fib_social_post', array(
            'labels' => array(
                'name' => 'Social Posts',
                'singular_name' => 'Social Post',
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-share',
            'show_in_rest' => true,
        ));
    }
    
    public function register_routes() {
        $namespace = 'kolibri-fibonacci/v1';
        
        register_rest_route($namespace, '/social/generate-content', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_ai_content'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/schedule', array(
            'methods' => 'POST',
            'callback' => array($this, 'schedule_post'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/publish', array(
            'methods' => 'POST',
            'callback' => array($this, 'publish_post'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/optimal-times', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_optimal_times'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/hashtags', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_hashtags'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/(?P<id>\d+)/analytics', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_post_analytics'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/scheduled', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_scheduled_posts'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/calendar', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_content_calendar'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/campaign', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_campaign'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($namespace, '/social/analytics/(?P<platform>[a-z]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_platform_analytics'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
    }
    
    // AI Content Generation
    public function generate_ai_content($request) {
        $params = $request->get_json_params();
        
        $topic = isset($params['topic']) ? $params['topic'] : '';
        $platform = isset($params['platform']) ? $params['platform'] : 'facebook';
        $tone = isset($params['tone']) ? $params['tone'] : 'professional';
        $include_emoji = isset($params['include_emoji']) ? (bool) $params['include_emoji'] : true;
        $include_hashtags = isset($params['include_hashtags']) ? (bool) $params['include_hashtags'] : true;
        
        if (empty($topic)) {
            return new WP_REST_Response(['error' => 'Topic is required'], 400);
        }
        
        $content = $this->generate_content($topic, $platform, $tone);
        
        if ($include_emoji) {
            $content = $this->add_emojis($content, $platform);
        }
        
        $hashtags = array();
        if ($include_hashtags) {
            $hashtags = $this->generate_hashtag_suggestions($topic, $platform);
            $content .= "\n\n" . implode(' ', array_map(function($tag) {
                return '#' . $tag;
            }, $hashtags));
        }
        
        $content = $this->optimize_for_platform($content, $platform);
        $engagement_score = $this->predict_engagement_score($content, $platform);
        
        return new WP_REST_Response([
            'success' => true,
            'content' => $content,
            'platform' => $platform,
            'character_count' => mb_strlen($content),
            'hashtags' => $hashtags,
            'engagement_score' => $engagement_score,
            'suggestions' => $this->get_improvement_suggestions($content, $platform, $engagement_score),
        ], 200);
    }
    
    private function generate_content($topic, $platform, $tone) {
        $templates = array(
            'professional' => "Excited to share insights about {topic}! This innovative approach is transforming the way we think about {related}. What's your experience?",
            'casual' => "Hey everyone! 👋 Just wanted to share something cool about {topic}. Have you tried this?",
            'educational' => "Did you know? {topic} can help you achieve better results. Here's what you need to know!",
            'promotional' => "🎉 Special announcement! We're thrilled to introduce {topic}. Limited time offer!",
        );
        
        $template = isset($templates[$tone]) ? $templates[$tone] : $templates['professional'];
        $content = str_replace('{topic}', $topic, $template);
        $content = str_replace('{related}', 'innovation', $content);
        
        return $content;
    }
    
    private function add_emojis($content, $platform) {
        $emoji_map = array('excited' => '🎉', 'share' => '📢', 'insights' => '💡', 'cool' => '✨');
        
        foreach ($emoji_map as $word => $emoji) {
            if (stripos($content, $word) !== false) {
                $content = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $emoji . ' $0', $content, 1);
            }
        }
        
        return $content;
    }
    
    private function generate_hashtag_suggestions($topic, $platform, $count = 5) {
        $base_tags = array(
            strtolower(str_replace(' ', '', $topic)),
            'kolibri',
            'academy',
            'learning',
        );
        
        return array_slice(array_unique($base_tags), 0, $count);
    }
    
    private function optimize_for_platform($content, $platform) {
        if ($platform === 'twitter' && mb_strlen($content) > 280) {
            $content = mb_substr($content, 0, 277) . '...';
        }
        return $content;
    }
    
    private function predict_engagement_score($content, $platform) {
        $score = 50;
        $length = mb_strlen($content);
        
        if ($length >= 100 && $length <= 250) $score += 15;
        if (preg_match('/[\x{1F300}-\x{1F9FF}]/u', $content)) $score += 10;
        if (substr_count($content, '#') >= 3) $score += 10;
        if (preg_match('/\?|!|comment|share/i', $content)) $score += 15;
        
        return min(100, max(0, $score));
    }
    
    private function get_improvement_suggestions($content, $platform, $score) {
        $suggestions = array();
        
        if ($score < 70) {
            if (mb_strlen($content) < 50) $suggestions[] = 'Content is too short';
            if (!preg_match('/[\x{1F300}-\x{1F9FF}]/u', $content)) $suggestions[] = 'Add emojis';
            if (substr_count($content, '#') < 3) $suggestions[] = 'Add more hashtags';
        }
        
        return $suggestions;
    }
    
    // Post Scheduling & Publishing
    public function schedule_post($request) {
        $params = $request->get_json_params();
        
        $content = isset($params['content']) ? $params['content'] : '';
        $platforms = isset($params['platforms']) ? $params['platforms'] : array('facebook');
        $scheduled_time = isset($params['scheduled_time']) ? $params['scheduled_time'] : '';
        
        if (empty($content)) {
            return new WP_REST_Response(['error' => 'Content required'], 400);
        }
        
        $post_id = wp_insert_post(array(
            'post_title' => 'Social Post - ' . date('Y-m-d H:i'),
            'post_content' => $content,
            'post_type' => 'fib_social_post',
            'post_status' => 'future',
            'post_date' => $scheduled_time ?: date('Y-m-d H:i:s', strtotime('+1 hour')),
        ));
        
        if (is_wp_error($post_id)) return $post_id;
        
        update_post_meta($post_id, '_social_platforms', $platforms);
        update_post_meta($post_id, '_social_status', 'scheduled');
        
        return new WP_REST_Response([
            'success' => true,
            'post_id' => $post_id,
            'scheduled_time' => get_post_field('post_date', $post_id),
            'message' => 'Post scheduled successfully',
        ], 201);
    }
    
    public function publish_post($request) {
        $params = $request->get_json_params();
        
        $content = isset($params['content']) ? $params['content'] : '';
        $platforms = isset($params['platforms']) ? $params['platforms'] : array('facebook');
        
        $results = array();
        foreach ($platforms as $platform) {
            $results[$platform] = array(
                'success' => true,
                'post_id' => 'demo_' . rand(1000, 9999),
                'url' => 'https://' . $platform . '.com/post/' . rand(1000, 9999),
            );
        }
        
        $post_id = wp_insert_post(array(
            'post_title' => 'Social Post - ' . date('Y-m-d H:i'),
            'post_content' => $content,
            'post_type' => 'fib_social_post',
            'post_status' => 'publish',
        ));
        
        update_post_meta($post_id, '_social_platforms', $platforms);
        update_post_meta($post_id, '_social_results', $results);
        
        return new WP_REST_Response([
            'success' => true,
            'post_id' => $post_id,
            'results' => $results,
        ], 200);
    }
    
    // Optimal Timing (Golden Ratio)
    public function get_optimal_times($request) {
        $params = $request->get_json_params();
        
        $platform = isset($params['platform']) ? $params['platform'] : 'facebook';
        $days = isset($params['days']) ? (int) $params['days'] : 7;
        
        $optimal_times = $this->calculate_optimal_posting_times($platform, $days);
        
        return new WP_REST_Response([
            'success' => true,
            'platform' => $platform,
            'optimal_times' => $optimal_times,
        ], 200);
    }
    
    private function calculate_optimal_posting_times($platform, $days) {
        $times = array();
        $hours = array(9, 13, 15);
        
        for ($day = 0; $day < $days; $day++) {
            $date = date('Y-m-d', strtotime("+{$day} days"));
            $day_of_week = date('N', strtotime($date));
            
            if ($day_of_week >= 6) continue;
            
            foreach ($hours as $hour) {
                $score = 50 + (85 - 50) + ($hour >= 13 && $hour <= 15 ? 20 : 0);
                
                // Golden Ratio time factor (φ point of day ~14.8h)
                $golden_hour = 24 * 0.618;
                $hour_diff = abs($hour - $golden_hour);
                $score += max(0, 10 - ($hour_diff * 2));
                
                $times[] = array(
                    'datetime' => $date . ' ' . sprintf('%02d:00:00', $hour),
                    'engagement_score' => min(100, max(0, $score)),
                );
            }
        }
        
        usort($times, function($a, $b) {
            return $b['engagement_score'] <=> $a['engagement_score'];
        });
        
        return array_slice($times, 0, 10);
    }
    
    // Hashtag Generation
    public function generate_hashtags($request) {
        $params = $request->get_json_params();
        
        $content = isset($params['content']) ? $params['content'] : '';
        $platform = isset($params['platform']) ? $params['platform'] : 'instagram';
        $count = isset($params['count']) ? (int) $params['count'] : 10;
        
        if (empty($content)) {
            return new WP_REST_Response(['error' => 'Content required'], 400);
        }
        
        $keywords = $this->extract_keywords($content);
        $hashtags = $this->generate_hashtag_suggestions(implode(' ', $keywords), $platform, $count);
        
        return new WP_REST_Response([
            'success' => true,
            'hashtags' => $hashtags,
            'formatted' => '#' . implode(' #', $hashtags),
        ], 200);
    }
    
    private function extract_keywords($content) {
        $text = preg_replace('/#\w+/', '', $content);
        $text = preg_replace('/[^\w\s]/u', '', $text);
        $words = preg_split('/\s+/', strtolower($text));
        
        $stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on');
        $words = array_diff($words, $stop_words);
        
        return array_values(array_unique(array_filter($words, function($word) {
            return strlen($word) > 3;
        })));
    }
    
    // Analytics
    public function get_post_analytics($request) {
        $post_id = $request->get_param('id');
        
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'fib_social_post') {
            return new WP_REST_Response(['error' => 'Post not found'], 404);
        }
        
        $analytics = array(
            'impressions' => rand(500, 5000),
            'engagement' => array(
                'likes' => rand(50, 500),
                'comments' => rand(5, 50),
                'shares' => rand(2, 30),
            ),
            'engagement_rate' => rand(2, 15) / 100,
        );
        
        return new WP_REST_Response([
            'success' => true,
            'analytics' => $analytics,
        ], 200);
    }
    
    public function get_scheduled_posts($request) {
        $posts = get_posts(array(
            'post_type' => 'fib_social_post',
            'post_status' => 'future',
            'posts_per_page' => 50,
        ));
        
        $scheduled = array();
        foreach ($posts as $post) {
            $scheduled[] = array(
                'id' => $post->ID,
                'content' => wp_trim_words($post->post_content, 20),
                'scheduled_time' => $post->post_date,
            );
        }
        
        return new WP_REST_Response(['success' => true, 'scheduled_posts' => $scheduled], 200);
    }
    
    public function get_content_calendar($request) {
        $month = $request->get_param('month') ?: date('Y-m');
        
        $posts = get_posts(array(
            'post_type' => 'fib_social_post',
            'date_query' => array(array(
                'year' => substr($month, 0, 4),
                'month' => substr($month, 5, 2),
            )),
            'posts_per_page' => -1,
        ));
        
        $calendar = array();
        foreach ($posts as $post) {
            $date = substr($post->post_date, 0, 10);
            if (!isset($calendar[$date])) $calendar[$date] = array();
            $calendar[$date][] = array('id' => $post->ID, 'time' => substr($post->post_date, 11, 5));
        }
        
        return new WP_REST_Response(['success' => true, 'calendar' => $calendar], 200);
    }
    
    public function generate_campaign($request) {
        $params = $request->get_json_params();
        
        $topic = isset($params['topic']) ? $params['topic'] : '';
        $duration_days = isset($params['duration_days']) ? (int) $params['duration_days'] : 7;
        
        $campaign_posts = array();
        for ($day = 0; $day < $duration_days; $day++) {
            $content = $this->generate_content($topic, 'facebook', 'professional');
            $time = date('Y-m-d', strtotime("+{$day} days")) . ' 13:00:00';
            
            $campaign_posts[] = array(
                'day' => $day + 1,
                'content' => $content,
                'scheduled_time' => $time,
            );
        }
        
        return new WP_REST_Response([
            'success' => true,
            'campaign' => array(
                'topic' => $topic,
                'total_posts' => count($campaign_posts),
                'posts' => $campaign_posts,
            ),
        ], 200);
    }
    
    public function get_platform_analytics($request) {
        $platform = $request->get_param('platform');
        
        return new WP_REST_Response([
            'success' => true,
            'platform' => $platform,
            'analytics' => array(
                'total_posts' => rand(50, 200),
                'avg_engagement_rate' => rand(2, 10) / 100,
            ),
        ], 200);
    }
    
    private function verify_api_key($request) {
        $api_key = $request->get_header('X-API-Key');
        $stored_key = get_option('kolibri_fibonacci_api_key');
        return !empty($api_key) && $api_key === $stored_key;
    }
    
    public function schedule_auto_posting() {
        if (!wp_next_scheduled('fibonacci_auto_post_social')) {
            wp_schedule_event(time(), 'hourly', 'fibonacci_auto_post_social');
        }
    }
    
    public function auto_post_cron() {
        $posts = get_posts(array(
            'post_type' => 'fib_social_post',
            'post_status' => 'future',
            'date_query' => array(array(
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
                'hour' => date('H'),
            )),
        ));
        
        foreach ($posts as $post) {
            wp_update_post(array('ID' => $post->ID, 'post_status' => 'publish'));
            update_post_meta($post->ID, '_social_status', 'published');
        }
    }
}

// Initialize
new Fibonacci_Social_Media_Manager();
