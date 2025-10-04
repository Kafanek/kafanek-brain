<?php
/**
 * AI Content Studio - Professional Copywriter
 * @version 1.2.0
 * Golden Ratio (φ = 1.618) Enhanced
 */

if (!defined('ABSPATH')) exit;

class Kafanek_AI_Copywriter {
    
    private $phi;
    private $ai_engine;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        $this->ai_engine = new Kafanek_AI_Engine();
        
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('wp_ajax_kafanek_generate_copy', [$this, 'ajax_generate_copy']);
        add_action('wp_ajax_kafanek_analyze_brand_voice', [$this, 'ajax_analyze_brand_voice']);
    }
    
    public function add_menu() {
        add_submenu_page(
            'kafanek-brain',
            'AI Copywriter',
            'AI Copywriter',
            'edit_posts',
            'kafanek-copywriter',
            [$this, 'render_page']
        );
    }
    
    public function render_page() {
        ?>
        <div class="wrap kafanek-copywriter">
            <h1>✍️ AI Professional Copywriter</h1>
            <p>Powered by φ = <?php echo $this->phi; ?></p>
            
            <div class="copywriter-grid">
                <!-- Content Type Selector -->
                <div class="card">
                    <h2>📝 Content Type</h2>
                    <select id="content-type" class="widefat">
                        <option value="product-description">Product Description</option>
                        <option value="blog-post">Blog Post</option>
                        <option value="landing-page">Landing Page</option>
                        <option value="email">Email Campaign</option>
                        <option value="social-media">Social Media Post</option>
                        <option value="ad-copy">Ad Copy</option>
                        <option value="video-script">Video Script</option>
                    </select>
                </div>
                
                <!-- Input Details -->
                <div class="card">
                    <h2>🎯 Input Details</h2>
                    <div class="form-group">
                        <label>Topic / Product Name:</label>
                        <input type="text" id="copy-topic" class="widefat" placeholder="Organic Coffee Beans">
                    </div>
                    
                    <div class="form-group">
                        <label>Key Points (comma separated):</label>
                        <textarea id="copy-points" rows="3" class="widefat" placeholder="Fair trade, organic, intense flavor"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Target Audience:</label>
                        <input type="text" id="copy-audience" class="widefat" value="<?php echo esc_attr(get_option('kafanek_target_audience', 'dospělí zákazníci')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Brand Voice:</label>
                        <select id="copy-voice" class="widefat">
                            <option value="professional">Professional & Trustworthy</option>
                            <option value="friendly">Friendly & Casual</option>
                            <option value="luxury">Luxury & Exclusive</option>
                            <option value="playful">Playful & Fun</option>
                            <option value="inspiring">Inspiring & Motivational</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Length (words):</label>
                        <input type="number" id="copy-length" class="widefat" value="<?php echo round(150 * $this->phi); ?>">
                        <small>Default: 150 × φ = <?php echo round(150 * $this->phi); ?> words</small>
                    </div>
                    
                    <button id="generate-copy" class="button button-primary button-hero">
                        ✨ Generate Copy
                    </button>
                </div>
                
                <!-- Generated Output -->
                <div class="card output-card">
                    <h2>📄 Generated Copy</h2>
                    <div id="copy-output"></div>
                    <div id="copy-actions" style="display:none;">
                        <button class="button" id="copy-to-clipboard">📋 Copy to Clipboard</button>
                        <button class="button" id="save-as-post">💾 Save as Draft</button>
                        <button class="button" id="refine-copy">🔄 Refine</button>
                    </div>
                </div>
                
                <!-- Brand Voice Analyzer -->
                <div class="card">
                    <h2>🎨 Brand Voice Analyzer</h2>
                    <p>Upload or paste existing content to learn your brand voice:</p>
                    <textarea id="brand-sample" rows="5" class="widefat" placeholder="Paste your existing content here..."></textarea>
                    <button id="analyze-voice" class="button">🔍 Analyze Brand Voice</button>
                    <div id="voice-analysis"></div>
                </div>
                
                <!-- SEO Optimizer -->
                <div class="card">
                    <h2>📊 SEO Score</h2>
                    <div id="seo-score">
                        <div class="score-circle">
                            <span id="seo-value">--</span>
                            <small>/100</small>
                        </div>
                        <div id="seo-tips"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .copywriter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .output-card {
            grid-column: 1 / -1;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        #copy-output {
            min-height: 200px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 15px;
            line-height: 1.8;
        }
        #copy-actions {
            display: flex;
            gap: 10px;
        }
        .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto 20px;
        }
        #seo-value {
            font-size: 32px;
            font-weight: bold;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            const phi = <?php echo $this->phi; ?>;
            
            $('#generate-copy').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).text('⏳ Generating...');
                
                $.post(ajaxurl, {
                    action: 'kafanek_generate_copy',
                    nonce: '<?php echo wp_create_nonce('kafanek_copy_nonce'); ?>',
                    content_type: $('#content-type').val(),
                    topic: $('#copy-topic').val(),
                    points: $('#copy-points').val(),
                    audience: $('#copy-audience').val(),
                    voice: $('#copy-voice').val(),
                    length: $('#copy-length').val()
                }, function(response) {
                    if (response.success) {
                        $('#copy-output').html(response.data.copy);
                        $('#copy-actions').show();
                        
                        // SEO Score
                        $('#seo-value').text(response.data.seo_score || '--');
                        $('#seo-tips').html(response.data.seo_tips || '');
                    } else {
                        alert('Error: ' + response.data);
                    }
                    btn.prop('disabled', false).text('✨ Generate Copy');
                });
            });
            
            $('#analyze-voice').on('click', function() {
                const sample = $('#brand-sample').val();
                if (!sample) {
                    alert('Please paste some content first');
                    return;
                }
                
                $(this).prop('disabled', true).text('⏳ Analyzing...');
                
                $.post(ajaxurl, {
                    action: 'kafanek_analyze_brand_voice',
                    nonce: '<?php echo wp_create_nonce('kafanek_copy_nonce'); ?>',
                    sample: sample
                }, function(response) {
                    if (response.success) {
                        $('#voice-analysis').html(response.data.analysis);
                    }
                    $('#analyze-voice').prop('disabled', false).text('🔍 Analyze Brand Voice');
                });
            });
            
            $('#copy-to-clipboard').on('click', function() {
                const text = $('#copy-output').text();
                navigator.clipboard.writeText(text);
                alert('✅ Copied to clipboard!');
            });
        });
        </script>
        <?php
    }
    
    public function ajax_generate_copy() {
        check_ajax_referer('kafanek_copy_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $content_type = sanitize_text_field($_POST['content_type']);
        $topic = sanitize_text_field($_POST['topic']);
        $points = sanitize_textarea_field($_POST['points']);
        $audience = sanitize_text_field($_POST['audience']);
        $voice = sanitize_text_field($_POST['voice']);
        $length = intval($_POST['length']);
        
        // Build prompt based on content type
        $prompt = $this->build_copy_prompt($content_type, $topic, $points, $audience, $voice, $length);
        
        // Generate with AI
        $copy = $this->ai_engine->generate_text($prompt, [
            'max_tokens' => $length * 2,
            'temperature' => 0.8
        ]);
        
        // Calculate SEO score
        $seo_data = $this->calculate_seo_score($copy, $topic);
        
        wp_send_json_success([
            'copy' => nl2br($copy),
            'seo_score' => $seo_data['score'],
            'seo_tips' => $seo_data['tips']
        ]);
    }
    
    private function build_copy_prompt($type, $topic, $points, $audience, $voice, $length) {
        $voice_descriptions = [
            'professional' => 'Professional, trustworthy, expertise-focused',
            'friendly' => 'Friendly, conversational, approachable',
            'luxury' => 'Luxury, exclusive, sophisticated',
            'playful' => 'Playful, fun, energetic',
            'inspiring' => 'Inspiring, motivational, aspirational'
        ];
        
        $prompts = [
            'product-description' => "Write a compelling product description for '{$topic}'. 
                Key features: {$points}
                Target audience: {$audience}
                Brand voice: {$voice_descriptions[$voice]}
                Length: {$length} words
                
                Requirements:
                - Highlight benefits, not just features
                - Use emotional triggers
                - Include strong CTA
                - SEO optimized with natural keywords
                - Golden Ratio structure (φ = {$this->phi}): 61.8% benefits, 38.2% features",
            
            'blog-post' => "Write a blog post about '{$topic}'.
                Key points to cover: {$points}
                Target audience: {$audience}
                Tone: {$voice_descriptions[$voice]}
                Length: {$length} words
                
                Structure (φ-based):
                - Intro (38.2%): Hook + Problem
                - Main content (61.8%): Solutions + Examples
                - Conclusion: Summary + CTA",
            
            'landing-page' => "Write landing page copy for '{$topic}'.
                USPs: {$points}
                Target: {$audience}
                Voice: {$voice_descriptions[$voice]}
                
                Include:
                - Powerful headline
                - Subheadline
                - 3-5 benefit bullets
                - Social proof
                - Strong CTA
                
                φ principle: 61.8% emotional appeal, 38.2% logical arguments",
        ];
        
        return $prompts[$type] ?? $prompts['product-description'];
    }
    
    private function calculate_seo_score($content, $keyword) {
        $score = 0;
        $tips = [];
        
        // Keyword density (optimal: 1-2%)
        $word_count = str_word_count($content);
        $keyword_count = substr_count(strtolower($content), strtolower($keyword));
        $density = ($keyword_count / $word_count) * 100;
        
        if ($density >= 1 && $density <= 2) {
            $score += 30;
        } else {
            $tips[] = "Keyword density: {$density}% (optimal: 1-2%)";
        }
        
        // Length (optimal based on φ)
        $optimal_length = round(300 * $this->phi); // ~485 words
        if ($word_count >= $optimal_length * 0.8) {
            $score += 25;
        } else {
            $tips[] = "Content length: {$word_count} words (optimal: {$optimal_length}+)";
        }
        
        // Headings
        if (preg_match('/<h[1-6]>/', $content)) {
            $score += 15;
        } else {
            $tips[] = "Add headings (H2, H3) for better structure";
        }
        
        // CTA
        if (stripos($content, 'buy') || stripos($content, 'order') || stripos($content, 'get')) {
            $score += 15;
        } else {
            $tips[] = "Add clear call-to-action";
        }
        
        // Reading ease
        $sentences = substr_count($content, '.') + substr_count($content, '!') + substr_count($content, '?');
        $avg_words_per_sentence = $word_count / max($sentences, 1);
        if ($avg_words_per_sentence <= 20) {
            $score += 15;
        } else {
            $tips[] = "Shorten sentences for better readability";
        }
        
        return [
            'score' => min($score, 100),
            'tips' => implode('<br>', $tips)
        ];
    }
    
    public function ajax_analyze_brand_voice() {
        check_ajax_referer('kafanek_copy_nonce', 'nonce');
        
        $sample = sanitize_textarea_field($_POST['sample']);
        
        $prompt = "Analyze this brand's writing style and voice:

{$sample}

Provide analysis of:
1. Tone (formal/casual/playful/etc.)
2. Common phrases and patterns
3. Sentence structure
4. Emotional triggers used
5. Target audience indicators

Format as HTML with bullet points.";
        
        $analysis = $this->ai_engine->generate_text($prompt, [
            'max_tokens' => 500
        ]);
        
        // Save learned voice
        update_option('kafanek_learned_brand_voice', $analysis);
        
        wp_send_json_success([
            'analysis' => nl2br($analysis)
        ]);
    }
}

// Initialize
new Kafanek_AI_Copywriter();
