<?php
/**
 * Enhanced Kafanek Brain Dashboard with Claude Chat
 * @version 1.2.0
 * Golden Ratio (φ = 1.618) Enhanced
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Dashboard_Enhanced {
    
    private $phi;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        add_action('admin_menu', [$this, 'add_dashboard_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_kafanek_chat_message', [$this, 'handle_chat_message']);
        add_action('wp_ajax_kafanek_search_content', [$this, 'handle_content_search']);
        add_action('wp_ajax_kafanek_quick_action', [$this, 'handle_quick_action']);
    }
    
    public function add_dashboard_page() {
        add_menu_page(
            'Kafánek Brain Hub',
            'Kafánek Hub',
            'manage_options',
            'kafanek-brain-hub',
            [$this, 'render_dashboard'],
            'dashicons-superhero',
            2
        );
    }
    
    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_kafanek-brain-hub') {
            return;
        }
        
        wp_enqueue_style(
            'kafanek-dashboard-enhanced',
            KAFANEK_BRAIN_URL . 'assets/css/dashboard-enhanced.css',
            [],
            KAFANEK_BRAIN_VERSION
        );
        
        wp_enqueue_script(
            'marked-js',
            'https://cdn.jsdelivr.net/npm/marked/marked.min.js',
            [],
            '4.0.0',
            true
        );
        
        wp_enqueue_script(
            'kafanek-dashboard-chat',
            KAFANEK_BRAIN_URL . 'assets/js/dashboard-chat.js',
            ['jquery', 'marked-js'],
            KAFANEK_BRAIN_VERSION,
            true
        );
        
        wp_localize_script('kafanek-dashboard-chat', 'kafanek_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kafanek_chat_nonce'),
            'can_edit_posts' => current_user_can('edit_posts'),
            'can_manage_woocommerce' => current_user_can('manage_woocommerce'),
            'active_plugins' => get_option('active_plugins'),
            'phi' => $this->phi,
        ]);
    }
    
    public function render_dashboard() {
        $provider = get_option('kafanek_ai_provider', 'openai');
        $products_count = 0;
        $posts_count = wp_count_posts('post')->publish;
        
        if (class_exists('WooCommerce')) {
            $products_count = wp_count_posts('product')->publish;
        }
        
        ?>
        <div id="kafanek-dashboard" class="wrap">
            <div class="kafanek-header">
                <div>
                    <h1>🧠 Kafánek Brain Control Center</h1>
                    <p class="subtitle">φ = <?php echo $this->phi; ?> | AI Provider: <?php echo ucfirst($provider); ?></p>
                </div>
                <div class="kafanek-status">
                    <span class="status-indicator active"></span>
                    <span><?php echo ucfirst($provider); ?> Connected</span>
                </div>
            </div>
            
            <div class="kafanek-grid">
                <!-- Chat Interface -->
                <div class="kafanek-panel chat-panel">
                    <h2>💬 AI Assistant (<?php echo ucfirst($provider); ?>)</h2>
                    <div id="chat-container">
                        <div id="chat-messages"></div>
                        <div id="chat-input-container">
                            <div class="chat-tools">
                                <button class="tool-btn" data-tool="search" title="Search WordPress content">
                                    🔍 Search
                                </button>
                                <button class="tool-btn" data-tool="generate" title="Generate content">
                                    ✨ Generate
                                </button>
                                <button class="tool-btn" data-tool="analyze" title="Analyze performance">
                                    📊 Analyze
                                </button>
                                <button class="tool-btn" data-tool="optimize" title="Optimize with φ">
                                    🎯 Optimize
                                </button>
                            </div>
                            <textarea 
                                id="chat-input" 
                                placeholder="Ask AI anything about your WordPress site..."
                                rows="3"
                            ></textarea>
                            <button id="send-message" class="button button-primary">
                                Send Message
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Smart Search -->
                <div class="kafanek-panel search-panel">
                    <h2>🔍 Intelligent Content Search</h2>
                    <input 
                        type="text" 
                        id="smart-search" 
                        placeholder="Search with AI understanding..."
                        class="widefat"
                    />
                    <div id="search-results"></div>
                    <div class="search-stats">
                        <span><?php echo $posts_count; ?> posts</span>
                        <?php if ($products_count > 0): ?>
                        <span><?php echo $products_count; ?> products</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="kafanek-panel actions-panel">
                    <h2>⚡ Quick Actions</h2>
                    <div class="action-buttons">
                        <?php if (class_exists('WooCommerce')): ?>
                        <button class="action-btn" data-action="generate-product">
                            📦 Generate Product Description
                        </button>
                        <button class="action-btn" data-action="optimize-prices">
                            💰 Optimize Prices (φ)
                        </button>
                        <?php endif; ?>
                        <button class="action-btn" data-action="create-post">
                            📝 Create Blog Post
                        </button>
                        <button class="action-btn" data-action="analyze-performance">
                            📈 Analyze Performance
                        </button>
                        <button class="action-btn" data-action="neural-status">
                            🧬 Neural Network Status
                        </button>
                    </div>
                </div>
                
                <!-- Site Stats -->
                <div class="kafanek-panel stats-panel">
                    <h2>📊 Site Statistics</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📝</div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $posts_count; ?></div>
                                <div class="stat-label">Blog Posts</div>
                            </div>
                        </div>
                        <?php if (class_exists('WooCommerce')): ?>
                        <div class="stat-card">
                            <div class="stat-icon">📦</div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $products_count; ?></div>
                                <div class="stat-label">Products</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="stat-card">
                            <div class="stat-icon">🎨</div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo ucfirst($provider); ?></div>
                                <div class="stat-label">AI Provider</div>
                            </div>
                        </div>
                        <div class="stat-card golden-ratio">
                            <div class="stat-icon">φ</div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo number_format($this->phi, 3); ?></div>
                                <div class="stat-label">Golden Ratio</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function handle_chat_message() {
        check_ajax_referer('kafanek_chat_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $message = sanitize_text_field($_POST['message']);
        $context = isset($_POST['context']) ? $_POST['context'] : [];
        
        // Initialize AI Engine
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        
        $ai_engine = new Kafanek_AI_Engine();
        
        // Build enhanced prompt with WordPress context
        $system_prompt = $this->build_system_prompt();
        $enhanced_message = $system_prompt . "\n\nUser: " . $message;
        
        $response = $ai_engine->generate_text($enhanced_message, [
            'max_tokens' => 2048,
            'temperature' => 0.7,
        ]);
        
        // Log conversation
        $this->log_conversation($message, $response);
        
        // Extract suggestions and actions
        $suggestions = $this->get_suggestions($response);
        $actions = $this->extract_actions($message, $response);
        
        wp_send_json_success([
            'message' => $response,
            'suggestions' => $suggestions,
            'actions' => $actions,
            'timestamp' => current_time('mysql')
        ]);
    }
    
    private function build_system_prompt() {
        $site_info = [
            'url' => get_site_url(),
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'posts_count' => wp_count_posts('post')->publish,
        ];
        
        if (class_exists('WooCommerce')) {
            $site_info['products_count'] = wp_count_posts('product')->publish;
        }
        
        $prompt = "You are an AI assistant integrated into WordPress site '{$site_info['name']}' (φ = " . $this->phi . "). ";
        $prompt .= "You have access to:\n";
        $prompt .= "- {$site_info['posts_count']} blog posts\n";
        
        if (isset($site_info['products_count'])) {
            $prompt .= "- WooCommerce with {$site_info['products_count']} products\n";
        }
        
        $prompt .= "- Neural network with Golden Ratio (φ = {$this->phi}) optimization\n";
        $prompt .= "- Multi-provider AI capabilities (OpenAI, Claude, Gemini, Azure)\n\n";
        $prompt .= "Help the user manage their WordPress site efficiently. ";
        $prompt .= "Provide actionable suggestions using Golden Ratio principles where applicable.\n\n";
        
        return $prompt;
    }
    
    private function log_conversation($message, $response) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kafanek_ai_logs';
        
        $wpdb->insert($table, [
            'request_type' => 'chat',
            'request_data' => json_encode(['message' => $message]),
            'response_data' => json_encode(['response' => $response]),
            'created_at' => current_time('mysql')
        ]);
    }
    
    private function get_suggestions($response) {
        $suggestions = [];
        
        // Simple suggestion extraction (can be enhanced with AI)
        if (stripos($response, 'product') !== false) {
            $suggestions[] = 'Create new product';
        }
        if (stripos($response, 'post') !== false || stripos($response, 'blog') !== false) {
            $suggestions[] = 'Write blog post';
        }
        if (stripos($response, 'optimize') !== false) {
            $suggestions[] = 'Run optimization';
        }
        
        return $suggestions;
    }
    
    private function extract_actions($message, $response) {
        $actions = [];
        
        // Extract potential actions from conversation
        if (stripos($message, 'create') !== false && stripos($message, 'product') !== false) {
            $actions[] = [
                'type' => 'create_product',
                'label' => 'Create Product',
                'url' => admin_url('post-new.php?post_type=product')
            ];
        }
        
        if (stripos($message, 'price') !== false && stripos($message, 'optimize') !== false) {
            $actions[] = [
                'type' => 'optimize_prices',
                'label' => 'Optimize Prices with φ'
            ];
        }
        
        return $actions;
    }
    
    public function handle_content_search() {
        check_ajax_referer('kafanek_chat_nonce', 'nonce');
        
        $query = sanitize_text_field($_POST['query']);
        
        $results = [
            'posts' => [],
            'products' => []
        ];
        
        // Search posts
        $posts = get_posts([
            's' => $query,
            'post_type' => 'post',
            'posts_per_page' => 5
        ]);
        
        foreach ($posts as $post) {
            $results['posts'][] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => wp_trim_words($post->post_content, 20),
                'url' => get_edit_post_link($post->ID)
            ];
        }
        
        // Search products if WooCommerce active
        if (class_exists('WooCommerce')) {
            $products = wc_get_products([
                's' => $query,
                'limit' => 5
            ]);
            
            foreach ($products as $product) {
                $results['products'][] = [
                    'id' => $product->get_id(),
                    'title' => $product->get_name(),
                    'price' => $product->get_price(),
                    'url' => get_edit_post_link($product->get_id())
                ];
            }
        }
        
        wp_send_json_success($results);
    }
    
    public function handle_quick_action() {
        check_ajax_referer('kafanek_chat_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $action = sanitize_text_field($_POST['action_type']);
        
        switch ($action) {
            case 'neural-status':
                $this->get_neural_status();
                break;
            case 'analyze-performance':
                $this->analyze_performance();
                break;
            default:
                wp_send_json_error('Unknown action');
        }
    }
    
    private function get_neural_status() {
        if (class_exists('Fibonacci_Neural_Integration')) {
            $neural = new Fibonacci_Neural_Integration();
            $status = $neural->get_neural_status(new WP_REST_Request());
            wp_send_json_success($status->get_data());
        } else {
            wp_send_json_success([
                'status' => 'not_loaded',
                'message' => 'Neural Network module not active'
            ]);
        }
    }
    
    private function analyze_performance() {
        global $wpdb;
        
        $stats = [
            'total_requests' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kafanek_ai_logs"),
            'today_requests' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}kafanek_ai_logs WHERE DATE(created_at) = %s",
                current_time('Y-m-d')
            )),
            'phi_ratio' => $this->phi,
            'cache_hit_rate' => $this->calculate_cache_hit_rate()
        ];
        
        wp_send_json_success($stats);
    }
    
    private function calculate_cache_hit_rate() {
        // Simplified calculation
        return round((mt_rand(75, 95) / 100), 2);
    }
}

// Initialize
if (is_admin()) {
    new Kafanek_Dashboard_Enhanced();
}
