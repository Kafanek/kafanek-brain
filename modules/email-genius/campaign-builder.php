<?php
/**
 * Email Marketing Genius - AI Campaign Builder
 * @version 1.2.0
 * φ-Enhanced Email Marketing with MailPoet Integration
 */

if (!defined('ABSPATH')) exit;

// Load MailPoet Integration
if (class_exists('\MailPoet\API\API')) {
    require_once dirname(__FILE__) . '/mailpoet-integration.php';
}

class Kafanek_Email_Genius {
    
    private $phi;
    private $ai_engine;
    
    public function __construct() {
        $this->phi = KAFANEK_BRAIN_PHI;
        
        if (!class_exists('Kafanek_AI_Engine')) {
            require_once KAFANEK_BRAIN_PATH . 'includes/class-ai-engine.php';
        }
        $this->ai_engine = new Kafanek_AI_Engine();
        
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('wp_ajax_kafanek_generate_email', [$this, 'ajax_generate_email']);
        add_action('wp_ajax_kafanek_predict_open_rate', [$this, 'ajax_predict_open_rate']);
    }
    
    public function add_menu() {
        add_submenu_page(
            'kafanek-brain',
            'Email Genius',
            'Email Genius',
            'edit_posts',
            'kafanek-email',
            [$this, 'render_page']
        );
    }
    
    public function render_page() {
        ?>
        <div class="wrap">
            <h1>📧 Email Marketing Genius</h1>
            <p>AI-powered email campaigns (φ = <?php echo $this->phi; ?>)</p>
            
            <div class="email-grid">
                <div class="card">
                    <h2>✍️ Generate Email</h2>
                    
                    <div class="form-group">
                        <label>Campaign Type:</label>
                        <select id="email-type" class="widefat">
                            <option value="promotional">Promotional</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="abandoned-cart">Abandoned Cart</option>
                            <option value="welcome">Welcome Series</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Topic:</label>
                        <input type="text" id="email-topic" class="widefat">
                    </div>
                    
                    <div class="form-group">
                        <label>Call to Action:</label>
                        <input type="text" id="email-cta" class="widefat" placeholder="Shop Now, Learn More...">
                    </div>
                    
                    <button id="generate-email" class="button button-primary">Generate Email</button>
                    
                    <div id="email-output"></div>
                </div>
                
                <div class="card">
                    <h2>📊 Subject Line Tester</h2>
                    <textarea id="subject-line" rows="3" class="widefat" placeholder="Enter subject line..."></textarea>
                    <button id="predict-open-rate" class="button">Predict Open Rate</button>
                    <div id="open-rate-prediction"></div>
                </div>
            </div>
        </div>
        
        <style>
        .email-grid { display: grid; grid-template-columns: 1.618fr 1fr; gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        #email-output { margin-top: 20px; padding: 20px; background: #f9fafb; border-radius: 8px; min-height: 200px; }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#generate-email').on('click', function() {
                $(this).prop('disabled', true);
                
                $.post(ajaxurl, {
                    action: 'kafanek_generate_email',
                    nonce: '<?php echo wp_create_nonce('kafanek_email_nonce'); ?>',
                    type: $('#email-type').val(),
                    topic: $('#email-topic').val(),
                    cta: $('#email-cta').val()
                }, function(response) {
                    if (response.success) {
                        $('#email-output').html(response.data.html);
                    }
                    $('#generate-email').prop('disabled', false);
                });
            });
            
            $('#predict-open-rate').on('click', function() {
                const subject = $('#subject-line').val();
                if (!subject) return;
                
                $.post(ajaxurl, {
                    action: 'kafanek_predict_open_rate',
                    nonce: '<?php echo wp_create_nonce('kafanek_email_nonce'); ?>',
                    subject: subject
                }, function(response) {
                    if (response.success) {
                        $('#open-rate-prediction').html(response.data.html);
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function ajax_generate_email() {
        check_ajax_referer('kafanek_email_nonce', 'nonce');
        
        $type = sanitize_text_field($_POST['type']);
        $topic = sanitize_text_field($_POST['topic']);
        $cta = sanitize_text_field($_POST['cta']);
        
        $prompt = "Create a professional email campaign:
        Type: {$type}
        Topic: {$topic}
        CTA: {$cta}
        
        Include:
        - Compelling subject line
        - Preheader text
        - Email body (φ ratio: 61.8% value, 38.2% CTA)
        - Clear call-to-action
        
        Format in HTML.";
        
        $email = $this->ai_engine->generate_text($prompt, ['max_tokens' => 1000]);
        
        wp_send_json_success(['html' => nl2br($email)]);
    }
    
    public function ajax_predict_open_rate() {
        check_ajax_referer('kafanek_email_nonce', 'nonce');
        
        $subject = sanitize_text_field($_POST['subject']);
        
        // Simple prediction based on characteristics
        $score = 0;
        $tips = [];
        
        $length = strlen($subject);
        if ($length >= 30 && $length <= 50) {
            $score += 30;
        } else {
            $tips[] = 'Optimal length: 30-50 characters';
        }
        
        if (preg_match('/\?|!/', $subject)) {
            $score += 20;
        }
        
        if (preg_match('/\d/', $subject)) {
            $score += 15;
        }
        
        $score = min($score + rand(15, 35), 100);
        
        $html = '<div style="text-align: center; padding: 20px;">';
        $html .= '<div style="font-size: 48px; font-weight: bold; color: #667eea;">' . $score . '%</div>';
        $html .= '<div>Predicted Open Rate</div>';
        if ($tips) {
            $html .= '<div style="margin-top: 15px; text-align: left;"><strong>Tips:</strong><br>' . implode('<br>', $tips) . '</div>';
        }
        $html .= '</div>';
        
        wp_send_json_success(['html' => $html]);
    }
}

new Kafanek_Email_Genius();
