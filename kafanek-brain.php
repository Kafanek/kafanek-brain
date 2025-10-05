<?php
/**
 * Plugin Name: Kafánkův Mozek - AI WordPress Brain
 * Plugin URI: https://mykolibri-academy.cz
 * Description: Inteligentní AI asistent pro WordPress s WooCommerce a Elementor integrací
 * Version:           1.2.5
 * Author: Kolibri Academy
 * Author URI: https://mykolibri-academy.cz
 * License: GPL v2 or later
 * Text Domain: kafanek-brain
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * WC requires at least: 7.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) exit;

// Constants
define('KAFANEK_BRAIN_VERSION', '1.2.5');
define('KAFANEK_BRAIN_PATH', plugin_dir_path(__FILE__));
define('KAFANEK_BRAIN_URL', plugin_dir_url(__FILE__));
define('KAFANEK_BRAIN_BASENAME', plugin_basename(__FILE__));
define('KAFANEK_BRAIN_PHI', 1.618033988749895);

// Main Plugin Class
class Kafanek_Brain_Plugin {
    
    private static $instance = null;
    private $modules_loaded = [];
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Initialize
        add_action('plugins_loaded', [$this, 'init'], 5);
        add_action('init', [$this, 'register_post_types']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        
        // AJAX handlers
        add_action('wp_ajax_kafanek_ai_request', [$this, 'handle_ai_request']);
        add_action('wp_ajax_nopriv_kafanek_ai_request', [$this, 'handle_ai_request']);
    }
    
    public function activate() {
        // Use professional activator class
        require_once KAFANEK_BRAIN_PATH . 'includes/class-activator.php';
        Kafanek_Brain_Activator::activate();
    }
    
    public function deactivate() {
        wp_clear_scheduled_hook('kafanek_daily_optimization');
        flush_rewrite_rules();
    }
    
    public function init() {
        // Check version and upgrade if needed
        $this->check_version();
        
        // Load all 7 core classes
        require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-batch-processor.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-updater.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-error-handler.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-multisite-compat.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-performance-optimizer.php';
        require_once KAFANEK_BRAIN_PATH . 'includes/class-activator.php';
        
        // Load shortcodes for easy usage
        require_once KAFANEK_BRAIN_PATH . 'includes/class-shortcodes.php';
        
        // Load core helpers first
        $this->load_core_helpers();
        
        // Initialize AI engine BEFORE modules (required by WooCommerce module)
        $this->init_ai_engine();
        
        // Load active modules
        $this->load_modules();
        
        // Initialize auto-updater
        $this->init_updater();
    }
    
    private function load_core_helpers() {
        $helpers_file = KAFANEK_BRAIN_PATH . 'modules/core/helpers.php';
        if (file_exists($helpers_file)) {
            require_once $helpers_file;
            $this->modules_loaded['helpers'] = true;
            error_log('Kafanek Brain: Core helpers loaded');
        }
    }
    
    private function load_modules() {
        $modules = get_option('kafanek_brain_modules', []);
        
        // WooCommerce module
        if (!empty($modules['woocommerce']) && class_exists('WooCommerce')) {
            $this->load_module('woocommerce/class-woocommerce-ai.php', 'WooCommerce AI');
        }
        
        // Elementor module
        if (!empty($modules['elementor']) && did_action('elementor/loaded')) {
            $this->load_module('elementor/class-elementor-widgets.php', 'Elementor Widgets');
        }
        
        // Neural Network module
        if (!empty($modules['neural'])) {
            $this->load_module('neural/class-neural-network.php', 'Neural Network');
        }
        
        // AI Content Studio (always load if file exists)
        $this->load_module('content-studio/ai-copywriter.php', 'AI Copywriter', false);
        
        // Dynamic Pricing Engine (load if WooCommerce active)
        if (class_exists('WooCommerce')) {
            $this->load_module('pricing-engine/dynamic-pricing.php', 'Dynamic Pricing', false);
        }
        
        // Email Marketing Genius
        $this->load_module('email-genius/campaign-builder.php', 'Email Genius', false);
        
        // AI Chatbot
        $this->load_module('chatbot/class-ai-chatbot.php', 'AI Chatbot', false);
        
        // AI Design Studio
        $this->load_module('design-studio/ai-design-generator.php', 'AI Design Studio', false);
    }
    
    private function load_module($file, $name, $required = true) {
        $path = KAFANEK_BRAIN_PATH . 'modules/' . $file;
        if (file_exists($path)) {
            require_once $path;
            $this->modules_loaded[$name] = true;
            error_log("Kafanek Brain: {$name} module loaded");
        } elseif ($required) {
            error_log("Kafanek Brain: {$name} module not found at {$path}");
        }
    }
    
    private function init_ai_engine() {
        $ai_engine_file = KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        if (file_exists($ai_engine_file)) {
            require_once $ai_engine_file;
            new Kafanek_AI_Engine();
        }
    }
    
    /**
     * Check version and upgrade if needed
     */
    private function check_version() {
        $installed_version = get_option('kafanek_brain_version', '0.0.0');
        
        if (version_compare($installed_version, KAFANEK_BRAIN_VERSION, '<')) {
            $this->upgrade($installed_version, KAFANEK_BRAIN_VERSION);
            update_option('kafanek_brain_version', KAFANEK_BRAIN_VERSION);
        }
    }
    
    /**
     * Upgrade routine for version changes
     */
    private function upgrade($from_version, $to_version) {
        // Upgrade from 1.0.x to 1.2.x
        if (version_compare($from_version, '1.2.0', '<')) {
            // Add new options for multi-provider AI
            if (!get_option('kafanek_ai_provider')) {
                add_option('kafanek_ai_provider', 'openai');
            }
            if (!get_option('kafanek_claude_api_key')) {
                add_option('kafanek_claude_api_key', '');
            }
            if (!get_option('kafanek_gemini_api_key')) {
                add_option('kafanek_gemini_api_key', '');
            }
            if (!get_option('kafanek_azure_speech_key')) {
                add_option('kafanek_azure_speech_key', '');
            }
            
            // Upgrade database if needed
            $this->create_tables();
        }
        
        // Log upgrade
        error_log("Kafanek Brain: Upgraded from {$from_version} to {$to_version}");
    }
    
    /**
     * Initialize auto-updater
     */
    private function init_updater() {
        // Only in admin
        if (!is_admin()) {
            return;
        }
        
        $updater_file = KAFANEK_BRAIN_PATH . 'includes/class-updater.php';
        if (file_exists($updater_file)) {
            require_once $updater_file;
        }
    }
    
    public function register_post_types() {
        // Register custom post types if needed
    }
    
    /**
     * REST API Routes
     */
    public function register_rest_routes() {
        // Main status endpoint
        register_rest_route('kafanek-brain/v1', '/status', [
            'methods' => 'GET',
            'callback' => [$this, 'get_status'],
            'permission_callback' => '__return_true'
        ]);
        
        // AI generation endpoint
        register_rest_route('kafanek-brain/v1', '/generate', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_content'],
            'permission_callback' => [$this, 'check_api_permission']
        ]);
        
        // Neural Network endpoints
        register_rest_route('kafanek-brain/v1', '/neural/status', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_neural_status'],
            'permission_callback' => '__return_true'
        ]);
        
        register_rest_route('kafanek-brain/v1', '/neural/predict-price', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_neural_predict_price'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_rest_route('kafanek-brain/v1', '/neural/optimize-content', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_neural_optimize_content'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_rest_route('kafanek-brain/v1', '/neural/train', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_neural_train'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
        
        // Product recommendations
        register_rest_route('kafanek-brain/v1', '/recommendations', [
            'methods' => 'GET',
            'callback' => [$this, 'get_recommendations'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    public function get_status() {
        return [
            'status' => 'active',
            'version' => KAFANEK_BRAIN_VERSION,
            'modules' => $this->modules_loaded,
            'ai_ready' => !empty(get_option('kafanek_brain_api_key'))
        ];
    }
    
    public function generate_content($request) {
        return ['message' => 'Generate content endpoint'];
    }
    
    public function get_recommendations($request) {
        return ['recommendations' => []];
    }
    
    public function check_api_permission($request) {
        $api_key = $request->get_header('X-Kafanek-API-Key');
        $stored_key = get_option('kafanek_brain_api_key');
        
        return !empty($api_key) && $api_key === $stored_key;
    }
    
    private function generate_product_description($data) {
        return ['success' => true, 'description' => 'Generated description'];
    }
    
    private function optimize_product_price($data) {
        return ['success' => true, 'price' => 0];
    }
    
    private function process_chat_message($data) {
        return ['success' => true, 'message' => 'Chat response'];
    }
    
    /**
     * Admin Menu
     */
    public function add_admin_menu() {
        // Load Enhanced Dashboard if exists
        $dashboard_enhanced = KAFANEK_BRAIN_PATH . 'admin/dashboard-enhanced.php';
        if (file_exists($dashboard_enhanced)) {
            require_once $dashboard_enhanced;
            // Enhanced Dashboard will add its own menu
        }
        
        add_menu_page(
            'Kafánkův Mozek',
            'Kafánkův Mozek',
            'manage_options',
            'kafanek-brain',
            [$this, 'admin_page'],
            'dashicons-admin-generic',
            30
        );
        
        add_submenu_page(
            'kafanek-brain',
            'Nastavení',
            'Nastavení',
            'manage_options',
            'kafanek-brain-settings',
            [$this, 'settings_page']
        );
    }
    
    public function admin_page() {
        $dashboard_file = KAFANEK_BRAIN_PATH . 'admin/dashboard.php';
        if (file_exists($dashboard_file)) {
            include $dashboard_file;
        } else {
            echo '<div class="wrap"><h1>🧠 Kafánkův Mozek</h1><p>Dashboard is being prepared...</p></div>';
        }
    }
    
    public function settings_page() {
        $settings_file = KAFANEK_BRAIN_PATH . 'admin/settings.php';
        if (file_exists($settings_file)) {
            include $settings_file;
        }
    }
    
    /**
     * Scripts & Styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'kafanek-brain',
            KAFANEK_BRAIN_URL . 'assets/js/kafanek-brain.js',
            ['jquery'],
            KAFANEK_BRAIN_VERSION,
            true
        );
        
        wp_localize_script('kafanek-brain', 'kafanek_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kafanek_ai_nonce'),
            'api_url' => rest_url('kafanek-brain/v1')
        ]);
        
        // Design Tokens (must load first)
        wp_enqueue_style(
            'kafanek-design-tokens',
            KAFANEK_BRAIN_URL . 'assets/css/design-tokens.css',
            [],
            KAFANEK_BRAIN_VERSION
        );
        
        wp_enqueue_style(
            'kafanek-brain',
            KAFANEK_BRAIN_URL . 'assets/css/kafanek-brain.css',
            ['kafanek-design-tokens'],
            KAFANEK_BRAIN_VERSION
        );
    }
    
    public function admin_scripts() {
        wp_enqueue_script(
            'kafanek-admin',
            KAFANEK_BRAIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-api'],
            KAFANEK_BRAIN_VERSION,
            true
        );
    }
    
    /**
     * Database tables
     */
    private function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // AI requests log
        $table_name = $wpdb->prefix . 'kafanek_ai_logs';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            request_type varchar(50) NOT NULL,
            request_data longtext,
            response_data longtext,
            tokens_used int(11),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY request_type (request_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Neural Network REST API Callbacks
     */
    public function rest_neural_status($request) {
        if (!class_exists('Fibonacci_Neural_Integration')) {
            require_once KAFANEK_BRAIN_PATH . 'modules/fibonacci-neural/fibonacci-neural-integration.php';
        }
        
        $neural = new Fibonacci_Neural_Integration();
        return $neural->get_neural_status($request);
    }
    
    public function rest_neural_predict_price($request) {
        if (!class_exists('Fibonacci_AI_Predictor')) {
            require_once KAFANEK_BRAIN_PATH . 'modules/fibonacci-neural/ai-predictor.php';
        }
        
        $predictor = new Fibonacci_AI_Predictor();
        return $predictor->predict_optimal_price($request);
    }
    
    public function rest_neural_optimize_content($request) {
        if (!class_exists('Fibonacci_AI_Predictor')) {
            require_once KAFANEK_BRAIN_PATH . 'modules/fibonacci-neural/ai-predictor.php';
        }
        
        $predictor = new Fibonacci_AI_Predictor();
        return $predictor->optimize_content($request);
    }
    
    public function rest_neural_train($request) {
        if (!class_exists('Fibonacci_Neural_Integration')) {
            require_once KAFANEK_BRAIN_PATH . 'modules/fibonacci-neural/fibonacci-neural-integration.php';
        }
        
        $neural = new Fibonacci_Neural_Integration();
        return $neural->train_neural_network($request);
    }
}

// Initialize plugin
Kafanek_Brain_Plugin::get_instance();
