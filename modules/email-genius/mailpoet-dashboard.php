<?php
/**
 * MailPoet AI Dashboard Template
 */
if (!defined('ABSPATH')) exit;
?>

<div class="wrap kafanek-mailpoet-dashboard">
    <h1>🤖 Kafánek AI pro MailPoet</h1>
    <p class="subtitle">φ-Enhanced Email Marketing</p>
    
    <div class="dashboard-grid">
        <!-- Subject Line Generator -->
        <div class="panel">
            <h2>🎯 AI Subject Line Generator</h2>
            
            <div class="form-group">
                <label>Téma newsletteru:</label>
                <textarea id="subject-context" rows="3" class="widefat" 
                          placeholder="Popište o čem bude newsletter..."></textarea>
            </div>
            
            <button id="generate-subjects" class="button button-primary">
                ✨ Generovat Subject Lines
            </button>
            
            <div id="subject-suggestions"></div>
        </div>
        
        <!-- Subject Line Tester -->
        <div class="panel">
            <h2>📊 Subject Line Analyzer</h2>
            
            <div class="form-group">
                <input type="text" id="test-subject" class="widefat" 
                       placeholder="Zadejte subject line...">
            </div>
            
            <button id="analyze-subject" class="button">Analyzovat</button>
            
            <div id="subject-analysis"></div>
        </div>
    </div>
</div>

<style>
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px; }
.panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.form-group { margin: 15px 0; }
.subject-suggestion { padding: 15px; margin: 10px 0; background: #f9fafb; border-left: 4px solid #667eea; }
.subject-suggestion .subject { font-weight: bold; font-size: 16px; }
.subject-suggestion .meta { color: #666; font-size: 14px; margin-top: 5px; }
.prediction { display: inline-block; padding: 3px 10px; background: #667eea; color: white; border-radius: 12px; font-size: 12px; margin-right: 10px; }
</style>

<script>
jQuery(function($) {
    $('#generate-subjects').on('click', function() {
        const context = $('#subject-context').val();
        if (!context) {
            alert('Zadejte téma newsletteru');
            return;
        }
        
        $(this).prop('disabled', true).text('⏳ Generuji...');
        
        $.post(kafanekMailPoet.ajaxurl, {
            action: 'kafanek_mailpoet_generate_subject',
            nonce: kafanekMailPoet.nonce,
            context: context
        }, function(response) {
            $('#generate-subjects').prop('disabled', false).text('✨ Generovat Subject Lines');
            
            if (response.success) {
                let html = '<h3>🎯 AI Návrhy:</h3>';
                
                response.data.forEach(function(item, index) {
                    html += '<div class="subject-suggestion">';
                    html += '<div class="subject">' + (index + 1) + '. ' + item.subject + '</div>';
                    html += '<div class="meta">';
                    html += '<span class="prediction">' + item.predicted_open_rate + '% open rate</span>';
                    html += 'Typ: ' + item.type;
                    if (item.emoji) html += ' | ' + item.emoji;
                    html += '</div>';
                    html += '<button class="button button-small copy-subject" data-subject="' + item.subject + '">Kopírovat</button>';
                    html += '</div>';
                });
                
                $('#subject-suggestions').html(html);
                
                $('.copy-subject').on('click', function() {
                    navigator.clipboard.writeText($(this).data('subject'));
                    $(this).text('✓ Zkopírováno');
                });
            }
        });
    });
    
    $('#analyze-subject').on('click', function() {
        const subject = $('#test-subject').val();
        if (!subject) return;
        
        $(this).prop('disabled', true).text('⏳ Analyzuji...');
        
        $.post(kafanekMailPoet.ajaxurl, {
            action: 'kafanek_mailpoet_test_subject',
            nonce: kafanekMailPoet.nonce,
            subject: subject
        }, function(response) {
            $('#analyze-subject').prop('disabled', false).text('Analyzovat');
            
            if (response.success) {
                let html = '<div class="analysis-results">';
                html += '<h3>📊 Analýza:</h3>';
                html += '<p><strong>Open Rate:</strong> ' + response.data.predicted_open_rate + '%</p>';
                html += '<p><strong>Skóre:</strong> ' + response.data.score + '/100</p>';
                html += '<p><strong>Délka:</strong> ' + response.data.length + ' znaků</p>';
                
                if (response.data.suggestions.length > 0) {
                    html += '<h4>💡 Doporučení:</h4><ul>';
                    response.data.suggestions.forEach(function(s) {
                        html += '<li>' + s + '</li>';
                    });
                    html += '</ul>';
                }
                
                html += '</div>';
                $('#subject-analysis').html(html);
            }
        });
    });
});
</script>
<?php
