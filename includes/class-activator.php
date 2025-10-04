<?php
/**
 * Plugin Activator - 7. třída pro perfektní architekturu
 * Handles plugin activation, database setup, and initial configuration
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Brain_Activator {
    
    /**
     * Plugin activation handler
     */
    public static function activate() {
        global $wpdb;
        
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            deactivate_plugins(KAFANEK_BRAIN_BASENAME);
            wp_die('Kafánek Brain vyžaduje WordPress 6.0 nebo novější.');
        }
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(KAFANEK_BRAIN_BASENAME);
            wp_die('Kafánek Brain vyžaduje PHP 7.4 nebo novější.');
        }
        
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create necessary directories
        self::create_directories();
        
        // Schedule cron events
        self::schedule_events();
        
        // Set activation timestamp
        update_option('kafanek_brain_activated_at', current_time('mysql'));
        update_option('kafanek_brain_version', KAFANEK_BRAIN_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Cache table
        $table_cache = $wpdb->prefix . 'kafanek_brain_cache';
        $sql_cache = "CREATE TABLE IF NOT EXISTS {$table_cache} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cache_key varchar(191) NOT NULL,
            cache_value longtext NOT NULL,
            expires_at datetime NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY cache_key (cache_key),
            KEY expires_at (expires_at)
        ) {$charset_collate};";
        dbDelta($sql_cache);
        
        // Usage tracking table
        $table_usage = $wpdb->prefix . 'kafanek_brain_usage';
        $sql_usage = "CREATE TABLE IF NOT EXISTS {$table_usage} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            provider varchar(50) NOT NULL,
            model varchar(100) NOT NULL,
            tokens_used int(11) NOT NULL DEFAULT 0,
            cost decimal(10,6) NOT NULL DEFAULT 0.000000,
            endpoint varchar(255) NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY provider (provider),
            KEY created_at (created_at)
        ) {$charset_collate};";
        dbDelta($sql_usage);
        
        // Neural models table
        $table_neural = $wpdb->prefix . 'kafanek_brain_neural_models';
        $sql_neural = "CREATE TABLE IF NOT EXISTS {$table_neural} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            architecture json NOT NULL,
            weights longtext NOT NULL,
            training_data json DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY name (name)
        ) {$charset_collate};";
        dbDelta($sql_neural);
        
        // Brand voices table
        $table_voices = $wpdb->prefix . 'kafanek_brain_brand_voices';
        $sql_voices = "CREATE TABLE IF NOT EXISTS {$table_voices} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            tone varchar(100) NOT NULL,
            keywords json NOT NULL,
            examples text NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";
        dbDelta($sql_voices);
        
        // Chatbot conversations table
        $table_chatbot = $wpdb->prefix . 'kafanek_chatbot_conversations';
        $sql_chatbot = "CREATE TABLE IF NOT EXISTS {$table_chatbot} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_message text NOT NULL,
            bot_response text NOT NULL,
            context json DEFAULT NULL,
            intent varchar(50) DEFAULT NULL,
            sentiment varchar(20) DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) {$charset_collate};";
        dbDelta($sql_chatbot);
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $defaults = [
            'kafanek_ai_provider' => 'openai',
            'kafanek_brand_voice' => 'profesionální a přátelský',
            'kafanek_target_audience' => 'dospělí zákazníci',
            'kafanek_custom_instructions' => '',
            'kafanek_enable_cache' => 1,
            'kafanek_cache_duration' => 1260, // 21 min (Fibonacci)
            'kafanek_enable_neural' => 1,
            'kafanek_enable_chatbot' => 1,
            'kafanek_chatbot_position' => 'bottom-right',
            'kafanek_modules' => [
                'content_studio' => 1,
                'dynamic_pricing' => 1,
                'email_genius' => 1,
                'chatbot' => 1,
                'neural' => 1,
                'woocommerce' => 1,
                'elementor' => 1
            ]
        ];
        
        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Create necessary directories
     */
    private static function create_directories() {
        $upload_dir = wp_upload_dir();
        $kafanek_dir = $upload_dir['basedir'] . '/kafanek-brain';
        
        if (!file_exists($kafanek_dir)) {
            wp_mkdir_p($kafanek_dir);
            
            // Add .htaccess for security
            $htaccess = $kafanek_dir . '/.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, 'deny from all');
            }
            
            // Add index.php for security
            $index = $kafanek_dir . '/index.php';
            if (!file_exists($index)) {
                file_put_contents($index, '<?php // Silence is golden');
            }
        }
        
        // Create logs directory
        $logs_dir = $kafanek_dir . '/logs';
        if (!file_exists($logs_dir)) {
            wp_mkdir_p($logs_dir);
        }
        
        // Create cache directory
        $cache_dir = $kafanek_dir . '/cache';
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
        }
        
        // Create models directory
        $models_dir = $kafanek_dir . '/models';
        if (!file_exists($models_dir)) {
            wp_mkdir_p($models_dir);
        }
    }
    
    /**
     * Schedule cron events
     */
    private static function schedule_events() {
        // Daily cleanup
        if (!wp_next_scheduled('kafanek_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'kafanek_daily_cleanup');
        }
        
        // Weekly optimization
        if (!wp_next_scheduled('kafanek_weekly_optimize')) {
            wp_schedule_event(time(), 'weekly', 'kafanek_weekly_optimize');
        }
        
        // Hourly cache cleanup
        if (!wp_next_scheduled('kafanek_hourly_cache_cleanup')) {
            wp_schedule_event(time(), 'hourly', 'kafanek_hourly_cache_cleanup');
        }
    }
    
    /**
     * Check system requirements
     */
    public static function check_requirements() {
        $errors = [];
        
        // WordPress version
        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            $errors[] = 'WordPress 6.0 nebo novější je vyžadován.';
        }
        
        // PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $errors[] = 'PHP 7.4 nebo novější je vyžadován.';
        }
        
        // PHP extensions
        $required_extensions = ['json', 'mbstring', 'curl'];
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = "PHP extension '{$ext}' je vyžadována.";
            }
        }
        
        // Memory limit
        $memory_limit = ini_get('memory_limit');
        if (wp_convert_hr_to_bytes($memory_limit) < 134217728) { // 128MB
            $errors[] = 'PHP memory_limit 128M nebo více je doporučen.';
        }
        
        // Write permissions
        $upload_dir = wp_upload_dir();
        if (!is_writable($upload_dir['basedir'])) {
            $errors[] = 'Upload directory není zapisovatelný.';
        }
        
        return $errors;
    }
    
    /**
     * Get activation status
     */
    public static function get_activation_status() {
        return [
            'activated_at' => get_option('kafanek_brain_activated_at'),
            'version' => get_option('kafanek_brain_version'),
            'database_version' => get_option('kafanek_brain_db_version'),
            'tables_exist' => self::check_tables_exist(),
            'requirements_met' => empty(self::check_requirements())
        ];
    }
    
    /**
     * Check if all tables exist
     */
    private static function check_tables_exist() {
        global $wpdb;
        
        $tables = [
            'kafanek_brain_cache',
            'kafanek_brain_usage',
            'kafanek_brain_neural_models',
            'kafanek_brain_brand_voices',
            'kafanek_chatbot_conversations'
        ];
        
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . $table;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
                return false;
            }
        }
        
        return true;
    }
}
