/**
 * Main JavaScript file for SNN AI API System
 * 
 * @package SNN_AI_API_System
 * @since 1.0.0
 */

(function(window, document, wp) {
    'use strict';
    
    // Initialize the SNN AI object
    window.snnAI = {
        
        init: function() {
            this.setupEventListeners();
        },
        
        setupEventListeners: function() {
            // Add event listeners for API interactions
            document.addEventListener('DOMContentLoaded', this.onDOMReady.bind(this));
        },
        
        onDOMReady: function() {
            // Initialize components when DOM is ready
            this.initChatInterface();
            this.initTemplateBuilder();
        },
        
        initChatInterface: function() {
            const chatContainer = document.getElementById('snn-ai-chat-container');
            if (!chatContainer) return;
            
            // Initialize chat interface
            this.chat = new this.ChatInterface(chatContainer);
        },
        
        initTemplateBuilder: function() {
            const templateBuilder = document.getElementById('snn-ai-template-builder');
            if (!templateBuilder) return;
            
            // Initialize template builder
            this.templateBuilder = new this.TemplateBuilder(templateBuilder);
        },
        
        // API Methods
        chat: function(args) {
            return this.apiRequest('chat', args);
        },
        
        complete: function(args) {
            return this.apiRequest('complete', args);
        },
        
        embed: function(text, args = {}) {
            return this.apiRequest('embed', { text: text, ...args });
        },
        
        generateImage: function(args) {
            return this.apiRequest('generate-image', args);
        },
        
        apiRequest: function(endpoint, data) {
            return wp.apiFetch({
                path: `snn-ai/v1/${endpoint}`,
                method: 'POST',
                data: data
            });
        },
        
        // Utility methods
        showNotice: function(message, type = 'success') {
            const notice = document.createElement('div');
            notice.className = `notice notice-${type} is-dismissible`;
            notice.innerHTML = `<p>${message}</p>`;
            
            const container = document.querySelector('.wrap') || document.body;
            container.insertBefore(notice, container.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notice.remove();
            }, 5000);
        },
        
        showError: function(message) {
            this.showNotice(message, 'error');
        },
        
        showLoading: function(element) {
            element.classList.add('snn-ai-loading');
        },
        
        hideLoading: function(element) {
            element.classList.remove('snn-ai-loading');
        }
    };
    
    // Chat Interface Class
    snnAI.ChatInterface = function(container) {
        this.container = container;
        this.messages = [];
        this.currentProvider = null;
        this.currentModel = null;
        this.init();
    };
    
    snnAI.ChatInterface.prototype = {
        init: function() {
            this.setupUI();
            this.bindEvents();
        },
        
        setupUI: function() {
            this.container.innerHTML = `
                <div class="snn-ai-chat-header">
                    <select id="snn-ai-provider-select">
                        <option value="">Select Provider</option>
                    </select>
                    <select id="snn-ai-model-select">
                        <option value="">Select Model</option>
                    </select>
                </div>
                <div class="snn-ai-chat-messages" id="snn-ai-chat-messages"></div>
                <div class="snn-ai-chat-input">
                    <textarea id="snn-ai-message-input" placeholder="Type your message..."></textarea>
                    <button id="snn-ai-send-button">Send</button>
                </div>
            `;
            
            this.loadProviders();
        },
        
        bindEvents: function() {
            const sendButton = document.getElementById('snn-ai-send-button');
            const messageInput = document.getElementById('snn-ai-message-input');
            const providerSelect = document.getElementById('snn-ai-provider-select');
            
            sendButton.addEventListener('click', this.sendMessage.bind(this));
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
            
            providerSelect.addEventListener('change', this.onProviderChange.bind(this));
        },
        
        loadProviders: function() {
            wp.apiFetch({
                path: 'snn-ai/v1/providers'
            }).then(providers => {
                const select = document.getElementById('snn-ai-provider-select');
                
                Object.keys(providers).forEach(name => {
                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = providers[name].display_name;
                    select.appendChild(option);
                });
            }).catch(error => {
                snnAI.showError('Failed to load providers: ' + error.message);
            });
        },
        
        onProviderChange: function(e) {
            this.currentProvider = e.target.value;
            this.loadModels();
        },
        
        loadModels: function() {
            if (!this.currentProvider) return;
            
            wp.apiFetch({
                path: `snn-ai/v1/providers/${this.currentProvider}/models`
            }).then(models => {
                const select = document.getElementById('snn-ai-model-select');
                select.innerHTML = '<option value="">Select Model</option>';
                
                Object.keys(models).forEach(modelId => {
                    const option = document.createElement('option');
                    option.value = modelId;
                    option.textContent = models[modelId].name;
                    select.appendChild(option);
                });
            }).catch(error => {
                snnAI.showError('Failed to load models: ' + error.message);
            });
        },
        
        sendMessage: function() {
            const messageInput = document.getElementById('snn-ai-message-input');
            const message = messageInput.value.trim();
            
            if (!message) return;
            if (!this.currentProvider) {
                snnAI.showError('Please select a provider');
                return;
            }
            
            this.addMessage('user', message);
            messageInput.value = '';
            
            const messages = this.messages.map(msg => ({
                role: msg.role,
                content: msg.content
            }));
            
            const sendButton = document.getElementById('snn-ai-send-button');
            snnAI.showLoading(sendButton);
            
            snnAI.chat({
                provider: this.currentProvider,
                model: document.getElementById('snn-ai-model-select').value,
                messages: messages
            }).then(response => {
                this.addMessage('assistant', response.content);
            }).catch(error => {
                snnAI.showError('Chat request failed: ' + error.message);
            }).finally(() => {
                snnAI.hideLoading(sendButton);
            });
        },
        
        addMessage: function(role, content) {
            this.messages.push({ role, content });
            
            const messagesContainer = document.getElementById('snn-ai-chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = `snn-ai-message snn-ai-message-${role}`;
            messageElement.innerHTML = `
                <div class="snn-ai-message-role">${role}</div>
                <div class="snn-ai-message-content">${this.formatMessage(content)}</div>
            `;
            
            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        },
        
        formatMessage: function(content) {
            return content.replace(/\n/g, '<br>');
        }
    };
    
    // Template Builder Class
    snnAI.TemplateBuilder = function(container) {
        this.container = container;
        this.init();
    };
    
    snnAI.TemplateBuilder.prototype = {
        init: function() {
            this.setupUI();
            this.bindEvents();
        },
        
        setupUI: function() {
            // Template builder UI setup
            console.log('Template builder initialized');
        },
        
        bindEvents: function() {
            // Template builder event bindings
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => snnAI.init());
    } else {
        snnAI.init();
    }
    
})(window, document, wp);