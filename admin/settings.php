<?php
/**
 * Kafanek Brain Settings Page
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

if (isset($_POST['submit']) && check_admin_referer('kafanek_settings')) {
    update_option('kafanek_brain_api_key', sanitize_text_field($_POST['api_key']));
    update_option('kafanek_claude_api_key', sanitize_text_field($_POST['claude_api_key'] ?? ''));
    update_option('kafanek_gemini_api_key', sanitize_text_field($_POST['gemini_api_key'] ?? ''));
    update_option('kafanek_azure_speech_key', sanitize_text_field($_POST['azure_speech_key'] ?? ''));
    update_option('kafanek_azure_region', sanitize_text_field($_POST['azure_region'] ?? 'westeurope'));
    update_option('kafanek_ai_provider', sanitize_text_field($_POST['ai_provider'] ?? 'openai'));
    update_option('kafanek_brain_modules', $_POST['modules'] ?? []);
    update_option('kafanek_brand_voice', sanitize_textarea_field($_POST['brand_voice'] ?? ''));
    update_option('kafanek_target_audience', sanitize_text_field($_POST['target_audience'] ?? ''));
    update_option('kafanek_custom_instructions', sanitize_textarea_field($_POST['custom_instructions'] ?? ''));
    echo '<div class="notice notice-success is-dismissible"><p>✅ Nastavení uloženo!</p></div>';
}

$api_key = get_option('kafanek_brain_api_key', '');
$claude_api_key = get_option('kafanek_claude_api_key', '');
$gemini_api_key = get_option('kafanek_gemini_api_key', '');
$azure_speech_key = get_option('kafanek_azure_speech_key', '');
$azure_region = get_option('kafanek_azure_region', 'westeurope');
$ai_provider = get_option('kafanek_ai_provider', 'openai');
$modules = get_option('kafanek_brain_modules', []);
$brand_voice = get_option('kafanek_brand_voice', 'profesionální a přátelský');
$target_audience = get_option('kafanek_target_audience', 'dospělí zákazníci');
$custom_instructions = get_option('kafanek_custom_instructions', '');
?>

<div class="wrap">
    <h1>🧠 Kafánkův Mozek - Nastavení</h1>
    
    <form method="post">
        <?php wp_nonce_field('kafanek_settings'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="ai_provider">AI Provider</label>
                </th>
                <td>
                    <select name="ai_provider" id="ai_provider" class="regular-text">
                        <option value="openai" <?php selected($ai_provider, 'openai'); ?>>OpenAI (GPT)</option>
                        <option value="claude" <?php selected($ai_provider, 'claude'); ?>>Anthropic (Claude)</option>
                        <option value="gemini" <?php selected($ai_provider, 'gemini'); ?>>Google (Gemini)</option>
                    </select>
                    <p class="description">
                        Vyberte AI poskytovatele pro generování textu. <strong>Aktuálně: <?php 
                            echo $ai_provider === 'claude' ? 'Claude 🤖' : ($ai_provider === 'gemini' ? 'Gemini ✨' : 'OpenAI 🧠'); 
                        ?></strong>
                    </p>
                </td>
            </tr>
            
            <tr class="openai-settings" <?php echo $ai_provider !== 'openai' ? 'style="display:none;"' : ''; ?>>
                <th scope="row">
                    <label for="api_key">OpenAI API Key</label>
                </th>
                <td>
                    <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                    <p class="description">
                        Získejte API klíč na <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                    </p>
                </td>
            </tr>
            
            <tr class="claude-settings" <?php echo $ai_provider !== 'claude' ? 'style="display:none;"' : ''; ?>>
                <th scope="row">
                    <label for="claude_api_key">Anthropic Claude API Key</label>
                </th>
                <td>
                    <input type="text" name="claude_api_key" id="claude_api_key" value="<?php echo esc_attr($claude_api_key); ?>" class="regular-text" />
                    <p class="description">
                        Získejte API klíč na <a href="https://claude.ai/" target="_blank">Anthropic Claude</a>
                    </p>
                </td>
            </tr>
            
            <tr class="gemini-settings" <?php echo $ai_provider !== 'gemini' ? 'style="display:none;"' : ''; ?>>
                <th scope="row">
                    <label for="gemini_api_key">Google Gemini API Key</label>
                </th>
                <td>
                    <input type="text" name="gemini_api_key" id="gemini_api_key" value="<?php echo esc_attr($gemini_api_key); ?>" class="regular-text" />
                    <p class="description">
                        Získejte API klíč na <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a><br>
                        <strong>Model:</strong> Gemini 1.5 Flash (rychlý) nebo Gemini 1.5 Pro (kvalitní)
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Aktivní moduly</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="modules[woocommerce]" value="1" <?php checked(!empty($modules['woocommerce'])); ?>>
                            WooCommerce AI
                            <?php if (!class_exists('WooCommerce')): ?>
                                <span style="color: orange;">(WooCommerce není aktivní)</span>
                            <?php endif; ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="modules[elementor]" value="1" <?php checked(!empty($modules['elementor'])); ?>>
                            Elementor Widgets
                            <?php if (!did_action('elementor/loaded')): ?>
                                <span style="color: orange;">(Elementor není aktivní)</span>
                            <?php endif; ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="modules[neural]" value="1" <?php checked(!empty($modules['neural'])); ?>>
                            Neural Network
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Uložit nastavení'); ?>
    </form>
    
    <hr>
    
    <h2>📊 Status systému</h2>
    <table class="widefat">
        <thead>
            <tr>
                <th>Komponenta</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>API Key</strong></td>
                <td><?php echo empty($api_key) ? '❌ Nenastaveno' : '✅ Nastaveno'; ?></td>
            </tr>
            <tr>
                <td><strong>WooCommerce</strong></td>
                <td><?php echo class_exists('WooCommerce') ? '✅ Aktivní (v' . WC()->version . ')' : '❌ Neaktivní'; ?></td>
            </tr>
            <tr>
                <td><strong>Elementor</strong></td>
                <td><?php echo did_action('elementor/loaded') ? '✅ Aktivní' : '❌ Neaktivní'; ?></td>
            </tr>
            <tr>
                <td><strong>PHP Version</strong></td>
                <td><?php echo PHP_VERSION; ?> <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? '✅' : '❌'; ?></td>
            </tr>
            <tr>
                <td><strong>WordPress Version</strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
        </tbody>
    </table>
    
    <hr>
    
    <h2>🎓 Učení AI - Přizpůsobení</h2>
    <form method="post">
        <?php wp_nonce_field('kafanek_settings'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="brand_voice">Brand Voice (Hlas značky)</label>
                </th>
                <td>
                    <input type="text" name="brand_voice" id="brand_voice" value="<?php echo esc_attr($brand_voice); ?>" class="regular-text" />
                    <p class="description">
                        Jak má AI psát? Např: "profesionální a přátelský", "neformální a vtipný", "luxusní a exkluzivní"
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="target_audience">Cílová skupina</label>
                </th>
                <td>
                    <input type="text" name="target_audience" id="target_audience" value="<?php echo esc_attr($target_audience); ?>" class="regular-text" />
                    <p class="description">
                        Pro koho píšeme? Např: "mladí profesionálové 25-35 let", "podnikatelé", "studenti"
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="custom_instructions">Vlastní instrukce pro AI</label>
                </th>
                <td>
                    <textarea name="custom_instructions" id="custom_instructions" rows="5" class="large-text"><?php echo esc_textarea($custom_instructions); ?></textarea>
                    <p class="description">
                        Přidejte specifické pokyny pro AI. Např:<br>
                        - "Vždy zmiň udržitelnost a ekologii"<br>
                        - "Používej emoji ✨ pro zvýraznění"<br>
                        - "Zdůrazni českou výrobu"<br>
                        - "Přidej konkrétní čísla a statistiky"
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Uložit AI nastavení'); ?>
    </form>
    
    <hr>
    
    <h2>🎤 Azure Speech Services</h2>
    <form method="post">
        <?php wp_nonce_field('kafanek_settings'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="azure_speech_key">Azure Speech API Key</label>
                </th>
                <td>
                    <input type="text" name="azure_speech_key" id="azure_speech_key" value="<?php echo esc_attr($azure_speech_key); ?>" class="regular-text" />
                    <p class="description">
                        Získejte API klíč na <a href="https://portal.azure.com/#create/Microsoft.CognitiveServicesSpeechServices" target="_blank">Azure Portal</a><br>
                        <strong>Použití:</strong> Speech-to-Text pro hlasové vstupy do produktových popisů
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="azure_region">Azure Region</label>
                </th>
                <td>
                    <select name="azure_region" id="azure_region" class="regular-text">
                        <option value="westeurope" <?php selected($azure_region, 'westeurope'); ?>>West Europe</option>
                        <option value="northeurope" <?php selected($azure_region, 'northeurope'); ?>>North Europe</option>
                        <option value="eastus" <?php selected($azure_region, 'eastus'); ?>>East US</option>
                        <option value="westus" <?php selected($azure_region, 'westus'); ?>>West US</option>
                    </select>
                    <p class="description">
                        Vyberte region, kde máte Azure Speech service nasazený
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Uložit Azure nastavení'); ?>
    </form>
    
    <hr>
    
    <h2>🧪 Test API</h2>
    <p>
        <button type="button" class="button" id="test-api">Test OpenAI Connection</button>
        <span id="test-result" style="margin-left: 10px;"></span>
    </p>
    
    <script>
    jQuery(document).ready(function($) {
        // Toggle API key fields based on provider
        $('#ai_provider').on('change', function() {
            var provider = $(this).val();
            $('.openai-settings, .claude-settings, .gemini-settings').hide();
            
            if (provider === 'claude') {
                $('.claude-settings').show();
            } else if (provider === 'gemini') {
                $('.gemini-settings').show();
            } else {
                $('.openai-settings').show();
            }
        });
        
        $('#test-api').on('click', function() {
            var button = $(this);
            button.prop('disabled', true);
            $('#test-result').html('⏳ Testuji...');
            
            $.post(ajaxurl, {
                action: 'kafanek_generate_text',
                prompt: 'Hello, are you working?',
                nonce: '<?php echo wp_create_nonce('kafanek_ai_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    $('#test-result').html('<span style="color: green;">✅ API funguje!</span>');
                } else {
                    $('#test-result').html('<span style="color: red;">❌ Chyba: ' + response.data + '</span>');
                }
                button.prop('disabled', false);
            }).fail(function() {
                $('#test-result').html('<span style="color: red;">❌ AJAX chyba</span>');
                button.prop('disabled', false);
            });
        });
    });
    </script>
    
    <hr style="margin: 40px 0;">
    
    <h2>🔄 Verze a aktualizace</h2>
    <table class="form-table">
        <tr>
            <th scope="row">Aktuální verze</th>
            <td>
                <strong><?php echo KAFANEK_BRAIN_VERSION; ?></strong>
                <p class="description">
                    <?php 
                    $installed = get_option('kafanek_brain_version', KAFANEK_BRAIN_VERSION);
                    if ($installed !== KAFANEK_BRAIN_VERSION) {
                        echo '⚠️ Databáze verze: ' . $installed . ' (bude aktualizována při dalším načtení)';
                    } else {
                        echo '✅ Plugin je aktuální';
                    }
                    ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">Changelog</th>
            <td>
                <details>
                    <summary>📋 Historie verzí</summary>
                    <div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
                        <h4>Verze 1.2.0</h4>
                        <ul>
                            <li>✅ Multi-Provider AI Engine (OpenAI, Claude, Gemini, Azure)</li>
                            <li>✅ Fibonacci Neural Network integrace</li>
                            <li>✅ WooCommerce AI automation</li>
                            <li>✅ Elementor widgets (4 custom)</li>
                            <li>✅ Golden Ratio optimization (φ = 1.618)</li>
                            <li>✅ Claude Desktop MCP integration</li>
                            <li>✅ REST API endpoints (8 endpoints)</li>
                            <li>✅ Fibonacci cache (21 minut)</li>
                            <li>✅ Upgrade system pro snadné aktualizace</li>
                        </ul>
                    </div>
                </details>
            </td>
        </tr>
        <tr>
            <th scope="row">Aktualizace</th>
            <td>
                <p class="description">
                    Plugin používá <strong>automatické aktualizace z GitHub</strong>.<br>
                    Nové verze se zobrazí v Dashboard → Aktualizace.
                </p>
                <?php 
                $cache_key = 'kafanek_brain_remote_version';
                $remote = get_transient($cache_key);
                if ($remote && version_compare($remote, KAFANEK_BRAIN_VERSION, '>')) {
                    echo '<div style="padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; margin-top: 10px;">';
                    echo '🔔 <strong>Nová verze ' . esc_html($remote) . ' je k dispozici!</strong><br>';
                    echo '<a href="' . admin_url('update-core.php') . '">Přejít na aktualizace</a>';
                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
</div>
