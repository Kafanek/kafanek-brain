<?php
/**
 * Kafánek MailPoet AI Integration
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_MailPoet_Integration {
    
    private $mailpoet_api;
    private $ai_engine;
    private $phi;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        if (!class_exists('\MailPoet\API\API')) {
            add_action('admin_notices', [$this, 'mailpoet_required_notice']);
            return;
        }
        
        try {
            $this->mailpoet_api = \MailPoet\API\API::MP('v1');
        } catch (Exception $e) {
            return;
        }
        
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        $this->ai_engine = new Kafanek_AI_Engine();
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_menu_pages'], 100);
        add_action('wp_ajax_kafanek_mailpoet_generate_subject', [$this, 'ajax_generate_subject']);
        add_action('wp_ajax_kafanek_mailpoet_test_subject', [$this, 'ajax_test_subject']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    public function mailpoet_required_notice() {
        echo '<div class="notice notice-error"><p><strong>Kafánek Email Genius:</strong> Vyžaduje aktivní MailPoet plugin.</p></div>';
    }
    
    public function add_menu_pages() {
        add_submenu_page(
            'mailpoet-newsletters',
            'AI Assistant',
            '🤖 AI Assistant',
            'manage_options',
            'kafanek-mailpoet-ai',
            [$this, 'render_dashboard']
        );
    }
    
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'kafanek-mailpoet') === false) return;
        
        wp_localize_script('jquery', 'kafanekMailPoet', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kafanek_mailpoet'),
            'phi' => $this->phi
        ]);
    }
    
    public function render_dashboard() {
        require_once dirname(__FILE__) . '/mailpoet-dashboard.php';
    }
    
    public function ajax_generate_subject() {
        check_ajax_referer('kafanek_mailpoet', 'nonce');
        
        $context = sanitize_textarea_field($_POST['context']);
        
        $triggers = [
            'curiosity' => 'Zajímavý, vzbudí zvědavost',
            'urgency' => 'Naléhavost',
            'benefit' => 'Jasný přínos',
            'personal' => 'Osobní',
            'question' => 'Otázka',
            'number' => 'Číslo'
        ];
        
        $variants = [];
        
        foreach ($triggers as $type => $desc) {
            $prompt = "Vytvoř email subject line v češtině. Kontext: {$context}. Styl: {$desc}. Max 50 znaků:";
            
            $subject = $this->ai_engine->generate_text($prompt, ['max_tokens' => 50, 'temperature' => 0.8]);
            $subject = trim(str_replace(['"', "'", "\n"], '', $subject));
            
            $variants[] = [
                'subject' => $subject,
                'type' => $type,
                'predicted_open_rate' => $this->predict_open_rate($subject),
                'emoji' => $this->suggest_emoji($type),
                'score' => $this->score_subject($subject)
            ];
        }
        
        usort($variants, function($a, $b) {
            return $b['predicted_open_rate'] <=> $a['predicted_open_rate'];
        });
        
        wp_send_json_success($variants);
    }
    
    private function predict_open_rate($subject) {
        $score = 20;
        $length = strlen($subject);
        
        if ($length >= 30 && $length <= 50) $score += 15;
        if (preg_match('/\?|!/', $subject)) $score += 10;
        if (preg_match('/\d/', $subject)) $score += 8;
        
        $score += rand(10, 25);
        return min($score, 95);
    }
    
    private function suggest_emoji($type) {
        $emojis = [
            'curiosity' => '🤔',
            'urgency' => '⏰',
            'benefit' => '🎁',
            'personal' => '👋',
            'question' => '❓',
            'number' => '📊'
        ];
        return $emojis[$type] ?? '';
    }
    
    private function score_subject($subject) {
        $score = 50;
        $length = strlen($subject);
        
        if ($length >= 30 && $length <= 50) $score += 20;
        if (str_word_count($subject) >= 4 && str_word_count($subject) <= 8) $score += 10;
        if (preg_match('/[!?]/', $subject)) $score += 10;
        
        return min(max($score, 0), 100);
    }
    
    public function ajax_test_subject() {
        check_ajax_referer('kafanek_mailpoet', 'nonce');
        
        $subject = sanitize_text_field($_POST['subject']);
        
        $analysis = [
            'predicted_open_rate' => $this->predict_open_rate($subject),
            'score' => $this->score_subject($subject),
            'length' => strlen($subject),
            'word_count' => str_word_count($subject),
            'suggestions' => []
        ];
        
        if ($analysis['length'] < 30) {
            $analysis['suggestions'][] = 'Subject line je příliš krátký (30-50 znaků ideální).';
        }
        if (!preg_match('/[!?]/', $subject)) {
            $analysis['suggestions'][] = 'Zvažte přidání otázky (?) nebo vykřičníku (!).';
        }
        
        wp_send_json_success($analysis);
    }
}

new Kafanek_MailPoet_Integration();
