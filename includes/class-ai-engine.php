<?php
/**
 * AI Engine - Multi-provider support (OpenAI + Claude + Gemini + Azure)
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_AI_Engine {
    
    private $api_key;
    private $provider; // 'openai', 'claude', 'gemini'
    private $azure_key;
    private $azure_region;
    
    public function __construct() {
        $this->provider = get_option('kafanek_ai_provider', 'openai');
        $this->api_key = $this->get_api_key();
        $this->azure_key = get_option('kafanek_azure_speech_key', '');
        $this->azure_region = get_option('kafanek_azure_region', 'westeurope');
        
        // Register AJAX handlers
        add_action('wp_ajax_kafanek_generate_text', [$this, 'ajax_generate_text']);
        add_action('wp_ajax_kafanek_analyze_content', [$this, 'ajax_analyze_content']);
        add_action('wp_ajax_kafanek_speech_to_text', [$this, 'ajax_speech_to_text']);
    }
    
    private function get_api_key() {
        if ($this->provider === 'claude') {
            if (defined('KAFANEK_CLAUDE_API_KEY')) {
                return KAFANEK_CLAUDE_API_KEY;
            }
            return get_option('kafanek_claude_api_key', '');
        }
        
        if ($this->provider === 'gemini') {
            if (defined('KAFANEK_GEMINI_API_KEY')) {
                return KAFANEK_GEMINI_API_KEY;
            }
            return get_option('kafanek_gemini_api_key', '');
        }
        
        // OpenAI (default)
        if (defined('KAFANEK_OPENAI_API_KEY')) {
            return KAFANEK_OPENAI_API_KEY;
        }
        return get_option('kafanek_brain_api_key', '');
    }
    
    /**
     * Generate text using selected provider
     */
    public function generate_text($prompt, $options = []) {
        if (empty($this->api_key)) {
            return "Chyba: API klíč není nastaven pro " . $this->provider;
        }
        
        switch ($this->provider) {
            case 'claude':
                return $this->generate_text_claude($prompt, $options);
            case 'gemini':
                return $this->generate_text_gemini($prompt, $options);
            default:
                return $this->generate_text_openai($prompt, $options);
        }
    }
    
    /**
     * Generate text using OpenAI API
     */
    private function generate_text_openai($prompt, $options = []) {
        $model = $options['model'] ?? 'gpt-3.5-turbo';
        $temperature = $options['temperature'] ?? 0.7;
        $max_tokens = $options['max_tokens'] ?? 500;
        
        // Check cache first
        $cache_key = 'kafanek_ai_' . md5($prompt . $model);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $temperature,
                'max_tokens' => $max_tokens
            ]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return "Chyba spojení: " . $response->get_error_message();
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return "OpenAI chyba: " . $body['error']['message'];
        }
        
        $result = $body['choices'][0]['message']['content'] ?? '';
        
        // Cache for 21 minutes (Fibonacci level)
        set_transient($cache_key, $result, 21 * MINUTE_IN_SECONDS);
        
        $this->log_usage($prompt, $result, $body['usage'] ?? [], 'openai');
        
        return $result;
    }
    
    /**
     * Generate text using Claude API (Anthropic)
     */
    private function generate_text_claude($prompt, $options = []) {
        $model = $options['model'] ?? 'claude-3-5-sonnet-20241022';
        $temperature = $options['temperature'] ?? 0.7;
        $max_tokens = $options['max_tokens'] ?? 1024;
        
        // Check cache first
        $cache_key = 'kafanek_ai_' . md5($prompt . $model);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => $model,
                'max_tokens' => $max_tokens,
                'temperature' => $temperature,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return "Chyba spojení: " . $response->get_error_message();
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return "Claude chyba: " . $body['error']['message'];
        }
        
        $result = $body['content'][0]['text'] ?? '';
        
        // Cache for 21 minutes (Fibonacci level)
        set_transient($cache_key, $result, 21 * MINUTE_IN_SECONDS);
        
        $this->log_usage($prompt, $result, [
            'input_tokens' => $body['usage']['input_tokens'] ?? 0,
            'output_tokens' => $body['usage']['output_tokens'] ?? 0,
            'total_tokens' => ($body['usage']['input_tokens'] ?? 0) + ($body['usage']['output_tokens'] ?? 0)
        ], 'claude');
        
        return $result;
    }
    
    /**
     * Generate product description
     */
    public function generate_product_description($product_id) {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return ['error' => 'Product not found'];
        }
        
        // Get custom instructions from settings
        $custom_instructions = get_option('kafanek_custom_instructions', '');
        $brand_voice = get_option('kafanek_brand_voice', 'profesionální a přátelský');
        $target_audience = get_option('kafanek_target_audience', 'dospělí zákazníci');
        
        // Get examples from successful generations (learning from history)
        $examples = $this->get_best_examples($product->get_category_ids());
        
        $prompt = sprintf(
            "Jsi expert na copywriting pro e-commerce. Vytvoř prodejní popis pro produkt.
            
            BRAND VOICE: %s
            CÍLOVÁ SKUPINA: %s
            
            PRODUKT:
            Název: %s
            Kategorie: %s
            Cena: %s Kč
            
            POŽADAVKY:
            - 150-200 slov
            - Zdůrazni výhody a benefity
            - Použij emocionální a prodejní jazyk
            - Přidej silný call-to-action
            - SEO optimalizace s přirozeným použitím klíčových slov
            - Piš v 2. osobě (oslovuj zákazníka přímo)
            %s
            
            INSPIRACE Z ÚSPĚŠNÝCH POPISŮ:
            %s
            
            Začni přímo textem, bez úvodu.",
            $brand_voice,
            $target_audience,
            $product->get_name(),
            implode(', ', wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names'])),
            $product->get_price(),
            $custom_instructions ? "\n            VLASTNÍ INSTRUKCE: " . $custom_instructions : '',
            $examples
        );
        
        $description = $this->generate_text($prompt);
        
        // Generate short description
        $short_prompt = "Shrň následující prodejní text do 1-2 stručných, ale poutavých vět. Zachovej prodejní tón: " . $description;
        $short_description = $this->generate_text($short_prompt, ['max_tokens' => 100]);
        
        // Save successful generation for future learning
        $this->save_successful_example($product_id, $description, $product->get_category_ids());
        
        return [
            'description' => $description,
            'short_description' => $short_description
        ];
    }
    
    /**
     * Get best examples from previous generations
     */
    private function get_best_examples($category_ids) {
        global $wpdb;
        
        // Get top 2 successful descriptions from same category
        $category_meta = !empty($category_ids) ? get_term_meta($category_ids[0], 'kafanek_example_description', true) : '';
        
        if ($category_meta) {
            return "- " . $category_meta;
        }
        
        return "Zatím nejsou dostupné příklady.";
    }
    
    /**
     * Save successful example for future learning
     */
    private function save_successful_example($product_id, $description, $category_ids) {
        // Save to first category as example (if user doesn't modify it = success)
        if (!empty($category_ids)) {
            $current_examples = get_term_meta($category_ids[0], 'kafanek_example_descriptions', true) ?: [];
            
            // Keep only last 3 examples
            $current_examples[] = substr($description, 0, 200);
            $current_examples = array_slice($current_examples, -3);
            
            update_term_meta($category_ids[0], 'kafanek_example_descriptions', $current_examples);
        }
    }
    
    /**
     * Analyze content for SEO
     */
    public function analyze_content($content, $keywords = []) {
        $prompt = sprintf(
            "Analyzuj následující obsah pro SEO:
            
            Obsah: %s
            
            Klíčová slova: %s
            
            Poskytni:
            1. SEO skóre (0-100)
            2. Doporučení pro zlepšení
            3. Chybějící klíčová slova
            4. Optimální meta description",
            substr($content, 0, 2000),
            implode(', ', $keywords)
        );
        
        $analysis = $this->generate_text($prompt, ['temperature' => 0.3]);
        
        return $this->parse_seo_analysis($analysis);
    }
    
    /**
     * Parse SEO analysis response
     */
    private function parse_seo_analysis($analysis) {
        // Simple parsing - in production use better parsing
        preg_match('/skóre[:\s]+(\d+)/i', $analysis, $score_match);
        $score = $score_match[1] ?? 50;
        
        return [
            'score' => intval($score),
            'analysis' => $analysis,
            'recommendations' => $this->extract_recommendations($analysis)
        ];
    }
    
    /**
     * Extract recommendations from analysis
     */
    private function extract_recommendations($text) {
        $recommendations = [];
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            if (strpos($line, '-') === 0 || strpos($line, '•') === 0 || preg_match('/^\d\./', $line)) {
                $recommendations[] = trim($line, '- •0123456789.');
            }
        }
        
        return $recommendations;
    }
    
    /**
     * AJAX handler for speech-to-text
     */
    public function ajax_speech_to_text() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        if (!isset($_FILES['audio'])) {
            wp_send_json_error('No audio file uploaded');
        }
        
        $file = $_FILES['audio'];
        $upload = wp_handle_upload($file, ['test_form' => false]);
        
        if (isset($upload['error'])) {
            wp_send_json_error($upload['error']);
        }
        
        $result = $this->speech_to_text($upload['file']);
        
        @unlink($upload['file']);
        
        if (isset($result['error'])) {
            wp_send_json_error($result['error']);
        }
        
        wp_send_json_success($result);
    }
    
    /**
     * Log AI usage
     */
    /**
     * Generate text using Google Gemini API
     */
    private function generate_text_gemini($prompt, $options = []) {
        $model = $options['model'] ?? 'gemini-1.5-flash';
        $temperature = $options['temperature'] ?? 0.7;
        
        $cache_key = 'kafanek_ai_' . md5($prompt . $model);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $this->api_key
        );
        
        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $options['max_tokens'] ?? 1024
                ]
            ]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return "Chyba spojení: " . $response->get_error_message();
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return "Gemini chyba: " . $body['error']['message'];
        }
        
        $result = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        set_transient($cache_key, $result, 21 * MINUTE_IN_SECONDS);
        
        $this->log_usage($prompt, $result, [
            'total_tokens' => ($body['usageMetadata']['totalTokenCount'] ?? 0)
        ], 'gemini');
        
        return $result;
    }
    
    /**
     * Azure Speech-to-Text
     */
    public function speech_to_text($audio_file_path) {
        if (empty($this->azure_key)) {
            return ['error' => 'Azure Speech API klíč není nastaven'];
        }
        
        $endpoint = sprintf(
            'https://%s.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language=cs-CZ',
            $this->azure_region
        );
        
        $audio_data = file_get_contents($audio_file_path);
        
        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $this->azure_key,
                'Content-Type' => 'audio/wav'
            ],
            'body' => $audio_data,
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['RecognitionStatus']) && $body['RecognitionStatus'] === 'Success') {
            return [
                'text' => $body['DisplayText'],
                'confidence' => $body['NBest'][0]['Confidence'] ?? 0
            ];
        }
        
        return ['error' => 'Rozpoznání selhalo'];
    }
    
    /**
     * AJAX handler for text generation
     */
    public function ajax_generate_text() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        $prompt = $_POST['prompt'] ?? '';
        $type = $_POST['type'] ?? 'general';
        
        if (empty($prompt)) {
            wp_send_json_error('Prompt is required');
        }
        
        $result = $this->generate_text($prompt);
        
        if (isset($result['error'])) {
            wp_send_json_error($result['error']);
        }
        
        wp_send_json_success(['text' => $result]);
    }
    
    /**
     * AJAX handler for content analysis
     */
    public function ajax_analyze_content() {
        check_ajax_referer('kafanek_ai_nonce', 'nonce');
        
        $content = $_POST['content'] ?? '';
        $keywords = $_POST['keywords'] ?? [];
        
        if (empty($content)) {
            wp_send_json_error('Content is required');
        }
        
        $result = $this->analyze_content($content, $keywords);
        
        if (isset($result['error'])) {
            wp_send_json_error($result['error']);
        }
        
        wp_send_json_success($result);
    }
    
    /**
     * Log AI usage
     */
    private function log_usage($prompt, $response, $usage, $provider = 'openai') {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'kafanek_ai_logs',
            [
                'request_type' => $provider,
                'request_data' => json_encode(['prompt' => substr($prompt, 0, 500)]),
                'response_data' => json_encode(['response' => substr($response, 0, 500)]),
                'tokens_used' => $usage['total_tokens'] ?? 0,
                'created_at' => current_time('mysql')
            ]
        );
    }
}
