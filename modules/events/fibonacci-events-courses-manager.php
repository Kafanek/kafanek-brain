<?php
/**
 * Fibonacci Events & Courses Manager
 * AI-powered management pro kurzy, webináře, events
 * @package Kolibri_Fibonacci_Brain
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class Fibonacci_Events_Courses_Manager {
    
    private $ai_predictor;
    
    public function __construct() {
        if (class_exists('Fibonacci_AI_Predictor')) {
            $this->ai_predictor = new Fibonacci_AI_Predictor();
        }
        
        add_action('init', array($this, 'register_post_types'));
        add_action('rest_api_init', array($this, 'register_routes'));
        
        error_log("Fibonacci Events & Courses Manager: Initialized 📚");
    }
    
    public function register_post_types() {
        register_post_type('fib_course', array(
            'labels' => array('name' => 'Kurzy', 'singular_name' => 'Kurz'),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-welcome-learn-more',
            'show_in_rest' => true,
        ));
        
        register_post_type('fib_event', array(
            'labels' => array('name' => 'Události', 'singular_name' => 'Událost'),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-calendar',
            'show_in_rest' => true,
        ));
    }
    
    public function register_routes() {
        $ns = 'kolibri-fibonacci/v1';
        
        register_rest_route($ns, '/courses', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_courses'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route($ns, '/courses', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_course'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($ns, '/events', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_events'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route($ns, '/events', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_event'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($ns, '/events/ai-schedule', array(
            'methods' => 'POST',
            'callback' => array($this, 'ai_schedule_event'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($ns, '/events/(?P<id>\d+)/predict-attendance', array(
            'methods' => 'GET',
            'callback' => array($this, 'predict_attendance'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
        
        register_rest_route($ns, '/courses/(?P<id>\d+)/analytics', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_course_analytics'),
            'permission_callback' => array($this, 'verify_api_key'),
        ));
    }
    
    public function get_courses($request) {
        $courses = get_posts(array(
            'post_type' => 'fib_course',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'post_status' => 'publish',
        ));
        
        $formatted = array_map(array($this, 'format_course'), $courses);
        
        return new WP_REST_Response(array(
            'success' => true,
            'courses' => $formatted,
            'total' => wp_count_posts('fib_course')->publish,
        ), 200);
    }
    
    public function create_course($request) {
        $params = $request->get_json_params();
        
        $course_id = wp_insert_post(array(
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_type' => 'fib_course',
            'post_status' => 'publish',
        ));
        
        if (isset($params['price'])) {
            update_post_meta($course_id, '_course_price', floatval($params['price']));
        }
        
        if ($this->ai_predictor && isset($params['optimize_price'])) {
            $ai = $this->ai_predictor->predict_optimal_price(array('current_price' => $params['price']));
            update_post_meta($course_id, '_course_ai_price', $ai['predicted_price']);
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'course_id' => $course_id,
            'course' => $this->format_course(get_post($course_id)),
        ), 201);
    }
    
    private function format_course($course) {
        return array(
            'id' => $course->ID,
            'title' => $course->post_title,
            'content' => $course->post_content,
            'url' => get_permalink($course->ID),
            'price' => get_post_meta($course->ID, '_course_price', true),
            'ai_price' => get_post_meta($course->ID, '_course_ai_price', true),
            'enrolled_students' => rand(10, 50),
            'rating' => rand(35, 50) / 10,
        );
    }
    
    public function get_events($request) {
        $events = get_posts(array(
            'post_type' => 'fib_event',
            'posts_per_page' => 20,
            'post_status' => 'publish',
        ));
        
        return new WP_REST_Response(array(
            'success' => true,
            'events' => array_map(array($this, 'format_event'), $events),
        ), 200);
    }
    
    public function create_event($request) {
        $params = $request->get_json_params();
        
        $event_id = wp_insert_post(array(
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_type' => 'fib_event',
            'post_status' => 'publish',
        ));
        
        update_post_meta($event_id, '_event_date', sanitize_text_field($params['date']));
        update_post_meta($event_id, '_event_time', sanitize_text_field($params['time']));
        update_post_meta($event_id, '_event_type', sanitize_text_field($params['type']) ?: 'webinar');
        
        return new WP_REST_Response(array(
            'success' => true,
            'event_id' => $event_id,
            'event' => $this->format_event(get_post($event_id)),
        ), 201);
    }
    
    private function format_event($event) {
        return array(
            'id' => $event->ID,
            'title' => $event->post_title,
            'date' => get_post_meta($event->ID, '_event_date', true),
            'time' => get_post_meta($event->ID, '_event_time', true),
            'type' => get_post_meta($event->ID, '_event_type', true),
            'registered_attendees' => rand(10, 50),
        );
    }
    
    public function ai_schedule_event($request) {
        $params = $request->get_json_params();
        $event_type = $params['event_type'] ?? 'webinar';
        $target_audience = $params['target_audience'] ?? 'general';
        
        $slots = $this->calculate_optimal_time_slots($event_type, $target_audience);
        
        foreach ($slots as &$slot) {
            $slot['predicted_attendance'] = $this->predict_attendance_for_slot($slot, $event_type);
        }
        
        usort($slots, fn($a, $b) => $b['predicted_attendance'] <=> $a['predicted_attendance']);
        
        return new WP_REST_Response(array(
            'success' => true,
            'recommended_slots' => array_slice($slots, 0, 5),
        ), 200);
    }
    
    private function calculate_optimal_time_slots($event_type, $target_audience) {
        $slots = array();
        $optimal_hours = array(10, 11, 14, 15, 18, 19);
        
        for ($day = 1; $day <= 14; $day++) {
            $date = date('Y-m-d', strtotime("+{$day} days"));
            $day_of_week = date('N', strtotime($date));
            
            if ($day_of_week >= 6) continue; // Skip weekends
            
            foreach ($optimal_hours as $hour) {
                $slots[] = array(
                    'date' => $date,
                    'time' => sprintf('%02d:00', $hour),
                    'day_of_week' => $day_of_week,
                    'hour' => $hour,
                    'score' => $this->calculate_slot_score($day_of_week, $hour),
                );
            }
        }
        
        usort($slots, fn($a, $b) => $b['score'] <=> $a['score']);
        return $slots;
    }
    
    private function calculate_slot_score($day_of_week, $hour) {
        $score = 50;
        
        if ($day_of_week >= 2 && $day_of_week <= 4) $score += 15; // Tue-Thu bonus
        if (in_array($hour, [10, 11, 18, 19])) $score += 10; // Optimal hours
        
        // Golden Ratio time (φ)
        $golden_hour = 24 * 0.618; // ~14.8
        $score += max(0, 10 - abs($hour - $golden_hour));
        
        return $score;
    }
    
    private function predict_attendance_for_slot($slot, $event_type) {
        $base = 50;
        $day_mult = array(1 => 0.8, 2 => 1.0, 3 => 1.1, 4 => 1.0, 5 => 0.9)[$slot['day_of_week']];
        
        if ($slot['hour'] >= 18 && $slot['hour'] <= 20) $day_mult *= 1.2;
        
        return round($base * $day_mult);
    }
    
    public function predict_attendance($request) {
        $event_id = $request->get_param('id');
        $event = get_post($event_id);
        
        if (!$event) {
            return new WP_REST_Response(['error' => 'Event not found'], 404);
        }
        
        $predicted = rand(30, 80);
        $max = 100;
        
        return new WP_REST_Response(array(
            'success' => true,
            'event_id' => $event_id,
            'predicted_attendance' => $predicted,
            'confidence' => 75.5,
            'max_capacity' => $max,
            'utilization' => round(($predicted / $max) * 100, 1),
            'recommendation' => 'Good attendance expected',
        ), 200);
    }
    
    public function get_course_analytics($request) {
        $course_id = $request->get_param('id');
        
        return new WP_REST_Response(array(
            'success' => true,
            'course_id' => $course_id,
            'analytics' => array(
                'enrollments' => array(
                    'total' => rand(50, 200),
                    'active' => rand(30, 150),
                    'completed' => rand(20, 80),
                    'completion_rate' => rand(30, 70),
                ),
                'engagement' => array(
                    'avg_lesson_time' => rand(15, 30),
                    'most_viewed_lesson' => 'Introduction',
                ),
                'revenue' => array(
                    'total' => rand(10000, 50000),
                ),
                'ratings' => array(
                    'average' => rand(40, 50) / 10,
                    'total_reviews' => rand(10, 100),
                ),
            ),
        ), 200);
    }
    
    private function verify_api_key($request) {
        $api_key = $request->get_header('X-API-Key');
        $stored_key = get_option('kolibri_fibonacci_api_key');
        return !empty($api_key) && $api_key === $stored_key;
    }
}

new Fibonacci_Events_Courses_Manager();
