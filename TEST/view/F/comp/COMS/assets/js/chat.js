// view/F/assets/js/chat.js

// view/F/assets/js/chat.js

document.addEventListener("DOMContentLoaded", function () {
    // Auto-scroll messages
    const msgBox = document.getElementById("messages");
    if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;

    // Edit message toggle
    document.querySelectorAll(".message .edit-message").forEach(btn => {
        btn.addEventListener("click", () => {
            const msg = btn.closest(".message");
            msg?.querySelector(".edit-message-form")?.classList.remove("d-none");
            msg?.querySelector(".text")?.classList.add("d-none");
        });
    });

    document.querySelectorAll(".message .cancel-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            const msg = btn.closest(".message");
            msg?.querySelector(".edit-message-form")?.classList.add("d-none");
            msg?.querySelector(".text")?.classList.remove("d-none");
        });
    });

    // ───────── POPUP CONTROLS ─────────
    const popup = document.getElementById('dm-popup');
    const dmBtn = document.getElementById('dm-btn');
    const closeBtn = document.getElementById('closeBtn');
    const maximizeBtn = document.getElementById('maximizeBtn');

    if (dmBtn) {
        dmBtn.addEventListener('click', () => {
            console.log('DM button clicked');
            if (popup) {
                popup.classList.add('show');
                dmBtn.style.display = 'none';
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            if (popup) {
                popup.classList.remove('show');
                dmBtn.style.display = 'flex';
                if (currentConversationId) leaveConversation();
            }
        });
    }

    if (maximizeBtn) {
        maximizeBtn.addEventListener('click', () => {
            if (popup) popup.classList.toggle('maximized');
        });
    }

    // ───────── GALAXY STARS ANIMATION ─────────
    const canvas = document.getElementById("galaxyCanvas");
    if (canvas) {
        const ctx = canvas.getContext("2d");
        let w, h;

        const resize = () => {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        };
        resize();
        window.addEventListener("resize", resize);

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

        function drawStars() {
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
        }
        drawStars();
    }
});

// ===== WEBSOCKET =====
let ws = null;
let wsReconnectAttempts = 0;
let wsReconnectTimeout = null;
let typingTimeout = null;
let currentConversationId = null;
let isEditMode = false;
let editingMessageId = null;

// ===== TYPING INDICATOR MANAGEMENT =====
const activeTypingUsers = new Map();

function showTypingIndicator(userId, username) {
    if (userId === CURRENT_USER_ID) return;

    if (activeTypingUsers.has(userId)) {
        clearTimeout(activeTypingUsers.get(userId).timeout);
    }

    const timeout = setTimeout(() => {
        activeTypingUsers.delete(userId);
        updateTypingDisplay();
    }, 3000);

    activeTypingUsers.set(userId, { username, timeout });
    updateTypingDisplay();
}

function hideTypingIndicator(userId) {
    if (activeTypingUsers.has(userId)) {
        clearTimeout(activeTypingUsers.get(userId).timeout);
        activeTypingUsers.delete(userId);
        updateTypingDisplay();
    }
}

function updateTypingDisplay() {
    const indicator = document.getElementById('typingIndicator');
    const text = document.getElementById('typingText');

    if (!indicator || !text) return;

    if (activeTypingUsers.size === 0) {
        indicator.classList.remove('show');
        return;
    }

    const usernames = Array.from(activeTypingUsers.values()).map(u => u.username);

    if (usernames.length === 1) {
        text.textContent = `${usernames[0]} is typing...`;
    } else if (usernames.length === 2) {
        text.textContent = `${usernames[0]} and ${usernames[1]} are typing...`;
    } else {
        text.textContent = `${usernames[0]} and ${usernames.length - 1} others are typing...`;
    }

    indicator.classList.add('show');
}

function connectWebSocket() {
    try {
        ws = new WebSocket(WS_URL);

        ws.onopen = () => {
            console.log('[WS] ✅ Connected');
            wsReconnectAttempts = 0;
            updateConnectionStatus('connected');
            if (currentConversationId) joinConversation(currentConversationId);
        };

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                handleWebSocketMessage(data);
            } catch (e) {
                console.error('[WS] Parse error:', e);
            }
        };

        ws.onclose = () => {
            console.log('[WS] ⚠️ Disconnected');
            updateConnectionStatus('disconnected');
            attemptReconnect();
        };

        ws.onerror = (error) => {
            console.error('[WS] ❌ Error:', error);
            updateConnectionStatus('disconnected');
        };
    } catch (error) {
        console.error('[WS] Connection failed:', error);
        updateConnectionStatus('disconnected');
        attemptReconnect();
    }
}

function attemptReconnect() {
    if (wsReconnectAttempts >= 10) {
        console.log('[WS] Max reconnection attempts reached');
        return;
    }
    wsReconnectAttempts++;
    const delay = Math.min(1000 * Math.pow(2, wsReconnectAttempts), 30000);
    console.log(`[WS] Reconnecting in ${delay/1000}s (attempt ${wsReconnectAttempts})`);
    updateConnectionStatus('connecting');
    wsReconnectTimeout = setTimeout(connectWebSocket, delay);
}

function updateConnectionStatus(status) {
    const statusEl = document.getElementById('wsStatus');
    if (!statusEl) return;
    const dotEl = statusEl.querySelector('.connection-dot');
    const textEl = document.getElementById('wsStatusText');
    if (!dotEl || !textEl) return;

    dotEl.className = 'connection-dot';
    switch(status) {
        case 'connected':
            dotEl.classList.add('connected');
            textEl.textContent = 'Live';
            break;
        case 'connecting':
            dotEl.classList.add('connecting');
            textEl.textContent = 'Connecting...';
            break;
        case 'disconnected':
            dotEl.classList.add('disconnected');
            textEl.textContent = 'Offline';
            break;
    }
}

function sendWSMessage(data) {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify(data));
        return true;
    }
    console.warn('[WS] Cannot send, not connected');
    return false;
}

function handleWebSocketMessage(data) {
    console.log('[WS] Received:', data.type, data);

    switch(data.type) {
        case 'connected':
            console.log('[WS] Client ID:', data.clientId);
            break;

        case 'new_message':
            if (data.conversationId == currentConversationId && data.userId != CURRENT_USER_ID) {
                addMessageToUI(data);
                scrollToBottom();
                hideTypingIndicator(data.userId);
            }
            break;

        case 'typing':
            console.log('🔎 Typing event received:', data);
            if (data.conversationId == currentConversationId) {
                if (data.isTyping) {
                    showTypingIndicator(data.userId, data.username);
                } else {
                    hideTypingIndicator(data.userId);
                }
            }
            break;

        case 'message_edited':
            if (data.conversationId == currentConversationId) {
                updateMessageInUI(data.messageId, data.content);
            }
            break;

        case 'message_deleted':
            if (data.conversationId == currentConversationId) {
                removeMessageFromUI(data.messageId);
            }
            break;
    }
}

function joinConversation(conversationId) {
    console.log('🚪 Joining conversation:', conversationId);
    sendWSMessage({
        type: 'join',
        conversationId,
        userId: CURRENT_USER_ID,
        username: CURRENT_USERNAME
    });
    activeTypingUsers.clear();
    updateTypingDisplay();
}

function leaveConversation() {
    if (currentConversationId) {
        console.log('👋 Leaving conversation:', currentConversationId);
        sendWSMessage({ type: 'leave' });
        activeTypingUsers.clear();
        updateTypingDisplay();
    }
}

function sendTypingIndicator(isTyping) {
    if (!currentConversationId) {
        console.warn('❌ Cannot send typing: no conversation selected');
        return;
    }

    console.log(`📤 Sending typing indicator: ${isTyping}, conv: ${currentConversationId}`);

    const success = sendWSMessage({
        type: 'typing',
        isTyping: isTyping,
        conversationId: currentConversationId,
        userId: CURRENT_USER_ID,
        username: CURRENT_USERNAME
    });

    if (!success) {
        console.error('❌ Failed to send typing - WebSocket not connected');
    }
}

function addMessageToUI(data) {
    const messagesDiv = document.getElementById('messages');
    const isOwn = data.userId == CURRENT_USER_ID;
    const msgDiv = document.createElement('div');
    msgDiv.className = `msg ${isOwn ? 'sent' : 'received'}`;
    msgDiv.dataset.messageId = data.messageId;

    const time = data.timestamp ? new Date(data.timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '';
    const content = escapeHtml(data.content).replace(/\n/g, '<br>');

    msgDiv.innerHTML = `
    <div class="msg-content">
        <div class="msg-text">${content}</div>
        <small>${escapeHtml(data.username)} • ${time}</small>
    </div>
`;

    if (isOwn) {
        const actionsWrapper = document.createElement('div');
        actionsWrapper.className = 'msg-actions-wrapper';
        actionsWrapper.innerHTML = `
        <div class="msg-actions">
            <button class="msg-action-btn edit-btn" data-id="${data.messageId}" data-content="${escapeHtml(data.content)}" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="msg-action-btn delete-btn" data-id="${data.messageId}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
        msgDiv.appendChild(actionsWrapper);

        actionsWrapper.querySelector('.edit-btn').onclick = (e) => {
            e.stopPropagation();
            enterEditMode(data.messageId, data.content);
        };
        actionsWrapper.querySelector('.delete-btn').onclick = (e) => {
            e.stopPropagation();
            openDeleteModal(data.messageId);
        };
    }

    messagesDiv.appendChild(msgDiv);
    return msgDiv;
}

function updateMessageInUI(messageId, newContent) {
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (msgEl) {
        const textEl = msgEl.querySelector('.msg-text');
        if (textEl) {
            textEl.innerHTML = escapeHtml(newContent).replace(/\n/g, '<br>');
            const editBtn = msgEl.querySelector('.edit-btn');
            if (editBtn) editBtn.dataset.content = newContent;
        }
    }
}

function removeMessageFromUI(messageId) {
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (msgEl) {
        msgEl.style.opacity = '0';
        msgEl.style.transform = 'scale(0.8)';
        setTimeout(() => msgEl.remove(), 300);
    }
}

function scrollToBottom() {
    const messagesDiv = document.getElementById('messages');
    if (messagesDiv) messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== MESSAGE INPUT =====
const messageInput = document.getElementById('messageInput');
const messageForm = document.getElementById('messageForm');

if (messageInput) {
    let isCurrentlyTyping = false;

    messageInput.addEventListener('input', function() {
        // Auto-resize textarea
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';

        // Don't send typing indicator in edit mode
        if (isEditMode) {
            console.log('⏭️ Skipping typing (edit mode)');
            return;
        }

        // Don't send if no conversation selected
        if (!currentConversationId) {
            console.log('⏭️ Skipping typing (no conversation)');
            return;
        }

        const hasContent = this.value.trim().length > 0;

        console.log('⌨️ Input detected:', { hasContent, isCurrentlyTyping, convId: currentConversationId });

        if (hasContent) {
            // Start typing if not already
            if (!isCurrentlyTyping) {
                console.log('🟢 START typing indicator');
                sendTypingIndicator(true);
                isCurrentlyTyping = true;
            }

            // Reset stop timer
            if (typingTimeout) clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                console.log('🔴 STOP typing indicator (timeout)');
                sendTypingIndicator(false);
                isCurrentlyTyping = false;
            }, 2000);
        } else {
            // Empty input - stop typing
            if (isCurrentlyTyping) {
                console.log('🔴 STOP typing indicator (empty)');
                if (typingTimeout) clearTimeout(typingTimeout);
                sendTypingIndicator(false);
                isCurrentlyTyping = false;
            }
        }
    });

    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            const currentValue = this.value;
            const lastChar = currentValue.charAt(currentValue.length - 1);

            if (lastChar === ' ') {
                return;
            }

            e.preventDefault();

            if (currentValue.trim() !== '') {
                // Stop typing before sending
                if (isCurrentlyTyping) {
                    console.log('🔴 STOP typing indicator (sending message)');
                    if (typingTimeout) clearTimeout(typingTimeout);
                    sendTypingIndicator(false);
                    isCurrentlyTyping = false;
                }

                messageForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    });

    // Stop typing when input loses focus
    messageInput.addEventListener('blur', function() {
        if (isCurrentlyTyping) {
            console.log('🔴 STOP typing indicator (blur)');
            if (typingTimeout) clearTimeout(typingTimeout);
            sendTypingIndicator(false);
            isCurrentlyTyping = false;
        }
    });
}

if (messageForm) {
    messageForm.onsubmit = async (e) => {
        e.preventDefault();
        const content = messageInput.value.trim();
        if (!content || !currentConversationId) return;

        // Ensure typing is stopped
        if (typingTimeout) clearTimeout(typingTimeout);
        sendTypingIndicator(false);

        if (isEditMode) {
            await editMessageHTTP(editingMessageId, content);
            exitEditMode();
        } else {
            const tempMsgId = 'temp_' + Date.now();
            addMessageToUI({
                messageId: tempMsgId,
                userId: CURRENT_USER_ID,
                username: CURRENT_USERNAME,
                content,
                conversationId: currentConversationId,
                timestamp: new Date().toISOString()
            });

            sendWSMessage({ type: 'message', messageId: tempMsgId, content });
            await saveMessageHTTP(content);
            scrollToBottom();
        }

        messageInput.value = '';
        messageInput.style.height = 'auto';
    };
}

async function saveMessageHTTP(content) {
    try {
        const formData = new FormData();
        formData.append('mode', 'send');
        formData.append('content', content);
        await fetch(`index.php?c=chatC&a=conversation&id=${currentConversationId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
    } catch (error) {
        console.error('Error saving message:', error);
    }
}

async function editMessageHTTP(messageId, content) {
    try {
        const formData = new FormData();
        formData.append('mode', 'edit');
        formData.append('message_id', messageId);
        formData.append('content', content);
        const response = await fetch(`index.php?c=chatC&a=conversation&id=${currentConversationId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (response.ok) {
            sendWSMessage({ type: 'message_edit', messageId, content });
            updateMessageInUI(messageId, content);
        }
    } catch (error) {
        console.error('Error editing message:', error);
    }
}

async function deleteMessageHTTP(messageId) {
    try {
        const formData = new FormData();
        formData.append('mode', 'delete');
        formData.append('message_id', messageId);
        const response = await fetch(`index.php?c=chatC&a=conversation&id=${currentConversationId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (response.ok) {
            sendWSMessage({ type: 'message_delete', messageId });
            removeMessageFromUI(messageId);
        }
    } catch (error) {
        console.error('Error deleting message:', error);
    }
}

function enterEditMode(messageId, content) {
    isEditMode = true;
    editingMessageId = messageId;
    messageInput.value = content;
    messageInput.placeholder = 'Edit your message...';
    document.getElementById('editBanner').style.display = 'flex';
    document.getElementById('sendBtn').innerHTML = '<i class="fas fa-check"></i>';
    messageInput.focus();
}

function exitEditMode() {
    isEditMode = false;
    editingMessageId = null;
    messageInput.value = '';
    messageInput.placeholder = 'Type a message...';
    const editBanner = document.getElementById('editBanner');
    const sendBtn = document.getElementById('sendBtn');
    if (editBanner) editBanner.style.display = 'none';
    if (sendBtn) sendBtn.innerHTML = '➤';
    document.querySelectorAll('.msg.editing').forEach(msg => msg.classList.remove('editing'));
}

const cancelEditBtn = document.getElementById('cancelEditBtn');
if (cancelEditBtn) {
    cancelEditBtn.onclick = exitEditMode;
}

// ===== DELETE MODAL =====
let currentDeleteMessageId = null;
const deleteModal = document.getElementById('deleteModal');

function openDeleteModal(msgId) {
    currentDeleteMessageId = msgId;
    if (deleteModal) deleteModal.classList.add('show');
}

function closeDeleteModal() {
    if (deleteModal) deleteModal.classList.remove('show');
    currentDeleteMessageId = null;
}

const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
const cancelDeleteBtn = document.getElementById('cancelDelete');
const confirmDeleteBtn = document.getElementById('confirmDelete');

if (closeDeleteModalBtn) closeDeleteModalBtn.onclick = closeDeleteModal;
if (cancelDeleteBtn) cancelDeleteBtn.onclick = closeDeleteModal;
if (confirmDeleteBtn) {
    confirmDeleteBtn.onclick = async () => {
        if (currentDeleteMessageId) {
            await deleteMessageHTTP(currentDeleteMessageId);
            closeDeleteModal();
        }
    };
}

// ===== LOAD CONVERSATION =====
async function loadConversation(convId, title) {
    if (currentConversationId) leaveConversation();
    currentConversationId = convId;
    const chatTitle = document.getElementById('chatTitle');
    const popupAvatar = document.getElementById('popup-avatar');

    if (chatTitle) chatTitle.textContent = title;

    // Show and update avatar with conversation initials
    if (popupAvatar) {
        popupAvatar.textContent = title.substring(0, 2).toUpperCase();
        popupAvatar.style.display = 'flex';
    }

    exitEditMode();
    joinConversation(convId);

    try {
        const response = await fetch(`index.php?c=chatC&a=getMessages&id=${convId}`);
        const data = await response.json();
        const messagesDiv = document.getElementById('messages');
        if (messagesDiv) messagesDiv.innerHTML = '';

        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                addMessageToUI({
                    messageId: msg.id,
                    userId: msg.user_id,
                    username: msg.username,
                    content: msg.content,
                    conversationId: convId,
                    timestamp: msg.created_at
                });
            });
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }

    const convPanel = document.getElementById('convPanel');
    const chatPanel = document.getElementById('chatPanel');
    const backBtn = document.getElementById('backBtn');

    if (convPanel) convPanel.classList.remove('show');
    if (chatPanel) chatPanel.classList.add('show');
    if (backBtn) backBtn.style.display = 'block';
}

// ===== BACK BUTTON =====
const backBtn = document.getElementById('backBtn');
if (backBtn) {
    backBtn.onclick = () => {
        if (currentConversationId) leaveConversation();
        currentConversationId = null;
        exitEditMode();

        const chatPanel = document.getElementById('chatPanel');
        const convPanel = document.getElementById('convPanel');
        const chatTitle = document.getElementById('chatTitle');
        const popupAvatar = document.getElementById('popup-avatar');

        if (chatPanel) chatPanel.classList.remove('show');
        if (convPanel) convPanel.classList.add('show');
        if (backBtn) backBtn.style.display = 'none';
        if (chatTitle) chatTitle.textContent = 'Messages';

        // Hide avatar when back to conversations list
        if (popupAvatar) {
            popupAvatar.style.display = 'none';
        }
    };
}

document.querySelectorAll('.conv-item').forEach(item => {
    item.onclick = () => loadConversation(item.dataset.id, item.querySelector('.name').textContent);
});

// ===== NEW CONVERSATION PANEL =====
const newConvBtn = document.getElementById('newConvBtn');
const newConvPanel = document.getElementById('newConvPanel');
const convPanel = document.getElementById('convPanel');
const backToConvBtn = document.getElementById('backToConvBtn');
const userSearchInput = document.getElementById('userSearchInput');
const searchUsersBtn = document.getElementById('searchUsersBtn');
const searchResults = document.getElementById('searchResults');
const selectedUsersDiv = document.getElementById('selectedUsers');
const selectedUsersList = document.getElementById('selectedUsersList');
const createConvBtn = document.getElementById('createConvBtn');
const isGroupCheck = document.getElementById('isGroupCheck');
const convTitleInput = document.getElementById('convTitleInput');

let selectedUsers = [];

if (newConvBtn) {
    newConvBtn.addEventListener('click', () => {
        if (convPanel) convPanel.classList.remove('show');
        if (newConvPanel) newConvPanel.classList.add('show');
        const backBtn = document.getElementById('backBtn');
        if (backBtn) backBtn.style.display = 'none';
    });
}

if (backToConvBtn) {
    backToConvBtn.addEventListener('click', () => {
        if (newConvPanel) newConvPanel.classList.remove('show');
        if (convPanel) convPanel.classList.add('show');
        resetNewConvPanel();
    });
}

async function searchUsers() {
    const query = userSearchInput.value.trim();

    if (query.length === 0) {
        searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">Start typing to search users...</p>';
        return;
    }

    if (query.length < 2) {
        searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">Type at least 2 characters...</p>';
        return;
    }

    try {
        searchResults.innerHTML = '<p style="padding:15px;text-align:center;"><i class="fas fa-spinner fa-spin"></i> Searching...</p>';

        const response = await fetch(`index.php?c=chatC&a=searchUsers&q=${encodeURIComponent(query)}`);
        const data = await response.json();

        if (data.success && data.users && data.users.length > 0) {
            searchResults.innerHTML = '';
            data.users.forEach(user => {
                const isSelected = selectedUsers.some(u => u.id === user.id);
                const userDiv = document.createElement('div');
                userDiv.className = 'user-result-item';
                userDiv.innerHTML = `
                <div class="avatar">${user.username.substring(0, 2).toUpperCase()}</div>
                <div class="user-info">
                    <div class="user-name">${escapeHtml(user.username)}</div>
                    <div class="user-email">${escapeHtml(user.email)}</div>
                </div>
                <button class="btn-add-user ${isSelected ? 'selected' : ''}" data-user-id="${user.id}">
                    <i class="fas ${isSelected ? 'fa-check' : 'fa-plus'}"></i>
                </button>
            `;

                const addBtn = userDiv.querySelector('.btn-add-user');
                addBtn.addEventListener('click', () => toggleUserSelection(user, addBtn));

                searchResults.appendChild(userDiv);
            });
        } else {
            searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">No users found</p>';
        }
    } catch (error) {
        console.error('Search error:', error);
        searchResults.innerHTML = '<p style="padding:15px;color:#ff6b6b;text-align:center;">Error searching users</p>';
    }
}

// Instant search for users as you type
let userSearchTimeout = null;

if (userSearchInput) {
    userSearchInput.addEventListener('input', function() {
        if (userSearchTimeout) {
            clearTimeout(userSearchTimeout);
        }

        userSearchTimeout = setTimeout(() => {
            searchUsers();
        }, 400);
    });

    userSearchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (userSearchTimeout) {
                clearTimeout(userSearchTimeout);
            }
            searchUsers();
        }
    });
}

if (searchUsersBtn) {
    searchUsersBtn.addEventListener('click', searchUsers);
}

function toggleUserSelection(user, buttonElement) {
    const index = selectedUsers.findIndex(u => u.id === user.id);

    if (index > -1) {
        selectedUsers.splice(index, 1);
        buttonElement.classList.remove('selected');
        buttonElement.innerHTML = '<i class="fas fa-plus"></i>';
    } else {
        selectedUsers.push(user);
        buttonElement.classList.add('selected');
        buttonElement.innerHTML = '<i class="fas fa-check"></i>';
    }

    updateSelectedUsers();
}
function updateSelectedUsers() {
    if (selectedUsers.length === 0) {
        if (selectedUsersDiv) selectedUsersDiv.style.display = 'none';
        if (createConvBtn) createConvBtn.disabled = true;
    } else {
        if (selectedUsersDiv) selectedUsersDiv.style.display = 'block';
        if (createConvBtn) createConvBtn.disabled = false;

        // Automatically check group option if more than one user is selected
        if (isGroupCheck && selectedUsers.length > 1) {
            isGroupCheck.checked = true;
        }

        if (selectedUsersList) {
            selectedUsersList.innerHTML = '';
            selectedUsers.forEach(user => {
                const userTag = document.createElement('div');
                userTag.className = 'selected-user-tag';
                userTag.innerHTML = `
                <span>${escapeHtml(user.username)}</span>
                <button class="remove-user-btn" data-user-id="${user.id}">
                    <i class="fas fa-times"></i>
                </button>
            `;

                userTag.querySelector('.remove-user-btn').addEventListener('click', () => {
                    selectedUsers = selectedUsers.filter(u => u.id !== user.id);
                    updateSelectedUsers();

                    const resultBtn = searchResults.querySelector(`[data-user-id="${user.id}"]`);
                    if (resultBtn) {
                        resultBtn.classList.remove('selected');
                        resultBtn.innerHTML = '<i class="fas fa-plus"></i>';
                    }
                });

                selectedUsersList.appendChild(userTag);
            });
        }
    }
}

function resetNewConvPanel() {
    selectedUsers = [];
    if (userSearchInput) userSearchInput.value = '';
    if (convTitleInput) convTitleInput.value = '';
    if (isGroupCheck) isGroupCheck.checked = false;
    if (searchResults) searchResults.innerHTML = '';
    updateSelectedUsers();
}

// ===== CONVERSATION SEARCH =====
const searchConvInput = document.getElementById('searchConvInput');
const convList = document.getElementById('convList');

if (searchConvInput && convList) {
    searchConvInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const convItems = convList.querySelectorAll('.conv-item');

        convItems.forEach(item => {
            const name = item.dataset.name || '';

            if (name.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });

        // Show "no results" message if all items are hidden
        const visibleItems = Array.from(convItems).filter(item => item.style.display !== 'none');
        let noResultsMsg = convList.querySelector('.no-results-message');

        if (visibleItems.length === 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.style.cssText = 'padding:20px;text-align:center;opacity:0.6;font-size:14px;';
                noResultsMsg.textContent = 'No conversations found';
                convList.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    });

    // Clear search on escape key
    searchConvInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
            this.blur();
        }
    });
}

// ===== CREATE CONVERSATION =====
if (createConvBtn) {
    createConvBtn.addEventListener('click', async () => {
        if (selectedUsers.length === 0) {
            alert('Please select at least one user');
            return;
        }

        // Disable button during creation
        createConvBtn.disabled = true;
        const originalText = createConvBtn.innerHTML;
        createConvBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

        try {
            const formData = new FormData();
            formData.append('mode', 'create');

            // Add selected user IDs
            selectedUsers.forEach(user => {
                formData.append('participants[]', user.id);
            });

            // Add title if provided
            const title = convTitleInput ? convTitleInput.value.trim() : '';
            if (title) {
                formData.append('title', title);
            }

            // Add group flag - automatically set if multiple users
            const isGroup = (isGroupCheck && isGroupCheck.checked) || selectedUsers.length > 1;
            if (isGroup) {
                formData.append('is_group', '1');
            }

            console.log('[CREATE] Creating conversation with:', {
                users: selectedUsers.map(u => u.username),
                title,
                isGroup
            });

            // Send request to create conversation
            const response = await fetch('index.php?c=chatC&a=newConversation', {
                method: 'POST',
                body: formData
            });

            if (response.redirected) {
                // Server redirected us to the new conversation
                console.log('[CREATE] Redirecting to:', response.url);
                window.location.href = response.url;
            } else {
                const text = await response.text();
                console.log('[CREATE] Response:', text);

                // Try to extract conversation ID from response
                const match = text.match(/conversation&id=(\d+)/);
                if (match) {
                    const convId = match[1];
                    console.log('[CREATE] Success! Conversation ID:', convId);
                    window.location.href = `index.php?c=chatC&a=conversation&id=${convId}`;
                } else {
                    console.log('[CREATE] Created but no ID found, reloading...');
                    window.location.reload();
                }
            }
        } catch (error) {
            console.error('[CREATE] Error:', error);
            alert('Failed to create conversation. Please try again.');

            // Re-enable button
            createConvBtn.disabled = false;
            createConvBtn.innerHTML = originalText;
        }
    });
}

// ===== INITIALIZE =====
connectWebSocket();
