/**
 * Kafánek AI Chatbot Widget
 * @version 1.2.0
 */

(function($) {
    'use strict';
    
    const KafanekChatbot = {
        sessionId: null,
        isOpen: false,
        context: {},
        
        init: function() {
            this.sessionId = this.generateSessionId();
            this.bindEvents();
            this.showWelcomeMessage();
        },
        
        generateSessionId: function() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        },
        
        bindEvents: function() {
            const self = this;
            
            $('#kafanek-chat-button').on('click', function() {
                self.toggleChat();
            });
            
            $('#kafanek-chat-close').on('click', function() {
                self.closeChat();
            });
            
            $('#kafanek-chat-send').on('click', function() {
                self.sendMessage();
            });
            
            $('#kafanek-chat-input').on('keypress', function(e) {
                if (e.which === 13) {
                    self.sendMessage();
                }
            });
            
            $(document).on('click', '.chat-quick-action', function() {
                const action = $(this).data('action');
                self.handleQuickAction(action);
            });
            
            $(document).on('click', '.chat-suggestion', function() {
                const text = $(this).text();
                $('#kafanek-chat-input').val(text);
                self.sendMessage();
            });
        },
        
        toggleChat: function() {
            if (this.isOpen) {
                this.closeChat();
            } else {
                this.openChat();
            }
        },
        
        openChat: function() {
            $('#kafanek-chat-window').fadeIn(300);
            $('#kafanek-chat-button .chat-badge').hide();
            this.isOpen = true;
            $('#kafanek-chat-input').focus();
        },
        
        closeChat: function() {
            $('#kafanek-chat-window').fadeOut(300);
            this.isOpen = false;
        },
        
        showWelcomeMessage: function() {
            setTimeout(() => {
                this.addBotMessage(kafanekChatbot.welcomeMessage);
                this.showQuickActions([
                    {label: '🛍️ Chci nakoupit', action: 'start_shopping'},
                    {label: '💬 Poradit si', action: 'get_advice'},
                    {label: '📞 Kontakt', action: 'show_contact'}
                ]);
            }, 1000);
        },
        
        sendMessage: function() {
            const message = $('#kafanek-chat-input').val().trim();
            
            if (!message) return;
            
            this.addUserMessage(message);
            $('#kafanek-chat-input').val('');
            this.showTypingIndicator();
            
            $.ajax({
                url: kafanekChatbot.ajaxurl,
                type: 'POST',
                data: {
                    action: 'kafanek_chatbot_message',
                    nonce: kafanekChatbot.nonce,
                    message: message,
                    session_id: this.sessionId,
                    context: JSON.stringify(this.context)
                },
                success: (response) => {
                    this.hideTypingIndicator();
                    
                    if (response.success) {
                        this.addBotMessage(response.data.response);
                        
                        if (response.data.quick_actions) {
                            this.showQuickActions(response.data.quick_actions);
                        }
                        
                        if (response.data.suggestions) {
                            this.showSuggestions(response.data.suggestions);
                        }
                    }
                },
                error: () => {
                    this.hideTypingIndicator();
                    this.addBotMessage('Omlouvám se, došlo k chybě. Zkuste to prosím znovu.');
                }
            });
        },
        
        addUserMessage: function(message) {
            const html = `
                <div class="chat-message user-message">
                    <div class="message-content">${this.escapeHtml(message)}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
            
            $('#kafanek-chat-messages').append(html);
            this.scrollToBottom();
        },
        
        addBotMessage: function(message) {
            const html = `
                <div class="chat-message bot-message">
                    <div class="message-content">${message}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
            
            $('#kafanek-chat-messages').append(html);
            this.scrollToBottom();
        },
        
        showTypingIndicator: function() {
            $('.chat-typing-indicator').show();
            this.scrollToBottom();
        },
        
        hideTypingIndicator: function() {
            $('.chat-typing-indicator').hide();
        },
        
        showQuickActions: function(actions) {
            let html = '';
            
            actions.forEach(action => {
                html += `<button class="chat-quick-action" data-action="${action.action}">${action.label}</button>`;
            });
            
            $('#kafanek-quick-actions').html(html).show();
        },
        
        showSuggestions: function(suggestions) {
            let html = '<div class="chat-suggestions">';
            
            suggestions.forEach(suggestion => {
                html += `<button class="chat-suggestion">${suggestion}</button>`;
            });
            
            html += '</div>';
            
            $('#kafanek-chat-messages').append(html);
            this.scrollToBottom();
        },
        
        handleQuickAction: function(action) {
            const actions = {
                'start_shopping': 'Chci nakoupit',
                'get_advice': 'Potřebuji poradit',
                'show_contact': 'Jak vás kontaktuji?',
                'view_all_products': 'Zobraz všechny produkty',
                'view_sale_products': 'Zobraz slevy',
                'track_order': 'Chci sledovat objednávku'
            };
            
            if (actions[action]) {
                $('#kafanek-chat-input').val(actions[action]);
                this.sendMessage();
            }
        },
        
        scrollToBottom: function() {
            const messages = document.getElementById('kafanek-chat-messages');
            messages.scrollTop = messages.scrollHeight;
        },
        
        getCurrentTime: function() {
            const now = new Date();
            return now.getHours().toString().padStart(2, '0') + ':' + 
                   now.getMinutes().toString().padStart(2, '0');
        },
        
        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    };
    
    $(document).ready(function() {
        KafanekChatbot.init();
    });
    
})(jQuery);
