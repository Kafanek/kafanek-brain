/**
 * Kafanek Brain Admin JavaScript
 * @version 1.2.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        /**
         * Test API Connection
         */
        $('#test-api').on('click', function() {
            var button = $(this);
            var originalText = button.text();
            
            button.prop('disabled', true).text('Testing...');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'kafanek_test_api',
                    nonce: kafanekAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ API Connection Successful!\n\nProvider: ' + response.data.provider + '\nModel: ' + response.data.model);
                    } else {
                        alert('❌ API Connection Failed\n\n' + response.data);
                    }
                },
                error: function() {
                    alert('❌ Connection Error');
                },
                complete: function() {
                    button.prop('disabled', false).text(originalText);
                }
            });
        });
        
        /**
         * Clear Cache
         */
        $('#clear-cache').on('click', function() {
            var button = $(this);
            
            if (!confirm('Are you sure you want to clear all AI cache?')) {
                return;
            }
            
            button.prop('disabled', true).text('Clearing...');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'kafanek_clear_cache',
                    nonce: kafanekAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Cache cleared successfully!');
                    } else {
                        alert('❌ Failed to clear cache');
                    }
                },
                complete: function() {
                    button.prop('disabled', false).text('Clear Cache');
                }
            });
        });
        
        /**
         * Module Toggle
         */
        $('.kafanek-module-toggle').on('change', function() {
            var checkbox = $(this);
            var module = checkbox.data('module');
            var enabled = checkbox.is(':checked');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'kafanek_toggle_module',
                    module: module,
                    enabled: enabled ? 1 : 0,
                    nonce: kafanekAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var message = enabled ? 'Module enabled' : 'Module disabled';
                        console.log('✅ ' + message + ': ' + module);
                    }
                }
            });
        });
        
        /**
         * Provider Selection Change Handler
         */
        $('#ai_provider').on('change', function() {
            var provider = $(this).val();
            
            // Hide all provider settings
            $('.openai-settings, .claude-settings, .gemini-settings').hide();
            
            // Show selected provider settings
            if (provider === 'claude') {
                $('.claude-settings').show();
            } else if (provider === 'gemini') {
                $('.gemini-settings').show();
            } else {
                $('.openai-settings').show();
            }
        });
        
        // Trigger on page load
        $('#ai_provider').trigger('change');
    });
    
})(jQuery);
