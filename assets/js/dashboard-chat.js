/**
 * Kafanek Brain Enhanced Dashboard - Chat Interface
 * @version 1.2.0
 * Golden Ratio (φ = 1.618) Enhanced
 */

class KafanekChatInterface {
    constructor() {
        this.chatContainer = document.getElementById('chat-messages');
        this.inputField = document.getElementById('chat-input');
        this.sendButton = document.getElementById('send-message');
        this.smartSearch = document.getElementById('smart-search');
        this.conversationHistory = [];
        this.isTyping = false;
        this.phi = kafanek_ajax.phi || 1.618;
        
        this.init();
    }
    
    init() {
        if (!this.chatContainer || !this.inputField || !this.sendButton) {
            console.error('Chat elements not found');
            return;
        }
        
        // Send button click
        this.sendButton.addEventListener('click', () => this.sendMessage());
        
        // Enter to send (Shift+Enter for newline)
        this.inputField.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Tool buttons
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleTool(e.target.dataset.tool));
        });
        
        // Action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleAction(e.target.dataset.action));
        });
        
        // Smart search
        if (this.smartSearch) {
            let searchTimeout;
            this.smartSearch.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => this.performSmartSearch(e.target.value), 500);
            });
        }
        
        // Welcome message
        this.addMessage('assistant', `Ahoj! 👋 Jsem váš AI asistent pro WordPress. 
        
Používám Golden Ratio (φ = ${this.phi}) pro optimalizaci. Jak vám mohu pomoci?

**Můžu:**
- 🔍 Hledat v obsahu
- ✨ Generovat popisy produktů
- 📊 Analyzovat výkon
- 💰 Optimalizovat ceny (φ-based)
- 🧬 Spravovat Neural Network`);
    }
    
    async sendMessage() {
        const message = this.inputField.value.trim();
        if (!message || this.isTyping) return;
        
        this.addMessage('user', message);
        this.inputField.value = '';
        this.setTypingIndicator(true);
        
        try {
            const response = await this.callAI(message);
            this.handleResponse(response);
        } catch (error) {
            console.error('Chat error:', error);
            this.addMessage('error', 'Omlouvám se, nastala chyba při komunikaci. Zkuste to prosím znovu.');
        } finally {
            this.setTypingIndicator(false);
        }
    }
    
    async callAI(message) {
        const formData = new FormData();
        formData.append('action', 'kafanek_chat_message');
        formData.append('message', message);
        formData.append('nonce', kafanek_ajax.nonce);
        formData.append('context', JSON.stringify(this.getContext()));
        
        const response = await fetch(kafanek_ajax.url, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        return await response.json();
    }
    
    handleResponse(response) {
        if (response.success) {
            this.addMessage('assistant', response.data.message, response.data.actions || []);
            
            // Add suggestions if present
            if (response.data.suggestions && response.data.suggestions.length > 0) {
                this.addSuggestions(response.data.suggestions);
            }
        } else {
            this.addMessage('error', response.data || 'Chyba při zpracování odpovědi');
        }
    }
    
    getContext() {
        return {
            page: window.location.pathname,
            history: this.conversationHistory.slice(-5),
            capabilities: {
                can_edit_posts: kafanek_ajax.can_edit_posts,
                can_manage_woocommerce: kafanek_ajax.can_manage_woocommerce
            },
            phi: this.phi
        };
    }
    
    handleTool(tool) {
        const prompts = {
            'search': 'Pomozte mi najít ',
            'generate': 'Vygenerujte ',
            'analyze': 'Analyzujte ',
            'optimize': `Optimalizujte pomocí φ (${this.phi}) `
        };
        
        this.inputField.value = prompts[tool] || '';
        this.inputField.focus();
    }
    
    async handleAction(action) {
        switch(action) {
            case 'generate-product':
                window.location.href = '/wp-admin/post-new.php?post_type=product';
                break;
            case 'optimize-prices':
                this.sendMessage = () => this.callAI('Optimalizuj všechny ceny produktů pomocí Golden Ratio');
                await this.sendMessage();
                break;
            case 'create-post':
                window.location.href = '/wp-admin/post-new.php';
                break;
            case 'analyze-performance':
                await this.quickAction('analyze-performance');
                break;
            case 'neural-status':
                await this.quickAction('neural-status');
                break;
        }
    }
    
    async quickAction(actionType) {
        this.setTypingIndicator(true);
        
        try {
            const formData = new FormData();
            formData.append('action', 'kafanek_quick_action');
            formData.append('action_type', actionType);
            formData.append('nonce', kafanek_ajax.nonce);
            
            const response = await fetch(kafanek_ajax.url, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.addMessage('assistant', this.formatActionResponse(actionType, data.data));
            } else {
                this.addMessage('error', data.data || 'Akce selhala');
            }
        } catch (error) {
            console.error('Quick action error:', error);
            this.addMessage('error', 'Chyba při provádění akce');
        } finally {
            this.setTypingIndicator(false);
        }
    }
    
    formatActionResponse(actionType, data) {
        switch(actionType) {
            case 'neural-status':
                if (data.success) {
                    return `**🧬 Neural Network Status:**

- Status: ${data.neural_network.status}
- Architecture: ${data.neural_network.architecture}
- Parameters: ${data.neural_network.parameters.toLocaleString()}
- Size: ${data.neural_network.size_kb} KB
- φ (Phi): ${data.neural_network.phi}

Capabilities: ${data.capabilities ? Object.keys(data.capabilities).join(', ') : 'N/A'}`;
                }
                return data.message || 'Neural Network není dostupná';
                
            case 'analyze-performance':
                return `**📊 Performance Analysis:**

- Total AI Requests: ${data.total_requests}
- Today's Requests: ${data.today_requests}
- Cache Hit Rate: ${(data.cache_hit_rate * 100).toFixed(1)}%
- Golden Ratio (φ): ${data.phi_ratio}

Cache efficiency optimized with Fibonacci timing (21 minutes).`;
                
            default:
                return JSON.stringify(data, null, 2);
        }
    }
    
    addMessage(type, content, actions = []) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.innerHTML = type === 'user' ? '👤' : (type === 'error' ? '⚠️' : '🤖');
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = this.formatMessage(content);
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        
        // Add action buttons if provided
        if (actions.length > 0) {
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'message-actions';
            actions.forEach(action => {
                const btn = document.createElement('button');
                btn.className = 'button button-small';
                btn.textContent = action.label;
                btn.onclick = () => this.executeAction(action);
                actionsDiv.appendChild(btn);
            });
            messageDiv.appendChild(actionsDiv);
        }
        
        this.chatContainer.appendChild(messageDiv);
        this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
        
        // Save to history
        this.conversationHistory.push({ type, content, timestamp: Date.now() });
        
        // Limit history (Fibonacci number: 89)
        if (this.conversationHistory.length > 89) {
            this.conversationHistory = this.conversationHistory.slice(-89);
        }
    }
    
    addSuggestions(suggestions) {
        const suggestionsDiv = document.createElement('div');
        suggestionsDiv.className = 'chat-suggestions';
        
        const title = document.createElement('div');
        title.className = 'suggestions-title';
        title.textContent = '💡 Suggestions:';
        suggestionsDiv.appendChild(title);
        
        suggestions.forEach(suggestion => {
            const btn = document.createElement('button');
            btn.className = 'suggestion-btn';
            btn.textContent = suggestion;
            btn.onclick = () => {
                this.inputField.value = suggestion;
                this.sendMessage();
            };
            suggestionsDiv.appendChild(btn);
        });
        
        this.chatContainer.appendChild(suggestionsDiv);
        this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
    }
    
    formatMessage(content) {
        if (typeof marked !== 'undefined') {
            return marked.parse(content);
        }
        
        // Fallback: simple formatting
        return content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
    
    setTypingIndicator(show) {
        this.isTyping = show;
        this.sendButton.disabled = show;
        this.inputField.disabled = show;
        
        // Remove existing indicator
        const existing = document.querySelector('.typing-indicator');
        if (existing) {
            existing.remove();
        }
        
        if (show) {
            const indicator = document.createElement('div');
            indicator.className = 'typing-indicator';
            indicator.innerHTML = '<span></span><span></span><span></span>';
            this.chatContainer.appendChild(indicator);
            this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
        }
    }
    
    async performSmartSearch(query) {
        if (!query || query.length < 3) {
            document.getElementById('search-results').innerHTML = '';
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'kafanek_search_content');
            formData.append('query', query);
            formData.append('nonce', kafanek_ajax.nonce);
            
            const response = await fetch(kafanek_ajax.url, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displaySearchResults(data.data);
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    displaySearchResults(results) {
        const container = document.getElementById('search-results');
        container.innerHTML = '';
        
        if (results.posts.length === 0 && results.products.length === 0) {
            container.innerHTML = '<div class="no-results">No results found</div>';
            return;
        }
        
        // Display posts
        if (results.posts.length > 0) {
            const postsDiv = document.createElement('div');
            postsDiv.className = 'search-section';
            postsDiv.innerHTML = '<h4>📝 Posts</h4>';
            
            results.posts.forEach(post => {
                const item = document.createElement('a');
                item.className = 'search-item';
                item.href = post.url;
                item.innerHTML = `
                    <div class="search-item-title">${post.title}</div>
                    <div class="search-item-excerpt">${post.excerpt}</div>
                `;
                postsDiv.appendChild(item);
            });
            
            container.appendChild(postsDiv);
        }
        
        // Display products
        if (results.products.length > 0) {
            const productsDiv = document.createElement('div');
            productsDiv.className = 'search-section';
            productsDiv.innerHTML = '<h4>📦 Products</h4>';
            
            results.products.forEach(product => {
                const item = document.createElement('a');
                item.className = 'search-item';
                item.href = product.url;
                item.innerHTML = `
                    <div class="search-item-title">${product.title}</div>
                    <div class="search-item-price">${product.price} Kč</div>
                `;
                productsDiv.appendChild(item);
            });
            
            container.appendChild(productsDiv);
        }
    }
    
    async executeAction(action) {
        switch(action.type) {
            case 'create_product':
                window.location.href = action.url;
                break;
            case 'optimize_prices':
                await this.quickAction('optimize-prices');
                break;
            default:
                console.log('Unknown action:', action);
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('kafanek-dashboard')) {
        new KafanekChatInterface();
    }
});
