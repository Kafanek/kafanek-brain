/**
 * Kafanek Brain - Frontend JavaScript
 * @version 1.2.0
 */

(function($) {
    'use strict';
    
    const KafanekBrain = {
        
        init: function() {
            console.log('🧠 Kafánek Brain v1.2.0 initialized');
            this.bindEvents();
        },
        
        bindEvents: function() {
            // AI Chat widget
            $(document).on('click', '.kafanek-chat-trigger', this.openChat);
            $(document).on('click', '.kafanek-chat-close', this.closeChat);
            $(document).on('submit', '.kafanek-chat-form', this.sendMessage);
            
            // Product recommendations
            this.initRecommendations();
        },
        
        openChat: function(e) {
            e.preventDefault();
            $('.kafanek-chat-widget').addClass('active');
        },
        
        closeChat: function(e) {
            e.preventDefault();
            $('.kafanek-chat-widget').removeClass('active');
        },
        
        sendMessage: function(e) {
            e.preventDefault();
            
            const form = $(this);
            const messageInput = form.find('input[name="message"]');
            const message = messageInput.val().trim();
            
            if (!message) return;
            
            // Add user message to chat
            KafanekBrain.addMessage(message, 'user');
            messageInput.val('');
            
            // Send to backend
            $.ajax({
                url: kafanek_ajax.url,
                type: 'POST',
                data: {
                    action: 'kafanek_ai_request',
                    ai_action: 'chat',
                    data: { message: message },
                    nonce: kafanek_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KafanekBrain.addMessage(response.message || 'Odpověď z AI', 'bot');
                    } else {
                        KafanekBrain.addMessage('Omlouváme se, nastala chyba.', 'bot');
                    }
                },
                error: function() {
                    KafanekBrain.addMessage('Chyba spojení.', 'bot');
                }
            });
        },
        
        addMessage: function(text, type) {
            const chatMessages = $('.kafanek-chat-messages');
            const messageHtml = `
                <div class="kafanek-message kafanek-message-${type}">
                    <div class="message-content">${text}</div>
                    <div class="message-time">${new Date().toLocaleTimeString()}</div>
                </div>
            `;
            
            chatMessages.append(messageHtml);
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        },
        
        initRecommendations: function() {
            $('.kafanek-ai-recommendations').each(function() {
                $(this).addClass('loaded');
            });
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        KafanekBrain.init();
    });
    
})(jQuery);
