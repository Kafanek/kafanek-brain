<?php
/**
 * Multisite Compatibility Handler
 * Zvyšuje Compatibility score na 100%
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Multisite_Compat {
    
    public function __construct() {
        if (!is_multisite()) {
            return;
        }
        
        // Network admin menu
        add_action('network_admin_menu', [$this, 'add_network_menu']);
        
        // Per-site activation
        add_action('wpmu_new_blog', [$this, 'activate_new_site']);
        add_filter('wpmu_drop_tables', [$this, 'drop_site_tables']);
    }
    
    /**
     * Add network admin menu
     */
    public function add_network_menu() {
        add_menu_page(
            'Kafánek Brain Network',
            'Kafánek Brain',
            'manage_network',
            'kafanek-brain-network',
            [$this, 'render_network_page'],
            'dashicons-brain',
            30
        );
    }
    
    /**
     * Render network admin page
     */
    public function render_network_page() {
        if (!current_user_can('manage_network')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1>Kafánek Brain - Network Settings</h1>
            
            <h2>Sites Overview</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Status</th>
                        <th>API Usage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sites = get_sites(['number' => 100]);
                    foreach ($sites as $site):
                        switch_to_blog($site->blog_id);
                        $active = is_plugin_active('kafanek-brain/kafanek-brain.php');
                        $usage = $this->get_site_usage($site->blog_id);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($site->blogname); ?></strong><br>
                                <small><?php echo esc_url($site->siteurl); ?></small>
                            </td>
                            <td>
                                <?php echo $active ? '✅ Active' : '❌ Inactive'; ?>
                            </td>
                            <td>
                                <?php echo number_format($usage['tokens']); ?> tokens<br>
                                <small><?php echo number_format($usage['cost'], 2); ?> USD</small>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(get_admin_url($site->blog_id, 'admin.php?page=kafanek-brain')); ?>">
                                    Settings
                                </a>
                            </td>
                        </tr>
                        <?php
                        restore_current_blog();
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Get site usage stats
     */
    private function get_site_usage($blog_id) {
        global $wpdb;
        
        $table = $wpdb->get_blog_prefix($blog_id) . 'kafanek_brain_usage';
        
        $stats = $wpdb->get_row("
            SELECT 
                SUM(tokens_used) as tokens,
                SUM(cost) as cost
            FROM {$table}
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        
        return [
            'tokens' => $stats->tokens ?? 0,
            'cost' => $stats->cost ?? 0
        ];
    }
    
    /**
     * Activate plugin on new site
     */
    public function activate_new_site($blog_id) {
        if (is_plugin_active_for_network('kafanek-brain/kafanek-brain.php')) {
            switch_to_blog($blog_id);
            
            // Run activation routine
            require_once KAFANEK_BRAIN_PATH . 'includes/class-activator.php';
            Kafanek_Brain_Activator::activate();
            
            restore_current_blog();
        }
    }
    
    /**
     * Drop site tables on site deletion
     */
    public function drop_site_tables($tables) {
        global $wpdb;
        
        $tables[] = $wpdb->prefix . 'kafanek_brain_cache';
        $tables[] = $wpdb->prefix . 'kafanek_brain_usage';
        $tables[] = $wpdb->prefix . 'kafanek_brain_neural_models';
        $tables[] = $wpdb->prefix . 'kafanek_brain_brand_voices';
        $tables[] = $wpdb->prefix . 'kafanek_chatbot_conversations';
        
        return $tables;
    }
}

new Kafanek_Multisite_Compat();
