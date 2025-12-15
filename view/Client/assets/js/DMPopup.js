/**
 * DMPopup - Complete Modular Chat System
 * Self-contained, drop-in replacement for chat.js
 *
 * Usage:
 * const chat = new DMPopup({
 *     wsUrl: 'ws://localhost:9090',
 *     userId: 123,
 *     username: 'JohnDoe',
 *     apiBaseUrl: 'index.php?c=chatC&a='
 * });
 * chat.init(conversations);
 */

class DMPopup {
    constructor(config = {}) {
        // Configuration with defaults from DMPopupConfig if available
        const globalConfig = typeof DMPopupConfig !== 'undefined' ? DMPopupConfig : {};

        this.config = {
            wsUrl: config.wsUrl || globalConfig.WEBSOCKET?.URL || `ws://${window.location.hostname}:9090`,
            userId: config.userId || 0,
            username: config.username || 'Guest',
            apiBaseUrl: config.apiBaseUrl || globalConfig.API?.BASE_URL || 'index.php?c=chatC&a=',
            container: config.container || document.body
        };

        // State
        this.state = {
            ws: null,
            wsReconnectAttempts: 0,
            wsReconnectTimeout: null,
            currentConversationId: null,
            isEditMode: false,
            editingMessageId: null,
            replyingToMessageId: null,
            replyingToContent: null,
            messagesCache: new Map(),
            activeTypingUsers: new Map(),
            typingTimeout: null,
            currentEmojiPicker: null,
            lastClickTime: 0,
            lastClickedMessageId: null,
            selectedUsers: new Set(),
            currentDeleteMessageId: null,
            isCurrentlyTyping: false
        };

        // UI Constants
        this.EMOJI_CATEGORIES = globalConfig.UI?.EMOJI_CATEGORIES || {
            ':D': ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🤧','🥵','🥶','🥴','😵','🤯','🤠','🥳','🥸','😎','🤓','🧐'],
            ':<': ['😕','😟','🙁','☹️','😮','😯','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
            ':3': ['💋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏'],
            '<3': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','♥️'],
            '☘︎': ['✨','⭐','🌟','💫','✔️','❌','❕','❓','⚠️','🔥','💯','💢','💥','💨','💦','💤','🎉','🎊','🎈','🎁','🏆','🥇','🥈','🥉','⚽','🏀','🏈','⚾','🎾','🏐','🏉','🎱','🏓','🏸','🥅','🎯'],
            '꩜': ['💎','👑','🎓','🎭','🎨','🎬','🎤','🎧','🎼','🎹','🥁','🎷','🎺','🎸','🪕','🎻','📱','💻','⌨️','🖥️','🖨️','🖱️','💿','📀','📷','📹','🎥','📞','☎️','📟','📠','📺','📻','🔔','⏰','⏱️','⏲️','⏳','📡'],
            '✿': ['🌍','🌎','🌏','🌐','🗺️','🏔️','⛰️','🌋','🗻','🏕️','🏖️','🏜️','🏝️','🏞️','☀️','🌤️','⛅','🌥️','☁️','🌦️','🌧️','⛈️','🌩️','🌨️','❄️','☃️','⛄','🌬️','💨','🌪️','🌫️','🌈','☔','💧','💦','⚡','🔥','✨','🌟','💫','⭐','🌙','☄️'],
            '☕︎': ['🍎','🍊','🍋','🍌','🍉','🍇','🍓','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶️','🌽','🥕','🧄','🧅','🥔','🍠','🥐','🥯','🍞','🥖','🥨','🧀','🥚','🍳','🧈','🥞','🧇','🥓','🥩','🍗','🍖','🦴','🌭','🍔','🍟','🍕','🥪','🥙','🧆','🌮','🌯','🥗','🥘','🥫','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🦪','🍤','🍙','🍚','🍘','🍥','🍢','🍡','🍧','🍨','🍦','🥧','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍿','🍩','🍪','🌰','🥜','🍯']
        };

        this.QUICK_REACTIONS = globalConfig.UI?.QUICK_REACTIONS || ['❤️','👍','😂','💀','😢','🔥','🎉'];

        // Elements cache
        this.elements = {};
    }

    /**
     * Initialize the chat system
     */
    init(conversations = []) {
        this.createHTML();
        this.cacheElements();
        this.setupGalaxyAnimation();
        this.attachEventListeners();
        this.connectWebSocket();

        if (conversations.length > 0) {
            this.loadConversations(conversations);
        }

        // Scroll messages to bottom on init
        const msgBox = this.elements.messages;
        if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;
    }

    /**
     * Create the complete HTML structure
     */
    createHTML() {
        const html = `
            <canvas id="galaxyCanvas"></canvas>
            <div class="bg-animation"></div>
            
            <div id="dm-btn" class="dm-btn">
                <i class="fas fa-comments"></i>
            </div>
            
            <div id="dm-popup" class="dm-popup">
                <div class="chat-header">
                    <div class="chat-header-left">
                        <button id="backBtn" style="display:none;">←</button>
                        <div id="popup-avatar" style="display: none;"></div>
                        <div class="chat-info">
                            <h3 id="chatTitle">Messages</h3>
                            <p id="chatSubtitle"></p>
                        </div>
                    </div>
                    <div class="header-btns">
                        <button id="closeBtn">✕</button>
                    </div>
                </div>
                
                <div class="popup-body">
                    <div class="conv-panel show" id="convPanel">
                        <div class="search">
                            <input type="text" id="searchConvInput" placeholder="Search conversations..." autocomplete="off">
                            <button id="newConvBtn" class="new-conv-btn" title="New Conversation">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="conv-list" id="convList"></div>
                    </div>
                    
                    <div class="conv-panel" id="newConvPanel">
                        <div class="new-conv-header">
                            <button id="backToConvBtn">←</button>
                            <h3>New Conversation</h3>
                        </div>
                        <div class="user-search-box">
                            <input type="text" id="userSearchInput" placeholder="Search users..." autocomplete="off">
                        </div>
                        <div id="searchResults" class="search-results">
                            <p style="padding:15px;opacity:0.6;text-align:center;">Start typing to search users...</p>
                        </div>
                        <div id="selectedUsers" class="selected-users" style="display:none;">
                            <h4>Selected Users</h4>
                            <div id="selectedUsersList" class="selected-users-list"></div>
                        </div>
                        <div class="create-conv-footer">
                            <div class="conv-options">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="isGroupCheck">
                                    <span>Group Chat</span>
                                </label>
                                <input type="text" id="convTitleInput" placeholder="Conversation title (optional)">
                            </div>
                            <button id="createConvBtn" class="btn-create-conv" disabled>Create Conversation</button>
                        </div>
                    </div>
                    
                    <div class="chat-panel" id="chatPanel">
                        <div id="messages"></div>
                        
                        <div id="typingIndicator" class="typing-indicator">
                            <div class="typing-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <span id="typingText"></span>
                        </div>
                        
                        <div id="replyBanner" class="reply-banner"></div>
                        
                        <div id="editBanner" class="edit-banner" style="display:none;">
                            <div class="edit-banner-content">
                                <i class="fas fa-edit"></i>
                                <span>Editing message</span>
                            </div>
                            <button id="cancelEditBtn" class="cancel-edit-btn">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                        
                        <form class="input-bar" id="messageForm">
                            <textarea name="content" id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                            <button type="submit" id="sendBtn">➤</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete Message</h3>
                        <button class="modal-close" id="closeDeleteModal">✕</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this message? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary" id="cancelDelete">Cancel</button>
                        <button class="btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        `;

        this.config.container.insertAdjacentHTML('beforeend', html);
    }

    /**
     * Cache DOM elements
     */
    cacheElements() {
        this.elements = {
            dmBtn: document.getElementById('dm-btn'),
            popup: document.getElementById('dm-popup'),
            closeBtn: document.getElementById('closeBtn'),
            backBtn: document.getElementById('backBtn'),
            convPanel: document.getElementById('convPanel'),
            chatPanel: document.getElementById('chatPanel'),
            newConvPanel: document.getElementById('newConvPanel'),
            messages: document.getElementById('messages'),
            messageForm: document.getElementById('messageForm'),
            messageInput: document.getElementById('messageInput'),
            sendBtn: document.getElementById('sendBtn'),
            chatTitle: document.getElementById('chatTitle'),
            chatSubtitle: document.getElementById('chatSubtitle'),
            popupAvatar: document.getElementById('popup-avatar'),
            searchConvInput: document.getElementById('searchConvInput'),
            convList: document.getElementById('convList'),
            newConvBtn: document.getElementById('newConvBtn'),
            backToConvBtn: document.getElementById('backToConvBtn'),
            userSearchInput: document.getElementById('userSearchInput'),
            searchResults: document.getElementById('searchResults'),
            selectedUsers: document.getElementById('selectedUsers'),
            selectedUsersList: document.getElementById('selectedUsersList'),
            createConvBtn: document.getElementById('createConvBtn'),
            isGroupCheck: document.getElementById('isGroupCheck'),
            convTitleInput: document.getElementById('convTitleInput'),
            typingIndicator: document.getElementById('typingIndicator'),
            typingText: document.getElementById('typingText'),
            editBanner: document.getElementById('editBanner'),
            replyBanner: document.getElementById('replyBanner'),
            cancelEditBtn: document.getElementById('cancelEditBtn'),
            deleteModal: document.getElementById('deleteModal'),
            closeDeleteModal: document.getElementById('closeDeleteModal'),
            cancelDelete: document.getElementById('cancelDelete'),
            confirmDelete: document.getElementById('confirmDelete'),
            galaxyCanvas: document.getElementById('galaxyCanvas')
        };
    }

    /**
     * Setup galaxy animation
     */
    setupGalaxyAnimation() {
        const canvas = this.elements.galaxyCanvas;
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let w, h;

        const resize = () => {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        };
        resize();
        window.addEventListener('resize', resize);

        const stars = [];
        for (let i = 0; i < 600; i++) {
            stars.push({
                x: Math.random() * w,
                y: Math.random() * h,
                radius: Math.random() * 1.8 + 0.5,
                alpha: Math.random() * 0.8 + 0.2,
                speed: Math.random() * 0.7 + 0.2
            });
        }

        const drawStars = () => {
            ctx.clearRect(0, 0, w, h);
            const isDark = document.documentElement.classList.contains('dark');

            stars.forEach(s => {
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${isDark ? s.alpha : s.alpha * 0.4})`;
                ctx.fill();

                s.alpha += (Math.random() - 0.5) * 0.05;
                s.alpha = Math.max(0.2, Math.min(1, s.alpha));

                s.x += s.speed;
                if (s.x > w) s.x = 0;
            });

            requestAnimationFrame(drawStars);
        };
        drawStars();
    }

    /**
     * Attach all event listeners
     */
    attachEventListeners() {
        // Popup controls
        this.elements.dmBtn.addEventListener('click', () => this.openPopup());
        this.elements.closeBtn.addEventListener('click', () => this.closePopup());
        this.elements.backBtn.addEventListener('click', () => this.goBackToList());

        // New conversation
        this.elements.newConvBtn.addEventListener('click', () => this.showNewConvPanel());
        this.elements.backToConvBtn.addEventListener('click', () => this.hideNewConvPanel());

        // User search with debounce
        let searchTimeout;
        this.elements.userSearchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => this.searchUsers(query), 300);
            } else {
                this.elements.searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">Start typing to search users...</p>';
            }
        });

        // Create conversation
        this.elements.createConvBtn.addEventListener('click', () => this.createConversation());

        // Conversation search
        this.elements.searchConvInput.addEventListener('input', (e) => this.filterConversations(e.target.value));

        // Message input
        this.elements.messageInput.addEventListener('input', () => this.handleMessageInput());
        this.elements.messageInput.addEventListener('keydown', (e) => this.handleKeyDown(e));
        this.elements.messageInput.addEventListener('blur', () => this.handleInputBlur());

        // Message form
        this.elements.messageForm.addEventListener('submit', (e) => this.handleMessageSubmit(e));

        // Edit controls
        this.elements.cancelEditBtn.addEventListener('click', () => this.exitEditMode());

        // Delete modal
        this.elements.closeDeleteModal.addEventListener('click', () => this.closeDeleteModal());
        this.elements.cancelDelete.addEventListener('click', () => this.closeDeleteModal());
        this.elements.confirmDelete.addEventListener('click', () => this.confirmDelete());
    }

// ========== WEBSOCKET ========== (Continue in Part 2)
// ========== CONTINUATION OF DMPopup CLASS ==========
// Add these methods to the DMPopup class from Part 1

    // ========== WEBSOCKET ==========

    connectWebSocket() {
        try {
            this.state.ws = new WebSocket(this.config.wsUrl);

            this.state.ws.onopen = () => {
                console.log('[WS] ✅ Connected');
                this.state.wsReconnectAttempts = 0;
                if (this.state.currentConversationId) {
                    this.joinConversation(this.state.currentConversationId);
                }
            };

            this.state.ws.onmessage = (event) => {
                try {
                    this.handleWebSocketMessage(JSON.parse(event.data));
                } catch (e) {
                    console.error('[WS] Parse error:', e);
                }
            };

            this.state.ws.onclose = () => {
                console.log('[WS] ⚠️ Disconnected');
                this.attemptReconnect();
            };

            this.state.ws.onerror = (error) => {
                console.error('[WS] ❌ Error:', error);
            };
        } catch (error) {
            console.error('[WS] Connection failed:', error);
            this.attemptReconnect();
        }
    }

    attemptReconnect() {
        if (this.state.wsReconnectAttempts >= 10) {
            console.log('[WS] Max reconnection attempts reached');
            return;
        }

        this.state.wsReconnectAttempts++;
        const delay = Math.min(1000 * Math.pow(2, this.state.wsReconnectAttempts), 30000);

        console.log(`[WS] Reconnecting in ${delay/1000}s (attempt ${this.state.wsReconnectAttempts})`);
        this.state.wsReconnectTimeout = setTimeout(() => this.connectWebSocket(), delay);
    }

    sendWSMessage(data) {
        if (this.state.ws && this.state.ws.readyState === WebSocket.OPEN) {
            this.state.ws.send(JSON.stringify(data));
            return true;
        }
        console.warn('[WS] Cannot send, not connected');
        return false;
    }

    handleWebSocketMessage(data) {
        console.log('[WS] Received:', data.type);

        switch(data.type) {
            case 'connected':
                console.log('[WS] Client ID:', data.clientId);
                break;

            case 'new_message':
                if (data.conversationId == this.state.currentConversationId &&
                    data.userId != this.config.userId) {
                    this.addMessageToUI(data);
                    this.scrollToBottom();
                    this.hideTypingIndicator(data.userId);
                }
                break;

            case 'typing':
                if (data.conversationId == this.state.currentConversationId) {
                    if (data.isTyping) {
                        this.showTypingIndicator(data.userId, data.username);
                    } else {
                        this.hideTypingIndicator(data.userId);
                    }
                }
                break;

            case 'message_edited':
                if (data.conversationId == this.state.currentConversationId) {
                    this.updateMessageInUI(data.messageId, data.content);
                }
                break;

            case 'message_deleted':
                if (data.conversationId == this.state.currentConversationId) {
                    this.removeMessageFromUI(data.messageId);
                }
                break;

            case 'reaction_updated':
                if (data.conversationId == this.state.currentConversationId) {
                    console.log('[WS] Updating reactions:', data.messageId, data.reactions);
                    this.updateReactionsInUI(data.messageId, data.reactions);
                }
                break;
        }
    }

    joinConversation(conversationId) {
        console.log('🚪 Joining:', conversationId);
        this.sendWSMessage({
            type: 'join',
            conversationId,
            userId: this.config.userId,
            username: this.config.username
        });
        this.state.activeTypingUsers.clear();
        this.updateTypingDisplay();
    }

    leaveConversation() {
        if (this.state.currentConversationId) {
            console.log('👋 Leaving:', this.state.currentConversationId);
            this.sendWSMessage({ type: 'leave' });
            this.state.activeTypingUsers.clear();
            this.updateTypingDisplay();
        }
    }

    sendTypingIndicator(isTyping) {
        if (!this.state.currentConversationId) return;
        this.sendWSMessage({
            type: 'typing',
            isTyping,
            conversationId: this.state.currentConversationId,
            userId: this.config.userId,
            username: this.config.username
        });
    }

    // ========== TYPING INDICATORS ==========

    showTypingIndicator(userId, username) {
        if (userId === this.config.userId) return;

        if (this.state.activeTypingUsers.has(userId)) {
            clearTimeout(this.state.activeTypingUsers.get(userId).timeout);
        }

        const timeout = setTimeout(() => {
            this.state.activeTypingUsers.delete(userId);
            this.updateTypingDisplay();
        }, 3000);

        this.state.activeTypingUsers.set(userId, { username, timeout });
        this.updateTypingDisplay();
    }

    hideTypingIndicator(userId) {
        if (this.state.activeTypingUsers.has(userId)) {
            clearTimeout(this.state.activeTypingUsers.get(userId).timeout);
            this.state.activeTypingUsers.delete(userId);
            this.updateTypingDisplay();
        }
    }

    updateTypingDisplay() {
        const indicator = this.elements.typingIndicator;
        const text = this.elements.typingText;
        if (!indicator || !text) return;

        if (this.state.activeTypingUsers.size === 0) {
            indicator.classList.remove('show');
            return;
        }

        const usernames = Array.from(this.state.activeTypingUsers.values()).map(u => u.username);

        if (usernames.length === 1) {
            text.textContent = `${usernames[0]} is typing...`;
        } else if (usernames.length === 2) {
            text.textContent = `${usernames[0]} and ${usernames[1]} are typing...`;
        } else {
            text.textContent = `${usernames[0]} and ${usernames.length - 1} others are typing...`;
        }

        indicator.classList.add('show');
    }

    // ========== UI CONTROLS ==========

    openPopup() {
        this.elements.popup.classList.add('show');
        this.elements.dmBtn.style.display = 'none';
    }

    closePopup() {
        this.elements.popup.classList.remove('show');
        this.elements.dmBtn.style.display = 'flex';
        if (this.state.currentConversationId) {
            this.leaveConversation();
        }
    }

    goBackToList() {
        if (this.state.currentConversationId) {
            this.leaveConversation();
        }
        this.state.currentConversationId = null;
        this.exitEditMode();
        this.exitReplyMode();
        this.elements.chatPanel.classList.remove('show');
        this.elements.convPanel.classList.add('show');
        this.elements.backBtn.style.display = 'none';
        this.elements.chatTitle.textContent = 'Messages';
        this.elements.popupAvatar.style.display = 'none';
    }

    // ========== CONVERSATIONS ==========

    loadConversations(conversations) {
        this.elements.convList.innerHTML = '';
        conversations.forEach(conv => {
            const item = document.createElement('div');
            item.className = 'conv-item';
            item.dataset.id = conv.id;
            const title = conv.display_title || conv.title || 'Chat';
            item.dataset.name = title.toLowerCase();

            item.innerHTML = `
                <div class="avatar">${title.substring(0, 2).toUpperCase()}</div>
                <div>
                    <div class="name">${this.escapeHtml(title)}</div>
                    <div style="font-size:12.5px;opacity:.8">${conv.is_group ? 'Group' : 'Direct'}</div>
                </div>
            `;

            item.addEventListener('click', () => {
                this.loadConversation(conv.id, title);
            });

            this.elements.convList.appendChild(item);
        });
    }

    async loadConversation(convId, title) {
        if (this.state.currentConversationId) {
            this.leaveConversation();
        }

        this.state.currentConversationId = convId;
        this.state.messagesCache.clear();

        this.elements.chatTitle.textContent = title;
        this.elements.popupAvatar.textContent = title.substring(0, 2).toUpperCase();
        this.elements.popupAvatar.style.display = 'flex';

        this.exitEditMode();
        this.exitReplyMode();
        this.joinConversation(convId);

        try {
            const response = await fetch(`${this.config.apiBaseUrl}getMessages&id=${convId}`);
            const data = await response.json();

            this.elements.messages.innerHTML = '';

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    this.addMessageToUI({
                        messageId: msg.id,
                        userId: msg.user_id,
                        username: msg.username,
                        content: msg.content,
                        conversationId: convId,
                        timestamp: msg.created_at,
                        reactions: msg.reactions,
                        replyToId: msg.reply_to_id
                    });
                });
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }

        this.elements.convPanel.classList.remove('show');
        this.elements.chatPanel.classList.add('show');
        this.elements.backBtn.style.display = 'block';
    }

    filterConversations(query) {
        const lowerQuery = query.toLowerCase().trim();
        const items = this.elements.convList.querySelectorAll('.conv-item');

        items.forEach(item => {
            const name = item.dataset.name || '';
            item.style.display = name.includes(lowerQuery) ? 'flex' : 'none';
        });

        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
        let noResultsMsg = this.elements.convList.querySelector('.no-results-message');

        if (visibleItems.length === 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.style.cssText = 'padding:20px;text-align:center;opacity:0.6;font-size:14px;';
                noResultsMsg.textContent = 'No conversations found';
                this.elements.convList.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) noResultsMsg.remove();
        }
    }

    // ========== NEW CONVERSATION ==========

    showNewConvPanel() {
        this.elements.convPanel.classList.remove('show');
        this.elements.newConvPanel.classList.add('show');
        this.state.selectedUsers.clear();
        this.updateSelectedUsersList();
        this.elements.userSearchInput.value = '';
        this.elements.convTitleInput.value = '';
        this.elements.isGroupCheck.checked = false;
        this.elements.createConvBtn.disabled = true;
    }

    hideNewConvPanel() {
        this.elements.newConvPanel.classList.remove('show');
        this.elements.convPanel.classList.add('show');
    }

    async searchUsers(query) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}searchUsers&q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success && data.users) {
                this.displaySearchResults(data.users);
            }
        } catch (error) {
            console.error('Error searching users:', error);
        }
    }

    displaySearchResults(users) {
        this.elements.searchResults.innerHTML = '';

        if (users.length === 0) {
            this.elements.searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">No users found</p>';
            return;
        }

        users.forEach(user => {
            if (user.id == this.config.userId) return;

            const userDiv = document.createElement('div');
            userDiv.className = 'user-result-item';
            userDiv.innerHTML = `
                <div class="avatar">${user.username.substring(0, 2).toUpperCase()}</div>
                <div class="user-info">
                    <div class="name">${this.escapeHtml(user.username)}</div>
                </div>
            `;

            userDiv.addEventListener('click', () => this.toggleUserSelection(user));
            this.elements.searchResults.appendChild(userDiv);
        });
    }

    toggleUserSelection(user) {
        if (this.state.selectedUsers.has(user.id)) {
            this.state.selectedUsers.delete(user.id);
        } else {
            this.state.selectedUsers.set(user.id, user);
        }
        this.updateSelectedUsersList();
        this.updateCreateButton();
    }

    updateSelectedUsersList() {
        if (this.state.selectedUsers.size === 0) {
            this.elements.selectedUsers.style.display = 'none';
            return;
        }

        this.elements.selectedUsers.style.display = 'block';
        this.elements.selectedUsersList.innerHTML = '';

        this.state.selectedUsers.forEach((user, id) => {
            const chip = document.createElement('div');
            chip.className = 'user-chip';
            chip.innerHTML = `
                <span>${this.escapeHtml(user.username)}</span>
                <button class="remove-user" data-id="${id}">×</button>
            `;

            chip.querySelector('.remove-user').addEventListener('click', () => {
                this.state.selectedUsers.delete(id);
                this.updateSelectedUsersList();
                this.updateCreateButton();
            });

            this.elements.selectedUsersList.appendChild(chip);
        });
    }

    updateCreateButton() {
        this.elements.createConvBtn.disabled = this.state.selectedUsers.size === 0;
    }

    async createConversation() {
        if (this.state.selectedUsers.size === 0) return;

        const userIds = Array.from(this.state.selectedUsers.keys());
        const isGroup = this.elements.isGroupCheck.checked;
        const title = this.elements.convTitleInput.value.trim();

        try {
            const formData = new FormData();
            formData.append('user_ids', JSON.stringify(userIds));
            formData.append('is_group', isGroup ? '1' : '0');
            if (title) formData.append('title', title);

            const response = await fetch(`${this.config.apiBaseUrl}newConversation`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.conversation) {
                this.hideNewConvPanel();
                this.loadConversation(data.conversation.id, data.conversation.title);
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
        }
    }

// Continue to next comment for Messages section...
// ========== CONTINUATION OF DMPopup CLASS - Part 3 ==========
// Add these methods to complete the class

    // ========== MESSAGES ==========

    addMessageToUI(data) {
        const messagesDiv = this.elements.messages;
        const isOwn = data.userId == this.config.userId;
        const msgDiv = document.createElement('div');
        msgDiv.className = `msg ${isOwn ? 'sent' : 'received'}`;
        msgDiv.dataset.messageId = data.messageId;

        this.state.messagesCache.set(String(data.messageId), {
            content: data.content,
            username: data.username,
            userId: data.userId
        });

        const time = data.timestamp ? new Date(data.timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '';
        const content = this.escapeHtml(data.content).replace(/\n/g, '<br>');

        let replyPreview = '';
        if (data.replyToId) {
            const replyTo = this.state.messagesCache.get(String(data.replyToId));
            if (replyTo) {
                const replyContent = replyTo.content.length > 50 ? replyTo.content.substring(0, 50) + '...' : replyTo.content;
                replyPreview = `
                    <div class="reply-reference" onclick="window.dmPopup.scrollToMessage('${data.replyToId}')">
                        <div class="reply-reference-header">
                            <span>↩</span>
                            <span>${this.escapeHtml(replyTo.username)}</span>
                        </div>
                        <div class="reply-reference-content">${this.escapeHtml(replyContent)}</div>
                    </div>
                `;
            }
        }

        msgDiv.innerHTML = `
            <div class="msg-content">
                ${replyPreview}
                <div class="msg-text">${content}</div>
                <small>${this.escapeHtml(data.username)} • ${time}</small>
                <div class="msg-reactions"></div>
            </div>
        `;

        const actionsWrapper = document.createElement('div');
        actionsWrapper.className = 'msg-actions-wrapper';
        actionsWrapper.innerHTML = `
            <div class="msg-actions">
                <button class="msg-action-btn reply-btn" title="Reply">
                    <i class="fas fa-reply"></i>
                </button>
                <button class="msg-action-btn react-btn" title="React">
                    <i class="fas fa-smile"></i>
                </button>
                ${isOwn ? `
                <button class="msg-action-btn edit-btn" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="msg-action-btn delete-btn" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
                ` : ''}
            </div>
        `;
        msgDiv.appendChild(actionsWrapper);

        const replyBtn = actionsWrapper.querySelector('.reply-btn');
        replyBtn.onclick = (e) => {
            e.stopPropagation();
            this.enterReplyMode(data.messageId, data.content, data.username);
        };

        const reactBtn = actionsWrapper.querySelector('.react-btn');
        reactBtn.onclick = (e) => {
            e.stopPropagation();
            this.createEmojiPicker(data.messageId, reactBtn);
        };

        msgDiv.onclick = (e) => this.handleMessageDoubleClick(data.messageId, e);

        if (isOwn) {
            actionsWrapper.querySelector('.edit-btn').onclick = (e) => {
                e.stopPropagation();
                this.enterEditMode(data.messageId, data.content);
            };
            actionsWrapper.querySelector('.delete-btn').onclick = (e) => {
                e.stopPropagation();
                this.openDeleteModal(data.messageId);
            };
        }

        messagesDiv.appendChild(msgDiv);

        if (data.reactions) this.updateReactionsInUI(data.messageId, data.reactions);

        return msgDiv;
    }

    updateMessageInUI(messageId, newContent) {
        const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (msgEl) {
            const textEl = msgEl.querySelector('.msg-text');
            if (textEl) {
                textEl.innerHTML = this.escapeHtml(newContent).replace(/\n/g, '<br>');
            }
            if (this.state.messagesCache.has(String(messageId))) {
                this.state.messagesCache.get(String(messageId)).content = newContent;
            }
        }
    }

    removeMessageFromUI(messageId) {
        const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (msgEl) {
            msgEl.style.opacity = '0';
            msgEl.style.transform = 'scale(0.8)';
            setTimeout(() => {
                msgEl.remove();
                this.state.messagesCache.delete(String(messageId));
            }, 300);
        }
    }

    scrollToBottom() {
        const messagesDiv = this.elements.messages;
        if (messagesDiv) messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    scrollToMessage(messageId) {
        const targetMsg = document.querySelector(`[data-message-id="${messageId}"]`);
        if (targetMsg) {
            targetMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetMsg.classList.add('highlight');
            setTimeout(() => targetMsg.classList.remove('highlight'), 1000);
        }
    }

    // ========== MESSAGE INPUT ==========

    handleMessageInput() {
        const input = this.elements.messageInput;
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';

        if (this.state.isEditMode || !this.state.currentConversationId) return;

        const hasContent = input.value.trim().length > 0;

        if (hasContent) {
            if (!this.state.isCurrentlyTyping) {
                this.sendTypingIndicator(true);
                this.state.isCurrentlyTyping = true;
            }
            if (this.state.typingTimeout) clearTimeout(this.state.typingTimeout);
            this.state.typingTimeout = setTimeout(() => {
                this.sendTypingIndicator(false);
                this.state.isCurrentlyTyping = false;
            }, 2000);
        } else {
            if (this.state.isCurrentlyTyping) {
                if (this.state.typingTimeout) clearTimeout(this.state.typingTimeout);
                this.sendTypingIndicator(false);
                this.state.isCurrentlyTyping = false;
            }
        }
    }

    handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.elements.messageInput.value.trim() !== '') {
                if (this.state.isCurrentlyTyping) {
                    if (this.state.typingTimeout) clearTimeout(this.state.typingTimeout);
                    this.sendTypingIndicator(false);
                    this.state.isCurrentlyTyping = false;
                }
                this.elements.messageForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        } else if (e.key === 'Escape') {
            if (this.state.replyingToMessageId) this.exitReplyMode();
            else if (this.state.isEditMode) this.exitEditMode();
        }
    }

    handleInputBlur() {
        if (this.state.isCurrentlyTyping) {
            if (this.state.typingTimeout) clearTimeout(this.state.typingTimeout);
            this.sendTypingIndicator(false);
            this.state.isCurrentlyTyping = false;
        }
    }

    async handleMessageSubmit(e) {
        e.preventDefault();
        const content = this.elements.messageInput.value.trim();
        if (!content || !this.state.currentConversationId) return;

        if (this.state.typingTimeout) clearTimeout(this.state.typingTimeout);
        this.sendTypingIndicator(false);

        if (this.state.isEditMode) {
            await this.editMessageHTTP(this.state.editingMessageId, content);
            this.exitEditMode();
        } else {
            const tempMsgId = 'temp_' + Date.now();
            const messageData = {
                messageId: tempMsgId,
                userId: this.config.userId,
                username: this.config.username,
                content,
                conversationId: this.state.currentConversationId,
                timestamp: new Date().toISOString(),
                replyToId: this.state.replyingToMessageId
            };

            this.addMessageToUI(messageData);

            this.sendWSMessage({
                type: 'message',
                messageId: tempMsgId,
                content,
                replyToId: this.state.replyingToMessageId
            });

            await this.saveMessageHTTP(content, this.state.replyingToMessageId);
            this.exitReplyMode();
            this.scrollToBottom();
        }

        this.elements.messageInput.value = '';
        this.elements.messageInput.style.height = 'auto';
    }

    async saveMessageHTTP(content, replyToId = null) {
        try {
            const formData = new FormData();
            formData.append('mode', 'send');
            formData.append('content', content);
            if (replyToId) formData.append('reply_to_id', replyToId);
            await fetch(`${this.config.apiBaseUrl}conversation&id=${this.state.currentConversationId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
        } catch (error) {
            console.error('Error saving message:', error);
        }
    }

    async editMessageHTTP(messageId, content) {
        try {
            const formData = new FormData();
            formData.append('mode', 'edit');
            formData.append('message_id', messageId);
            formData.append('content', content);
            const response = await fetch(`${this.config.apiBaseUrl}conversation&id=${this.state.currentConversationId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            if (response.ok) {
                this.sendWSMessage({ type: 'message_edit', messageId, content });
                this.updateMessageInUI(messageId, content);
            }
        } catch (error) {
            console.error('Error editing message:', error);
        }
    }

    async deleteMessageHTTP(messageId) {
        try {
            const formData = new FormData();
            formData.append('mode', 'delete');
            formData.append('message_id', messageId);
            const response = await fetch(`${this.config.apiBaseUrl}conversation&id=${this.state.currentConversationId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            if (response.ok) {
                this.sendWSMessage({ type: 'message_delete', messageId });
                this.removeMessageFromUI(messageId);
            }
        } catch (error) {
            console.error('Error deleting message:', error);
        }
    }

    // ========== EDIT MODE ==========

    enterEditMode(messageId, content) {
        this.exitReplyMode();
        this.state.isEditMode = true;
        this.state.editingMessageId = messageId;
        this.elements.messageInput.value = content;
        this.elements.messageInput.placeholder = 'Edit your message...';
        this.elements.editBanner.style.display = 'flex';
        this.elements.sendBtn.innerHTML = '<i class="fas fa-check"></i>';
        this.elements.messageInput.focus();
    }

    exitEditMode() {
        this.state.isEditMode = false;
        this.state.editingMessageId = null;
        this.elements.messageInput.value = '';
        this.elements.messageInput.placeholder = 'Type a message...';
        this.elements.editBanner.style.display = 'none';
        this.elements.sendBtn.innerHTML = '➤';
        document.querySelectorAll('.msg.editing').forEach(msg => msg.classList.remove('editing'));
    }

    // ========== REPLY MODE ==========

    enterReplyMode(messageId, content, username) {
        this.state.replyingToMessageId = messageId;
        this.state.replyingToContent = content;

        let banner = this.elements.replyBanner;
        if (!banner.hasChildNodes()) {
            banner.innerHTML = `
                <div class="reply-banner-content">
                    <div class="reply-icon">↩</div>
                    <div class="reply-info">
                        <div class="reply-to-user" id="replyToUser"></div>
                        <div class="reply-preview-text" id="replyPreviewText"></div>
                    </div>
                    <button id="cancelReplyBtn" class="cancel-reply-btn">
                        <i class="fas fa-times"></i><span>Cancel</span>
                    </button>
                </div>
            `;
            document.getElementById('cancelReplyBtn').onclick = () => this.exitReplyMode();
        }

        document.getElementById('replyToUser').textContent = `Replying to ${username}`;
        const truncated = content.length > 50 ? content.substring(0, 50) + '...' : content;
        document.getElementById('replyPreviewText').textContent = truncated;
        banner.classList.add('active');
        this.elements.messageInput.focus();
    }

    exitReplyMode() {
        this.state.replyingToMessageId = null;
        this.state.replyingToContent = null;
        this.elements.replyBanner.classList.remove('active');
    }

    // ========== EMOJI PICKER ==========

    createEmojiPicker(messageId, buttonElement) {
        this.closeAllEmojiPickers();

        const picker = document.createElement('div');
        picker.className = 'emoji-picker-full';
        picker.id = `emoji-picker-${messageId}`;

        const quickBar = document.createElement('div');
        quickBar.className = 'emoji-quick-bar';
        this.QUICK_REACTIONS.forEach(emoji => {
            const btn = document.createElement('button');
            btn.className = 'emoji-quick-btn';
            btn.textContent = emoji;
            btn.onclick = (e) => {
                e.stopPropagation();
                this.toggleReaction(messageId, emoji);
                this.closeAllEmojiPickers();
            };
            quickBar.appendChild(btn);
        });
        picker.appendChild(quickBar);

        const tabs = document.createElement('div');
        tabs.className = 'emoji-tabs';
        const categoryNames = Object.keys(this.EMOJI_CATEGORIES);
        categoryNames.forEach((cat, index) => {
            const tab = document.createElement('button');
            tab.className = 'emoji-tab' + (index === 0 ? ' active' : '');
            tab.textContent = cat;
            tab.onclick = (e) => {
                e.stopPropagation();
                document.querySelectorAll('.emoji-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                this.showEmojiCategory(cat, emojiGrid, messageId);
            };
            tabs.appendChild(tab);
        });
        picker.appendChild(tabs);

        const emojiGrid = document.createElement('div');
        emojiGrid.className = 'emoji-grid';
        picker.appendChild(emojiGrid);

        this.showEmojiCategory(categoryNames[0], emojiGrid, messageId);

        const msgElement = buttonElement.closest('.msg');
        const messageRect = msgElement.getBoundingClientRect();
        const dmPopup = this.elements.popup;
        const dmPopupRect = dmPopup.getBoundingClientRect();
        const messageCenterY = messageRect.y + (messageRect.height / 2);
        const dmPopupCenterY = dmPopupRect.y + (dmPopupRect.height / 2);

        msgElement.appendChild(picker);
        this.state.currentEmojiPicker = picker;

        if (messageCenterY > dmPopupCenterY) {
            picker.style.bottom = '5%';
            picker.style.top = 'auto';
        } else {
            picker.style.top = '0%';
            picker.style.bottom = 'auto';
        }

        setTimeout(() => {
            document.addEventListener('click', () => this.closeAllEmojiPickers(), { once: true });
        }, 0);
    }

    showEmojiCategory(category, container, messageId) {
        container.innerHTML = '';
        const emojis = this.EMOJI_CATEGORIES[category] || [];
        emojis.forEach(emoji => {
            const btn = document.createElement('button');
            btn.className = 'emoji-grid-btn';
            btn.textContent = emoji;
            btn.onclick = (e) => {
                e.stopPropagation();
                this.toggleReaction(messageId, emoji);
                this.closeAllEmojiPickers();
            };
            container.appendChild(btn);
        });
    }

    closeAllEmojiPickers() {
        document.querySelectorAll('.emoji-picker-full').forEach(p => p.remove());
        this.state.currentEmojiPicker = null;
    }

    handleMessageDoubleClick(messageId, event) {
        const now = Date.now();
        if (this.state.lastClickedMessageId === messageId && now - this.state.lastClickTime < 500) {
            event.preventDefault();
            this.toggleReaction(messageId, '❤️');
            this.showHeartAnimation(event.target);
        }
        this.state.lastClickTime = now;
        this.state.lastClickedMessageId = messageId;
    }

    showHeartAnimation(element) {
        const heart = document.createElement('div');
        heart.className = 'heart-animation';
        heart.textContent = '❤️';
        const rect = element.getBoundingClientRect();
        heart.style.left = rect.left + rect.width / 2 + 'px';
        heart.style.top = rect.top + rect.height / 2 + 'px';
        document.body.appendChild(heart);
        setTimeout(() => heart.remove(), 1000);
    }

    // ========== REACTIONS ==========

    async toggleReaction(messageId, emoji) {
        console.log('[REACTION] Toggling:', messageId, emoji);

        const currentReactions = this.getCurrentReactions(messageId);
        const userIdStr = String(this.config.userId);

        if (!currentReactions[emoji]) currentReactions[emoji] = [];

        const index = currentReactions[emoji].indexOf(userIdStr);
        if (index !== -1) {
            currentReactions[emoji].splice(index, 1);
            if (currentReactions[emoji].length === 0) delete currentReactions[emoji];
        } else {
            currentReactions[emoji].push(userIdStr);
        }

        this.updateReactionsInUI(messageId, currentReactions);

        console.log('[REACTION] Broadcasting via WS');
        this.sendWSMessage({
            type: 'reaction_update',
            messageId,
            reactions: currentReactions,
            conversationId: this.state.currentConversationId
        });

        try {
            const formData = new FormData();
            formData.append('message_id', messageId);
            formData.append('emoji', emoji);
            const response = await fetch(`${this.config.apiBaseUrl}toggleReaction`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                console.log('[REACTION] DB updated');
                this.updateReactionsInUI(messageId, data.reactions);
            }
        } catch (error) {
            console.error('Error toggling reaction:', error);
        }
    }

    getCurrentReactions(messageId) {
        const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (!msgEl) return {};
        const reactionsContainer = msgEl.querySelector('.msg-reactions');
        if (!reactionsContainer) return {};
        const reactions = {};
        reactionsContainer.querySelectorAll('.reaction-bubble').forEach(bubble => {
            const emoji = bubble.querySelector('.reaction-emoji').textContent;
            const count = parseInt(bubble.querySelector('.reaction-count').textContent);
            const hasUserReacted = bubble.classList.contains('user-reacted');
            reactions[emoji] = [];
            if (hasUserReacted) reactions[emoji].push(String(this.config.userId));
        });
        return reactions;
    }

    updateReactionsInUI(messageId, reactions) {
        console.log('[UI] Updating reactions:', messageId, reactions);
        const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (!msgEl) {
            console.warn('[UI] Message not found:', messageId);
            return;
        }

        let reactionsContainer = msgEl.querySelector('.msg-reactions');
        if (!reactionsContainer) {
            reactionsContainer = document.createElement('div');
            reactionsContainer.className = 'msg-reactions';
            const contentDiv = msgEl.querySelector('.msg-content');
            if (contentDiv) contentDiv.appendChild(reactionsContainer);
        }

        reactionsContainer.innerHTML = '';

        if (reactions && Object.keys(reactions).length > 0) {
            Object.entries(reactions).forEach(([emoji, userIds]) => {
                if (!userIds || userIds.length === 0) return;
                const reactionBtn = document.createElement('button');
                reactionBtn.className = 'reaction-bubble';
                const userIdStr = String(this.config.userId);
                if (userIds.includes(userIdStr) || userIds.includes(this.config.userId)) {
                    reactionBtn.classList.add('user-reacted');
                }
                reactionBtn.innerHTML = `<span class="reaction-emoji">${emoji}</span><span class="reaction-count">${userIds.length}</span>`;
                reactionBtn.onclick = (e) => {
                    e.stopPropagation();
                    this.toggleReaction(messageId, emoji);
                };
                reactionsContainer.appendChild(reactionBtn);
            });
        }
    }

    // ========== DELETE MODAL ==========

    openDeleteModal(msgId) {
        this.state.currentDeleteMessageId = msgId;
        this.elements.deleteModal.classList.add('show');
    }

    closeDeleteModal() {
        this.elements.deleteModal.classList.remove('show');
        this.state.currentDeleteMessageId = null;
    }

    async confirmDelete() {
        if (this.state.currentDeleteMessageId) {
            await this.deleteMessageHTTP(this.state.currentDeleteMessageId);
            this.closeDeleteModal();
        }
    }

    // ========== UTILITIES ==========

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    destroy() {
        if (this.state.ws) {
            this.state.ws.close();
        }
        if (this.state.wsReconnectTimeout) {
            clearTimeout(this.state.wsReconnectTimeout);
        }
        if (this.state.typingTimeout) {
            clearTimeout(this.state.typingTimeout);
        }

        // Remove HTML elements
        document.getElementById('galaxyCanvas')?.remove();
        document.querySelector('.bg-animation')?.remove();
        document.getElementById('dm-btn')?.remove();
        document.getElementById('dm-popup')?.remove();
        document.getElementById('deleteModal')?.remove();
    }
}

// Make instance globally available for inline onclick handlers
window.dmPopup = null;

// Auto-init if config is in window
if (typeof window !== 'undefined') {
    window.DMPopup = DMPopup;
}