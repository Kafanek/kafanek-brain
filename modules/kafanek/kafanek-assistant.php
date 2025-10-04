<?php
/**
 * Fibonacci Brain - Kafánek Personal Assistant
 * Version: 2.1.0
 * Personalizováno pro pana Kafánka ☕
 */

if (!defined('ABSPATH')) exit;

class Fibonacci_Kafanek_Assistant {
    
    /**
     * 31. GET KAFÁNEK GREETING
     */
    public function get_greeting($request) {
        $hour = intval(date('H'));
        
        if ($hour >= 5 && $hour < 10) {
            $greeting = "🌅 Dobré ráno, pane Kafánku! Začínáme nový den s čerstvou kávou!";
        } elseif ($hour >= 10 && $hour < 14) {
            $greeting = "☀️ Hezké dopoledne, pane Kafánku! Produktivní čas!";
        } elseif ($hour >= 14 && $hour < 18) {
            $greeting = "☕ Dobré odpoledne, pane Kafánku! Jak se daří projekty?";
        } elseif ($hour >= 18 && $hour < 22) {
            $greeting = "🌙 Dobrý večer, pane Kafánku! Čas na večerní kontrolu!";
        } else {
            $greeting = "🌃 Pracujete do noci, pane Kafánku? Obdivuhodné!";
        }
        
        return new WP_REST_Response([
            'success' => true,
            'greeting' => $greeting,
            'time' => current_time('mysql'),
            'quick_stats' => [
                'posts' => wp_count_posts()->publish,
                'pages' => wp_count_posts('page')->publish,
            ]
        ], 200);
    }
    
    /**
     * 32. GET DAILY TIPS
     */
    public function get_daily_tips($request) {
        $tips = [
            '🚀 Zapněte Jetpack Lazy Load pro rychlejší web!',
            '🎯 Použijte Golden Ratio pro optimální ceny',
            '📊 Zkontrolujte dnešní statistiky',
            '🔒 Jetpack Protect chrání před útoky',
            '⚡ Aktivujte caching pro zrychlení',
        ];
        
        return new WP_REST_Response([
            'success' => true,
            'tip_of_the_day' => $tips[array_rand($tips)],
            'all_tips' => $tips,
        ], 200);
    }
    
    /**
     * 33. POST SAVE NOTE
     */
    public function save_note($request) {
        $params = $request->get_json_params();
        $note = sanitize_textarea_field($params['note'] ?? '');
        
        if (empty($note)) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Poznámka je prázdná'
            ], 400);
        }
        
        $notes = get_option('kafanek_notes', []);
        $notes[] = [
            'note' => $note,
            'date' => current_time('mysql'),
        ];
        update_option('kafanek_notes', $notes);
        
        return new WP_REST_Response([
            'success' => true,
            'message' => '✅ Poznámka uložena, pane Kafánku!',
            'total_notes' => count($notes),
        ], 200);
    }
    
    /**
     * 34. GET KAFÁNEK DASHBOARD
     */
    public function get_dashboard($request) {
        return new WP_REST_Response([
            'success' => true,
            'dashboard' => [
                'greeting' => $this->get_greeting($request)->data['greeting'],
                'stats' => [
                    'posts' => wp_count_posts()->publish,
                    'pages' => wp_count_posts('page')->publish,
                    'products' => class_exists('WooCommerce') ? wp_count_posts('product')->publish : 0,
                ],
                'jetpack_status' => class_exists('Jetpack') ? Jetpack::is_connection_ready() : false,
                'quick_actions' => [
                    '/kafanek/tips',
                    '/jetpack/status',
                    '/feeds/generate-all',
                ]
            ]
        ], 200);
    }
}
