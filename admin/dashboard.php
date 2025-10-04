<?php
/**
 * Kafanek Brain Dashboard
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

// Get AI usage stats
global $wpdb;
$table_name = $wpdb->prefix . 'kafanek_ai_logs';

$total_requests = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
$total_tokens = $wpdb->get_var("SELECT SUM(tokens_used) FROM $table_name");
$recent_requests = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10");

?>

<div class="wrap">
    <h1>🧠 Kafánkův Mozek - Dashboard</h1>
    
    <div class="kafanek-dashboard">
        <div class="kafanek-stats">
            <div class="stat-box">
                <h3>📊 Celkem požadavků</h3>
                <p class="stat-number"><?php echo number_format($total_requests); ?></p>
            </div>
            
            <div class="stat-box">
                <h3>🎯 Použité tokeny</h3>
                <p class="stat-number"><?php echo number_format($total_tokens); ?></p>
            </div>
            
            <div class="stat-box">
                <h3>✨ Golden Ratio</h3>
                <p class="stat-number"><?php echo KAFANEK_BRAIN_PHI; ?></p>
            </div>
            
            <div class="stat-box">
                <h3>🚀 Verze</h3>
                <p class="stat-number"><?php echo KAFANEK_BRAIN_VERSION; ?></p>
            </div>
        </div>
        
        <h2>📝 Poslední AI požadavky</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Čas</th>
                    <th>Typ</th>
                    <th>Tokeny</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_requests): ?>
                    <?php foreach ($recent_requests as $request): ?>
                        <tr>
                            <td><?php echo esc_html($request->created_at); ?></td>
                            <td><?php echo esc_html($request->request_type); ?></td>
                            <td><?php echo esc_html($request->tokens_used); ?></td>
                            <td>
                                <button class="button button-small view-details" data-id="<?php echo $request->id; ?>">
                                    Zobrazit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Zatím žádné požadavky</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <h2>🎨 Quick Actions</h2>
        <div class="kafanek-actions">
            <a href="<?php echo admin_url('admin.php?page=kafanek-brain-settings'); ?>" class="button button-primary">
                ⚙️ Nastavení
            </a>
            
            <a href="<?php echo rest_url('kafanek-brain/v1/status'); ?>" class="button" target="_blank">
                📊 API Status
            </a>
            
            <?php if (class_exists('WooCommerce')): ?>
                <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button">
                    🛒 WooCommerce Produkty
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.kafanek-dashboard {
    max-width: 1200px;
}

.kafanek-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-box {
    background: white;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}

.stat-box h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #646970;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2271b1;
    margin: 0;
}

.kafanek-actions {
    margin: 20px 0;
}

.kafanek-actions .button {
    margin-right: 10px;
}

.widefat {
    margin-top: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.view-details').on('click', function() {
        var id = $(this).data('id');
        alert('Detail zobrazení bude přidáno v další verzi. Request ID: ' + id);
    });
});
</script>
