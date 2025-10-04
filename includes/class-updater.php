<?php
/**
 * Kafanek Brain Auto-Updater via GitHub
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Brain_Updater {
    
    private $github_username = 'kolibric'; // Změňte na váš GitHub username
    private $github_repo = 'kafanek-brain'; // Název vašeho repo
    private $plugin_slug = 'kafanek-brain/kafanek-brain.php';
    private $current_version;
    
    public function __construct() {
        $this->current_version = KAFANEK_BRAIN_VERSION;
        
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }
    
    /**
     * Check GitHub for new version
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->current_version, $remote_version, '<')) {
            $download_url = $this->get_download_url();
            
            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->new_version = $remote_version;
            $obj->package = $download_url;
            
            $transient->response[$this->plugin_slug] = $obj;
        }
        
        return $transient;
    }
    
    /**
     * Get latest version from GitHub
     */
    private function get_remote_version() {
        $cache_key = 'kafanek_brain_remote_version';
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $api_url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->github_username,
            $this->github_repo
        );
        
        $response = wp_remote_get($api_url, ['timeout' => 10]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $version = isset($body['tag_name']) ? ltrim($body['tag_name'], 'v') : false;
        
        // Cache for 12 hours
        set_transient($cache_key, $version, 12 * HOUR_IN_SECONDS);
        
        return $version;
    }
    
    /**
     * Get download URL for latest release
     */
    private function get_download_url() {
        $api_url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->github_username,
            $this->github_repo
        );
        
        $response = wp_remote_get($api_url, ['timeout' => 10]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        return $body['zipball_url'] ?? false;
    }
    
    /**
     * Plugin info for update screen
     */
    public function plugin_info($false, $action, $response) {
        if ($action !== 'plugin_information') {
            return $false;
        }
        
        if ($response->slug !== $this->plugin_slug) {
            return $false;
        }
        
        $remote_version = $this->get_remote_version();
        
        $info = new stdClass();
        $info->name = 'Kafánkův Mozek';
        $info->slug = $this->plugin_slug;
        $info->version = $remote_version;
        $info->author = '<a href="https://mykolibri-academy.cz">Kolibri Academy</a>';
        $info->homepage = 'https://mykolibri-academy.cz';
        $info->download_link = $this->get_download_url();
        $info->requires = '6.0';
        $info->tested = '6.4';
        $info->requires_php = '7.4';
        
        $info->sections = [
            'description' => 'Inteligentní AI asistent pro WordPress s WooCommerce a Claude/OpenAI integrací.',
            'changelog' => $this->get_changelog()
        ];
        
        return $info;
    }
    
    /**
     * Get changelog from GitHub
     */
    private function get_changelog() {
        $api_url = sprintf(
            'https://api.github.com/repos/%s/%s/releases',
            $this->github_username,
            $this->github_repo
        );
        
        $response = wp_remote_get($api_url, ['timeout' => 10]);
        
        if (is_wp_error($response)) {
            return 'Changelog nedostupný.';
        }
        
        $releases = json_decode(wp_remote_retrieve_body($response), true);
        
        $changelog = '<h3>Changelog</h3>';
        foreach (array_slice($releases, 0, 5) as $release) {
            $changelog .= '<h4>Version ' . ltrim($release['tag_name'], 'v') . '</h4>';
            $changelog .= '<p><em>' . date('Y-m-d', strtotime($release['published_at'])) . '</em></p>';
            $changelog .= '<div>' . wp_kses_post($release['body']) . '</div>';
        }
        
        return $changelog;
    }
}

// Initialize updater
new Kafanek_Brain_Updater();
