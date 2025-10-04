<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1>💬 AI Chatbot Nastavení</h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('kafanek_chatbot');
        do_settings_sections('kafanek_chatbot');
        ?>
        
        <table class="form-table">
            <tr>
                <th>Povolit Chatbot</th>
                <td>
                    <label>
                        <input type="checkbox" name="kafanek_chatbot_enabled" value="1" 
                               <?php checked(get_option('kafanek_chatbot_enabled', '1'), '1'); ?>>
                        Zobrazit chatbot na webu
                    </label>
                </td>
            </tr>
            
            <tr>
                <th>Jméno bota</th>
                <td>
                    <input type="text" name="kafanek_chatbot_name" class="regular-text"
                           value="<?php echo esc_attr(get_option('kafanek_chatbot_name', 'Kafánek')); ?>">
                </td>
            </tr>
            
            <tr>
                <th>Uvítací zpráva</th>
                <td>
                    <textarea name="kafanek_chatbot_welcome" rows="3" class="large-text"><?php 
                        echo esc_textarea(get_option('kafanek_chatbot_welcome', 'Ahoj! Jak vám mohu pomoci?')); 
                    ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>Placeholder</th>
                <td>
                    <input type="text" name="kafanek_chatbot_placeholder" class="regular-text"
                           value="<?php echo esc_attr(get_option('kafanek_chatbot_placeholder', 'Napište zprávu...')); ?>">
                </td>
            </tr>
            
            <tr>
                <th>Pozice na stránce</th>
                <td>
                    <select name="kafanek_chatbot_position">
                        <option value="bottom-right" <?php selected(get_option('kafanek_chatbot_position', 'bottom-right'), 'bottom-right'); ?>>
                            Vpravo dole
                        </option>
                        <option value="bottom-left" <?php selected(get_option('kafanek_chatbot_position'), 'bottom-left'); ?>>
                            Vlevo dole
                        </option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th>AI Provider</th>
                <td>
                    <select name="kafanek_chatbot_provider">
                        <option value="openai" <?php selected(get_option('kafanek_chatbot_provider', 'claude'), 'openai'); ?>>
                            OpenAI (GPT-4)
                        </option>
                        <option value="claude" <?php selected(get_option('kafanek_chatbot_provider', 'claude'), 'claude'); ?>>
                            Claude (Sonnet 3.5)
                        </option>
                        <option value="gemini" <?php selected(get_option('kafanek_chatbot_provider', 'claude'), 'gemini'); ?>>
                            Gemini Pro
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Uložit nastavení'); ?>
    </form>
    
    <hr>
    
    <h2>📊 Statistiky konverzací</h2>
    <?php
    global $wpdb;
    $table = $wpdb->prefix . 'kafanek_chatbot_conversations';
    
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $today = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE DATE(created_at) = CURDATE()");
    $positive = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE sentiment = 'positive'");
    ?>
    
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
        <div class="stat-box" style="padding: 20px; background: #f0f9ff; border-radius: 8px;">
            <h3 style="margin: 0;"><?php echo number_format($total); ?></h3>
            <p style="margin: 5px 0 0;">Celkem konverzací</p>
        </div>
        <div class="stat-box" style="padding: 20px; background: #f0fdf4; border-radius: 8px;">
            <h3 style="margin: 0;"><?php echo number_format($today); ?></h3>
            <p style="margin: 5px 0 0;">Dnes</p>
        </div>
        <div class="stat-box" style="padding: 20px; background: #fef3c7; border-radius: 8px;">
            <h3 style="margin: 0;"><?php echo $total > 0 ? round(($positive / $total) * 100) : 0; ?>%</h3>
            <p style="margin: 5px 0 0;">Spokojenost</p>
        </div>
    </div>
    
    <h2 style="margin-top: 30px;">💬 Poslední konverzace</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Čas</th>
                <th>Uživatel</th>
                <th>Intent</th>
                <th>Sentiment</th>
                <th>Zpráva</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conversations = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 20");
            
            foreach ($conversations as $conv):
                $sentiment_emoji = [
                    'positive' => '😊',
                    'neutral' => '😐',
                    'negative' => '😞'
                ];
            ?>
            <tr>
                <td><?php echo esc_html(date('d.m. H:i', strtotime($conv->created_at))); ?></td>
                <td><?php echo esc_html($conv->session_id); ?></td>
                <td><?php echo esc_html($conv->intent); ?></td>
                <td><?php echo $sentiment_emoji[$conv->sentiment] ?? ''; ?> <?php echo esc_html($conv->sentiment); ?></td>
                <td><?php echo esc_html(wp_trim_words($conv->user_message, 10)); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
