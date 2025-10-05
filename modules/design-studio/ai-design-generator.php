<?php
/**
 * Kafánek Brain - AI Design Studio
 * Ultimate AI Design Generator with Golden Ratio
 * @version 1.2.1
 */

if (!defined('ABSPATH')) exit;

class Kafanek_AI_Design_Studio {
    
    private $phi;
    private $golden_angle;
    private $ai_engine;
    
    // Design categories
    private $categories = [
        'logo' => 'Logo Design',
        'web_design' => 'Web Design',
        'ui_ux' => 'UI/UX Design',
        'architecture' => 'Architecture & Interiors',
        'brand_identity' => 'Brand Identity',
        '3d_visualization' => '3D Visualization'
    ];
    
    // Styles per category
    private $styles = [
        'logo' => ['minimalist', 'vintage', 'modern', 'luxury', 'tech', 'organic', 'geometric', 'handdrawn', 'badge', 'mascot'],
        'web_design' => ['saas_landing', 'ecommerce', 'portfolio', 'corporate', 'startup', 'agency', 'blog', 'dashboard'],
        'ui_ux' => ['mobile_app', 'dashboard', 'wireframe', 'design_system', 'dark_mode', 'glassmorphism', 'neumorphism'],
        'architecture' => ['modern_house', 'villa', 'office', 'restaurant', 'garden', 'interior', 'facade'],
        'brand_identity' => ['business_card', 'letterhead', 'social_media', 'packaging', 'brand_book'],
        '3d_visualization' => ['product_render', 'architectural_viz', 'character', 'nft_art', 'metaverse']
    ];
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        $this->golden_angle = 137.5;
        
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        $this->ai_engine = new Kafanek_AI_Engine();
        
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_kafanek_generate_design', [$this, 'ajax_generate_design']);
        add_action('wp_ajax_kafanek_generate_palette', [$this, 'ajax_generate_palette']);
        add_action('wp_ajax_kafanek_apply_golden_grid', [$this, 'ajax_apply_golden_grid']);
        
        // Shortcodes
        add_shortcode('kafanek_design', [$this, 'design_shortcode']);
        add_shortcode('kafanek_design_gallery', [$this, 'gallery_shortcode']);
        add_shortcode('kafanek_design_button', [$this, 'button_shortcode']);
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'kafanek-brain',
            '🎨 AI Design Studio',
            '🎨 Design Studio',
            'edit_posts',
            'kafanek-design-studio',
            [$this, 'render_admin_page']
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ('kafanek-brain_page_kafanek-design-studio' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'kafanek-design-studio',
            KAFANEK_BRAIN_URL . 'assets/css/design-studio.css',
            [],
            KAFANEK_BRAIN_VERSION
        );
        
        wp_enqueue_script(
            'kafanek-design-studio',
            KAFANEK_BRAIN_URL . 'assets/js/design-studio.js',
            ['jquery'],
            KAFANEK_BRAIN_VERSION,
            true
        );
        
        wp_localize_script('kafanek-design-studio', 'kafanekDesign', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kafanek_design_nonce'),
            'phi' => $this->phi,
            'golden_angle' => $this->golden_angle,
            'categories' => $this->categories,
            'styles' => $this->styles
        ]);
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap kafanek-design-studio-wrap">
            <h1>🎨 AI Design Studio</h1>
            <p class="subtitle">Powered by Golden Ratio (φ = <?php echo $this->phi; ?>)</p>
            
            <div class="kafanek-design-studio">
                <!-- Category Selection -->
                <div class="design-generator-panel">
                    <h2>🚀 Quick Generate</h2>
                    
                    <div class="category-grid">
                        <?php foreach ($this->categories as $key => $label): ?>
                            <button class="category-btn" data-category="<?php echo esc_attr($key); ?>">
                                <span class="icon"><?php echo $this->get_category_icon($key); ?></span>
                                <span class="label"><?php echo esc_html($label); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Style Selection -->
                    <div class="style-selection" style="display:none;">
                        <h3>Vyber styl:</h3>
                        <div class="styles-grid" id="styles-grid"></div>
                    </div>
                    
                    <!-- Description -->
                    <div class="design-description-section">
                        <h3>Popiš svou vizi:</h3>
                        <textarea id="design-description" 
                                  class="design-description-textarea" 
                                  placeholder="Např: Modern coffee shop logo with coffee bean, minimalist, warm colors..."
                                  rows="4"></textarea>
                    </div>
                    
                    <!-- Color Palette -->
                    <div class="color-palette-section">
                        <h3>Barevná paleta (volitelné):</h3>
                        <div class="color-inputs">
                            <input type="color" id="color1" value="#697077">
                            <input type="color" id="color2" value="#4a5058">
                            <input type="color" id="color3" value="#2c3036">
                            <button class="btn-secondary" id="generate-palette-btn">
                                ✨ Vygenerovat Golden Paletu
                            </button>
                        </div>
                        <div id="generated-palette" class="generated-palette"></div>
                    </div>
                    
                    <!-- Advanced Options -->
                    <div class="advanced-options">
                        <h3>Pokročilé možnosti:</h3>
                        <label class="option-label">
                            <input type="checkbox" id="use-golden-ratio" checked>
                            <span>Použít Golden Ratio kompozici (φ)</span>
                        </label>
                        <label class="option-label">
                            <input type="checkbox" id="generate-variations" checked>
                            <span>Generovat varianty (3x)</span>
                        </label>
                        <label class="option-label">
                            <input type="checkbox" id="create-mockups">
                            <span>Vytvořit mockupy (vizitka, web, tričko)</span>
                        </label>
                        <label class="option-label">
                            <input type="checkbox" id="apply-fibonacci">
                            <span>Fibonacci spirála overlay</span>
                        </label>
                        
                        <div class="quality-selector">
                            <label>Kvalita:</label>
                            <select id="image-quality">
                                <option value="standard">Standard (512x512)</option>
                                <option value="hd" selected>HD (1024x1024)</option>
                                <option value="ultra">Ultra HD (1792x1024)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Generate Button -->
                    <button class="btn-primary btn-large" id="generate-design-btn">
                        <span class="btn-icon">🚀</span>
                        <span class="btn-text">Vygenerovat Design s AI</span>
                    </button>
                    
                    <div class="loading-indicator" style="display:none;">
                        <div class="spinner"></div>
                        <p>Generuji váš design s Golden Ratio magic...</p>
                    </div>
                </div>
                
                <!-- Results Panel -->
                <div class="design-results-panel" style="display:none;">
                    <h2>✨ Výsledky</h2>
                    
                    <div class="main-design-container">
                        <div class="design-image-wrapper">
                            <img id="main-design-image" src="" alt="Generated Design">
                            <canvas id="golden-grid-canvas" class="grid-overlay"></canvas>
                        </div>
                        
                        <div class="design-tools">
                            <button class="tool-btn" data-action="toggle-grid">
                                📏 Golden Grid
                            </button>
                            <button class="tool-btn" data-action="extract-colors">
                                🎨 Extrahovat Barvy
                            </button>
                            <button class="tool-btn" data-action="crop-ratio">
                                📐 Oříznout na φ
                            </button>
                            <button class="tool-btn" data-action="upscale">
                                ⬆️ Upscale 4K
                            </button>
                            <button class="tool-btn" data-action="remove-bg">
                                🗑️ Odstranit Pozadí
                            </button>
                            <button class="tool-btn" data-action="download">
                                💾 Stáhnout
                            </button>
                        </div>
                    </div>
                    
                    <!-- Variations -->
                    <div class="variations-container">
                        <h3>🎭 Varianty</h3>
                        <div class="variations-grid" id="variations-grid"></div>
                    </div>
                    
                    <!-- Mockups -->
                    <div class="mockups-container" style="display:none;">
                        <h3>📱 Mockupy</h3>
                        <div class="mockups-grid" id="mockups-grid"></div>
                    </div>
                    
                    <!-- Extracted Colors -->
                    <div class="extracted-colors-container">
                        <h3>🎨 Barevná Paleta</h3>
                        <div class="color-swatches" id="color-swatches"></div>
                    </div>
                </div>
            </div>
            
            <!-- Design History -->
            <div class="design-history">
                <h2>📜 Historie Designů</h2>
                <div class="history-grid" id="history-grid">
                    <p class="empty-state">Zatím žádné designy. Vygeneruj svůj první! 🚀</p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Generate Design
     */
    public function ajax_generate_design() {
        check_ajax_referer('kafanek_design_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Nedostatečná oprávnění']);
        }
        
        $category = sanitize_text_field($_POST['category']);
        $style = sanitize_text_field($_POST['style']);
        $description = sanitize_textarea_field($_POST['description']);
        $colors = isset($_POST['colors']) ? array_map('sanitize_text_field', $_POST['colors']) : [];
        $use_golden_ratio = isset($_POST['use_golden_ratio']) && $_POST['use_golden_ratio'] === 'true';
        $quality = sanitize_text_field($_POST['quality'] ?? 'hd');
        
        // Build prompt with Golden Ratio instructions
        $prompt = $this->build_design_prompt($category, $style, $description, $colors, $use_golden_ratio);
        
        // Generate image using AI
        $image_params = [
            'prompt' => $prompt,
            'size' => $this->get_image_size($quality),
            'quality' => $quality === 'ultra' ? 'hd' : 'standard',
            'style' => $this->map_style_to_ai($style)
        ];
        
        $result = $this->ai_engine->generate_image($prompt, $image_params);
        
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }
        
        // Save to media library
        $image_url = $result['url'];
        $attachment_id = $this->save_to_media_library($image_url, $description);
        
        // Generate variations if requested
        $variations = [];
        if (isset($_POST['generate_variations']) && $_POST['generate_variations'] === 'true') {
            $variations = $this->generate_variations($prompt, $image_params, 3);
        }
        
        // Store in history
        $this->save_to_history([
            'category' => $category,
            'style' => $style,
            'description' => $description,
            'attachment_id' => $attachment_id,
            'image_url' => wp_get_attachment_url($attachment_id),
            'variations' => $variations,
            'timestamp' => current_time('mysql')
        ]);
        
        wp_send_json_success([
            'image_url' => wp_get_attachment_url($attachment_id),
            'attachment_id' => $attachment_id,
            'variations' => $variations,
            'colors' => $this->extract_colors_from_description($colors)
        ]);
    }
    
    /**
     * Build AI prompt with Golden Ratio instructions
     */
    private function build_design_prompt($category, $style, $description, $colors, $use_golden_ratio) {
        $prompt = $description;
        
        // Add style prefix
        $style_modifiers = [
            'minimalist' => 'minimalist, clean, simple',
            'modern' => 'modern, contemporary, sleek',
            'luxury' => 'luxury, premium, elegant, sophisticated',
            'tech' => 'tech, futuristic, digital, innovative',
            'vintage' => 'vintage, retro, classic, nostalgic'
        ];
        
        if (isset($style_modifiers[$style])) {
            $prompt = $style_modifiers[$style] . ', ' . $prompt;
        }
        
        // Add Golden Ratio instructions
        if ($use_golden_ratio) {
            $prompt .= ', composition using golden ratio (φ = 1.618), Fibonacci spiral layout, harmonious proportions';
        }
        
        // Add color instructions
        if (!empty($colors)) {
            $color_list = implode(', ', $colors);
            $prompt .= ', color palette: ' . $color_list;
        }
        
        // Category-specific additions
        $category_additions = [
            'logo' => ', vector style, clean lines, scalable, professional logo design',
            'web_design' => ', modern web interface, UI elements, landing page layout',
            'ui_ux' => ', user interface design, mobile app screen, UX focused',
            'architecture' => ', architectural visualization, 3D render, realistic lighting',
            'brand_identity' => ', brand design, corporate identity, professional',
            '3d_visualization' => ', 3D render, photorealistic, high quality'
        ];
        
        if (isset($category_additions[$category])) {
            $prompt .= $category_additions[$category];
        }
        
        // Quality suffix
        $prompt .= ', high quality, professional, detailed, award-winning design';
        
        return $prompt;
    }
    
    /**
     * Generate Golden Ratio color palette
     */
    public function ajax_generate_palette() {
        check_ajax_referer('kafanek_design_nonce', 'nonce');
        
        $base_hue = isset($_POST['base_hue']) ? intval($_POST['base_hue']) : rand(0, 360);
        $palette = $this->generate_golden_palette($base_hue);
        
        wp_send_json_success(['palette' => $palette]);
    }
    
    private function generate_golden_palette($base_hue, $count = 5) {
        $palette = [];
        
        for ($i = 0; $i < $count; $i++) {
            $hue = ($base_hue + $i * $this->golden_angle) % 360;
            $saturation = 70;
            $lightness = 60 - ($i * 5); // Darker with each step
            
            $palette[] = [
                'hsl' => "hsl($hue, $saturation%, $lightness%)",
                'hex' => $this->hsl_to_hex($hue, $saturation, $lightness)
            ];
        }
        
        return $palette;
    }
    
    /**
     * Helper: Get category icon
     */
    private function get_category_icon($category) {
        $icons = [
            'logo' => '🎨',
            'web_design' => '💻',
            'ui_ux' => '📱',
            'architecture' => '🏛️',
            'brand_identity' => '🏷️',
            '3d_visualization' => '🎭'
        ];
        
        return $icons[$category] ?? '🎨';
    }
    
    /**
     * Helper: Get image size based on quality
     */
    private function get_image_size($quality) {
        $sizes = [
            'standard' => '512x512',
            'hd' => '1024x1024',
            'ultra' => '1792x1024'
        ];
        
        return $sizes[$quality] ?? '1024x1024';
    }
    
    /**
     * Helper: Save image to media library
     */
    private function save_to_media_library($image_url, $description) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $tmp = download_url($image_url);
        
        if (is_wp_error($tmp)) {
            return 0;
        }
        
        $file_array = [
            'name' => 'kafanek-design-' . time() . '.png',
            'tmp_name' => $tmp
        ];
        
        $id = media_handle_sideload($file_array, 0, $description);
        
        if (is_wp_error($id)) {
            @unlink($tmp);
            return 0;
        }
        
        return $id;
    }
    
    /**
     * Helper: HSL to HEX conversion
     */
    private function hsl_to_hex($h, $s, $l) {
        $h /= 360;
        $s /= 100;
        $l /= 100;
        
        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hue_to_rgb($p, $q, $h + 1/3);
            $g = $this->hue_to_rgb($p, $q, $h);
            $b = $this->hue_to_rgb($p, $q, $h - 1/3);
        }
        
        return sprintf("#%02x%02x%02x", round($r * 255), round($g * 255), round($b * 255));
    }
    
    private function hue_to_rgb($p, $q, $t) {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }
    
    /**
     * Shortcode: [kafanek_design]
     */
    public function design_shortcode($atts) {
        $atts = shortcode_atts([
            'type' => 'logo',
            'style' => 'modern',
            'description' => '',
            'width' => '100%',
            'height' => 'auto'
        ], $atts);
        
        // Return design generator embed
        ob_start();
        ?>
        <div class="kafanek-design-embed" 
             data-type="<?php echo esc_attr($atts['type']); ?>"
             data-style="<?php echo esc_attr($atts['style']); ?>">
            <button class="kafanek-design-trigger">
                🎨 Generate <?php echo esc_html($atts['type']); ?> Design
            </button>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Save design to history
     */
    private function save_to_history($data) {
        $history = get_option('kafanek_design_history', []);
        array_unshift($history, $data);
        
        // Keep only last 50
        $history = array_slice($history, 0, 50);
        
        update_option('kafanek_design_history', $history);
    }
    
    /**
     * Generate variations
     */
    private function generate_variations($base_prompt, $params, $count) {
        $variations = [];
        
        for ($i = 0; $i < $count; $i++) {
            $varied_prompt = $base_prompt . ', variation ' . ($i + 1);
            $result = $this->ai_engine->generate_image($varied_prompt, $params);
            
            if (!is_wp_error($result)) {
                $variations[] = [
                    'url' => $result['url'],
                    'prompt' => $varied_prompt
                ];
            }
        }
        
        return $variations;
    }
    
    /**
     * Extract colors from description
     */
    private function extract_colors_from_description($colors) {
        if (empty($colors)) {
            // Generate golden ratio palette
            return $this->generate_golden_palette(rand(0, 360));
        }
        
        return array_map(function($color) {
            return ['hex' => $color];
        }, $colors);
    }
    
    /**
     * Map style to AI provider style
     */
    private function map_style_to_ai($style) {
        $mappings = [
            'minimalist' => 'vivid',
            'modern' => 'vivid',
            'vintage' => 'natural',
            'luxury' => 'vivid'
        ];
        
        return $mappings[$style] ?? 'vivid';
    }
}

// Initialize
new Kafanek_AI_Design_Studio();
