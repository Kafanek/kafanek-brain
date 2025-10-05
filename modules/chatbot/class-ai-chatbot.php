<?php
/**
 * AI Chatbot Module
 * @version 1.2.0
 * φ-Enhanced Conversational AI
 */

if (!defined('ABSPATH')) exit;

class Kafanek_AI_Chatbot {
    
    private $ai_engine;
    private $phi;
    private $conversations = [];
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        $this->ai_engine = new Kafanek_AI_Engine();
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Frontend widget
        add_action('wp_footer', [$this, 'render_chatbot_widget']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        
        // AJAX handlers
        add_action('wp_ajax_kafanek_chatbot_message', [$this, 'handle_message']);
        add_action('wp_ajax_nopriv_kafanek_chatbot_message', [$this, 'handle_message']);
        
        add_action('wp_ajax_kafanek_chatbot_product_search', [$this, 'search_products']);
        add_action('wp_ajax_nopriv_kafanek_chatbot_product_search', [$this, 'search_products']);
        
        // Admin
        add_action('admin_menu', [$this, 'add_admin_menu'], 99);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Shortcode
        add_shortcode('kafanek_chatbot', [$this, 'chatbot_shortcode']);
        
        // Create conversations table
        add_action('admin_init', [$this, 'create_conversations_table'], 5);
    }
    
    public function create_conversations_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kafanek_chatbot_conversations';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id bigint(20) AUTO_INCREMENT PRIMARY KEY,
                session_id varchar(255) NOT NULL,
                user_id bigint(20) DEFAULT NULL,
                user_message TEXT,
                bot_response TEXT,
                context JSON,
                intent varchar(100),
                sentiment varchar(50),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                KEY session_id (session_id),
                KEY user_id (user_id),
                KEY created_at (created_at)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    public function enqueue_frontend_assets() {
        if (!get_option('kafanek_chatbot_enabled', '1')) {
            return;
        }
        
        wp_enqueue_style('kafanek-chatbot', 
            plugins_url('../../assets/css/chatbot-widget.css', __FILE__),
            [],
            '1.2.0'
        );
        
        wp_enqueue_script('kafanek-chatbot',
            plugins_url('../../assets/js/chatbot-widget.js', __FILE__),
            ['jquery'],
            '1.2.0',
            true
        );
        
        wp_localize_script('kafanek-chatbot', 'kafanekChatbot', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kafanek_chatbot'),
            'botName' => get_option('kafanek_chatbot_name', 'Kafánek'),
            'welcomeMessage' => get_option('kafanek_chatbot_welcome', 'Ahoj! Jak vám mohu pomoci?'),
            'placeholder' => get_option('kafanek_chatbot_placeholder', 'Napište zprávu...'),
            'isWooCommerce' => class_exists('WooCommerce'),
            'phi' => $this->phi
        ]);
    }
    
    public function render_chatbot_widget() {
        if (!get_option('kafanek_chatbot_enabled', '1')) {
            return;
        }
        
        $position = get_option('kafanek_chatbot_position', 'bottom-right');
        $avatar = get_option('kafanek_chatbot_avatar', plugins_url('../../assets/images/kafanek-avatar.svg', __FILE__));
        
        ?>
        <div id="kafanek-chatbot-widget" class="position-<?php echo esc_attr($position); ?>">
            <div id="kafanek-chat-button" class="chat-button">
                <img src="<?php echo esc_url($avatar); ?>" alt="Chat">
                <span class="chat-badge">1</span>
            </div>
            
            <div id="kafanek-chat-window" class="chat-window" style="display: none;">
                <div class="chat-header">
                    <div class="chat-header-left">
                        <img src="<?php echo esc_url($avatar); ?>" alt="Bot">
                        <div class="header-info">
                            <span class="bot-name"><?php echo esc_html(get_option('kafanek_chatbot_name', 'Kafánek')); ?></span>
                            <span class="bot-status">● Online</span>
                        </div>
                    </div>
                    <button id="kafanek-chat-close" class="chat-close">×</button>
                </div>
                
                <div id="kafanek-chat-messages" class="chat-messages">
                    <!-- Messages will be inserted here -->
                </div>
                
                <div class="chat-quick-actions" id="kafanek-quick-actions">
                    <!-- Quick action buttons -->
                </div>
                
                <div class="chat-input-area">
                    <input type="text" id="kafanek-chat-input" placeholder="<?php echo esc_attr(get_option('kafanek_chatbot_placeholder', 'Napište zprávu...')); ?>">
                    <button id="kafanek-chat-send" class="chat-send-btn">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                        </svg>
                    </button>
                </div>
                
                <div class="chat-typing-indicator" style="display: none;">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function handle_message() {
        check_ajax_referer('kafanek_chatbot', 'nonce');
        
        $message = sanitize_text_field($_POST['message']);
        $session_id = sanitize_text_field($_POST['session_id']);
        $context = isset($_POST['context']) ? json_decode(stripslashes($_POST['context']), true) : [];
        
        if (empty($message)) {
            wp_send_json_error('Prázdná zpráva');
        }
        
        // Detect intent
        $intent = $this->detect_intent($message);
        
        // Analyze sentiment
        $sentiment = $this->analyze_message_sentiment($message);
        
        // Get conversation history
        $history = $this->get_conversation_history($session_id, 5);
        
        // Generate response based on intent
        $response = $this->generate_response($message, $intent, $context, $history);
        
        // Save conversation
        $this->save_conversation($session_id, $message, $response, $context, $intent, $sentiment);
        
        // Add quick actions
        $quick_actions = $this->get_quick_actions($intent, $context);
        
        wp_send_json_success([
            'response' => $response,
            'intent' => $intent,
            'sentiment' => $sentiment,
            'quick_actions' => $quick_actions,
            'suggestions' => $this->get_suggestions($intent)
        ]);
    }
    
    private function detect_intent($message) {
        $message_lower = mb_strtolower($message);
        
        // Product search
        if (preg_match('/\b(hledám|chci|potřebuji|mají|máte|prodáváte|koupit)\b/u', $message_lower)) {
            return 'product_search';
        }
        
        // Price inquiry
        if (preg_match('/\b(cena|stojí|kolik|levně|sleva|akce)\b/u', $message_lower)) {
            return 'price_inquiry';
        }
        
        // Order status
        if (preg_match('/\b(objednávka|dodání|doručení|zásilka|tracking)\b/u', $message_lower)) {
            return 'order_status';
        }
        
        // Support
        if (preg_match('/\b(problém|nefunguje|pomoc|reklamace|vrácení)\b/u', $message_lower)) {
            return 'support';
        }
        
        // Contact
        if (preg_match('/\b(kontakt|email|telefon|adresa|kde|kdy)\b/u', $message_lower)) {
            return 'contact';
        }
        
        // Greeting
        if (preg_match('/\b(ahoj|dobrý|nazdar|čau|zdravím)\b/u', $message_lower)) {
            return 'greeting';
        }
        
        return 'general';
    }
    
    private function generate_response($message, $intent, $context, $history) {
        $system_prompt = $this->build_system_prompt($intent, $context);
        
        // Build conversation context
        $conversation_context = '';
        if (!empty($history)) {
            foreach ($history as $msg) {
                $conversation_context .= "Uživatel: {$msg->user_message}\nBot: {$msg->bot_response}\n";
            }
        }
        
        $full_prompt = $system_prompt . "\n\nHistorie konverzace:\n" . $conversation_context . 
                      "\n\nNová zpráva od uživatele:\n" . $message . 
                      "\n\nTvá odpověď (maximálně 3 věty, přátelský a profesionální tón):";
        
        $provider = get_option('kafanek_chatbot_provider', 'claude');
        
        $response = $this->ai_engine->generate_text($full_prompt, [
            'provider' => $provider,
            'model' => $this->get_model_for_provider($provider),
            'max_tokens' => 200,
            'temperature' => 0.7
        ]);
        
        // Handle specific intents
        switch ($intent) {
            case 'product_search':
                $response .= $this->append_product_recommendations($message);
                break;
            
            case 'price_inquiry':
                $response .= $this->append_price_info($message);
                break;
            
            case 'order_status':
                $response .= $this->append_order_tracking($context);
                break;
        }
        
        return trim($response);
    }
    
    private function build_system_prompt($intent, $context) {
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        
        $base_prompt = "Jsi {$site_name} AI asistent. {$site_description}. 
Tvým úkolem je pomáhat zákazníkům s jejich dotazy, doporučovat produkty a poskytovat podporu.
Používej zlatý řez (φ = 1.618) pro doporučení - 61.8% informací, 38.2% akce.
Buď přátelský, profesionální a stručný. Odpovídej v češtině.";
        
        $intent_prompts = [
            'product_search' => "\nZaměř se na nalezení relevantních produktů a jejich doporučení.",
            'price_inquiry' => "\nPoskytni informace o cenách a případných slevách.",
            'order_status' => "\nPomoz zákazníkovi sledovat jeho objednávku.",
            'support' => "\nPoskytni technickou podporu a řešení problémů.",
            'contact' => "\nPoskytni kontaktní informace: email, telefon, adresa.",
            'greeting' => "\nPřivítej zákazníka a nabídni pomoc."
        ];
        
        return $base_prompt . ($intent_prompts[$intent] ?? '');
    }
    
    private function get_model_for_provider($provider) {
        $models = [
            'openai' => 'gpt-4',
            'claude' => 'claude-3-5-sonnet-20241022',
            'gemini' => 'gemini-pro',
            'azure' => 'gpt-4'
        ];
        
        return $models[$provider] ?? 'gpt-4';
    }
    
    private function append_product_recommendations($message) {
        if (!class_exists('WooCommerce')) {
            return '';
        }
        
        // Extract keywords
        $keywords = $this->extract_keywords($message);
        
        // Search products
        $args = [
            's' => implode(' ', $keywords),
            'post_type' => 'product',
            'posts_per_page' => 3,
            'post_status' => 'publish'
        ];
        
        $products = get_posts($args);
        
        if (empty($products)) {
            return '';
        }
        
        $html = "\n\n<div class='chatbot-products'><strong>Doporučené produkty:</strong>";
        
        foreach ($products as $product_post) {
            $product = wc_get_product($product_post->ID);
            $html .= "<div class='chatbot-product'>";
            $html .= "<img src='" . get_the_post_thumbnail_url($product_post->ID, 'thumbnail') . "' alt=''>";
            $html .= "<div class='product-info'>";
            $html .= "<strong>" . $product->get_name() . "</strong><br>";
            $html .= "<span class='price'>" . $product->get_price_html() . "</span><br>";
            $html .= "<a href='" . get_permalink($product_post->ID) . "' class='btn-view'>Zobrazit</a>";
            $html .= "</div></div>";
        }
        
        $html .= "</div>";
        
        return $html;
    }
    
    private function append_price_info($message) {
        if (!class_exists('WooCommerce')) {
            return '';
        }
        
        // Check for active sales
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 5,
            'meta_query' => [
                [
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ]
            ]
        ];
        
        $sale_products = get_posts($args);
        
        if (!empty($sale_products)) {
            return "\n\n💰 Máme aktuálně slevy na " . count($sale_products) . " produktů! Chcete je vidět?";
        }
        
        return '';
    }
    
    private function append_order_tracking($context) {
        if (!is_user_logged_in()) {
            return "\n\nPro sledování objednávky se prosím přihlaste nebo zadejte číslo objednávky a email.";
        }
        
        $customer = new WC_Customer(get_current_user_id());
        $orders = wc_get_orders([
            'customer' => get_current_user_id(),
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        
        if (empty($orders)) {
            return '';
        }
        
        $order = $orders[0];
        return "\n\nVaše poslední objednávka #{$order->get_id()} má status: " . 
               wc_get_order_status_name($order->get_status());
    }
    
    private function extract_keywords($message) {
        $stopwords = ['je', 'to', 'a', 'v', 's', 'na', 'se', 'o', 'do', 'z', 'k', 'pro'];
        $words = preg_split('/\s+/', mb_strtolower($message));
        
        return array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 2;
        });
    }
    
    private function analyze_message_sentiment($message) {
        $positive_words = ['skvělý', 'super', 'perfekt', 'dobrý', 'díky', 'děkuji', 'prima'];
        $negative_words = ['špatný', 'problém', 'nefunguje', 'nespokojen', 'reklamace'];
        
        $message_lower = mb_strtolower($message);
        
        $positive_count = 0;
        foreach ($positive_words as $word) {
            if (strpos($message_lower, $word) !== false) {
                $positive_count++;
            }
        }
        
        $negative_count = 0;
        foreach ($negative_words as $word) {
            if (strpos($message_lower, $word) !== false) {
                $negative_count++;
            }
        }
        
        if ($positive_count > $negative_count) return 'positive';
        if ($negative_count > $positive_count) return 'negative';
        return 'neutral';
    }
    
    private function get_quick_actions($intent, $context) {
        $actions = [];
        
        switch ($intent) {
            case 'product_search':
                $actions = [
                    ['label' => '🔍 Zobrazit všechny produkty', 'action' => 'view_all_products'],
                    ['label' => '💎 Novinky', 'action' => 'view_new_products'],
                    ['label' => '🔥 Slevy', 'action' => 'view_sale_products']
                ];
                break;
            
            case 'support':
                $actions = [
                    ['label' => '📞 Kontakt', 'action' => 'show_contact'],
                    ['label' => '📧 Napsat email', 'action' => 'email_support'],
                    ['label' => '❓ FAQ', 'action' => 'show_faq']
                ];
                break;
            
            case 'greeting':
                $actions = [
                    ['label' => '🛍️ Chci nakoupit', 'action' => 'start_shopping'],
                    ['label' => '📦 Sledovat objednávku', 'action' => 'track_order'],
                    ['label' => '💬 Poradit si', 'action' => 'get_advice']
                ];
                break;
        }
        
        return $actions;
    }
    
    private function get_suggestions($intent) {
        $suggestions = [
            'product_search' => [
                'Jaké máte novinky?',
                'Máte něco na skladě?',
                'Co mi doporučujete?'
            ],
            'price_inquiry' => [
                'Máte nějaké slevy?',
                'Kdy bude akce?',
                'Nabízíte hromadné slevy?'
            ],
            'general' => [
                'Chci koupit produkt',
                'Potřebuji poradit',
                'Kde vás najdu?'
            ]
        ];
        
        return $suggestions[$intent] ?? $suggestions['general'];
    }
    
    private function get_conversation_history($session_id, $limit = 5) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kafanek_chatbot_conversations';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE session_id = %s ORDER BY created_at DESC LIMIT %d",
            $session_id,
            $limit
        ));
    }
    
    private function save_conversation($session_id, $user_message, $bot_response, $context, $intent, $sentiment) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kafanek_chatbot_conversations';
        
        $wpdb->insert($table, [
            'session_id' => $session_id,
            'user_id' => get_current_user_id(),
            'user_message' => $user_message,
            'bot_response' => $bot_response,
            'context' => json_encode($context),
            'intent' => $intent,
            'sentiment' => $sentiment,
            'created_at' => current_time('mysql')
        ]);
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'kafanek-brain',
            'AI Chatbot',
            '💬 AI Chatbot',
            'manage_options',
            'kafanek-chatbot',
            [$this, 'render_admin_page']
        );
    }
    
    public function register_settings() {
        register_setting('kafanek_chatbot', 'kafanek_chatbot_enabled');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_name');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_welcome');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_placeholder');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_position');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_provider');
        register_setting('kafanek_chatbot', 'kafanek_chatbot_avatar');
    }
    
    public function render_admin_page() {
        require_once dirname(__FILE__) . '/admin-chatbot.php';
    }
    
    public function chatbot_shortcode($atts) {
        $atts = shortcode_atts([
            'inline' => 'false'
        ], $atts);
        
        if ($atts['inline'] === 'true') {
            ob_start();
            ?>
            <div class="kafanek-chatbot-inline">
                <div id="kafanek-chat-messages-inline" class="chat-messages-inline"></div>
                <div class="chat-input-area">
                    <input type="text" class="kafanek-chat-input-inline" placeholder="Napište zprávu...">
                    <button class="chat-send-btn-inline">Odeslat</button>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}

new Kafanek_AI_Chatbot();
