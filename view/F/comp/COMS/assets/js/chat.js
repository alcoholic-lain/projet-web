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

    // ─────── THEME TOGGLE ───────
    const body = document.body;
    const toggle = document.getElementById("themeToggle");

    // Load saved theme (default = dark)
    if (localStorage.getItem("chatTheme") === "light") {
        body.classList.add("theme-light");
        body.classList.remove("theme-dark");
       // if (toggle) toggle.innerHTML = "Moon";
    } else {
        body.classList.add("theme-dark");
        body.classList.remove("theme-light");
        //if (toggle) toggle.innerHTML = "Sun";
    }

    // Click to toggle
    if (toggle) {
        toggle.addEventListener("click", () => {
            if (body.classList.contains("theme-dark")) {
                body.classList.replace("theme-dark", "theme-light");
                localStorage.setItem("chatTheme", "light");
                //toggle.innerHTML = "Moon";
            } else {
                body.classList.replace("theme-light", "theme-dark");
                localStorage.setItem("chatTheme", "dark");
                //toggle.innerHTML = "Sun";
            }
        });
    }

    // ─────── GALAXY STARS ANIMATION ───────
    const canvas = document.getElementById("galaxyCanvas");
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    let w, h;

    const resize = () => {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
    };
    resize();
    window.addEventListener("resize", resize);

    const stars = [];
    for (let i = 0; i < 400; i++) {
        stars.push({
            x: Math.random() * w,
            y: Math.random() * h,
            radius: Math.random() * 2,
            alpha: Math.random() * 0.8 + 0.2
        });
    }

    const animate = () => {
        ctx.clearRect(0, 0, w, h);
        stars.forEach(s => {
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${s.alpha})`;
            ctx.fill();

            s.alpha += (Math.random() - 0.5) * 0.05;
            s.alpha = Math.max(0.2, Math.min(1, s.alpha));
        });
        requestAnimationFrame(animate);
    };
    animate();
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
const activeTypingUsers = new Map(); // userId -> {username, timeout}

function showTypingIndicator(userId, username) {
    if (userId === CURRENT_USER_ID) return; // Don't show for current user

    // Clear existing timeout for this user
    if (activeTypingUsers.has(userId)) {
        clearTimeout(activeTypingUsers.get(userId).timeout);
    }

    // Add/update typing user
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
    const dotEl = statusEl.querySelector('.connection-dot');
    const textEl = document.getElementById('wsStatusText');
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
    console.log('[WS] Received:', data.type);
    switch(data.type) {
        case 'connected':
            console.log('[WS] Client ID:', data.clientId);
            break;
        case 'new_message':
            if (data.conversationId == currentConversationId && data.userId != CURRENT_USER_ID) {
                addMessageToUI(data);
                scrollToBottom();
                // Hide typing indicator for this user
                hideTypingIndicator(data.userId);
            }
            break;
        case 'typing':
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
    sendWSMessage({
        type: 'join',
        conversationId,
        userId: CURRENT_USER_ID,
        username: CURRENT_USERNAME
    });
    // Clear all typing indicators when joining
    activeTypingUsers.clear();
    updateTypingDisplay();
}

function leaveConversation() {
    if (currentConversationId) {
        sendWSMessage({ type: 'leave' });
        activeTypingUsers.clear();
        updateTypingDisplay();
    }
}

function sendTypingIndicator(isTyping) {
    if (currentConversationId) {
        sendWSMessage({
            type: 'typing',
            isTyping,
            conversationId: currentConversationId,
            userId: CURRENT_USER_ID,
            username: CURRENT_USERNAME
        });
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
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== MESSAGE INPUT =====
const messageInput = document.getElementById('messageInput');
const messageForm = document.getElementById('messageForm');

messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';

    if (this.value.trim()) {
        sendTypingIndicator(true);
        if (typingTimeout) clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => sendTypingIndicator(false), 2000);
    } else {
        sendTypingIndicator(false);
    }
});

messageForm.onsubmit = async (e) => {
    e.preventDefault();
    const content = messageInput.value.trim();
    if (!content || !currentConversationId) return;

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
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    //if (msgEl) msgEl.classList.add('editing');
    // i comented this cuz the behavior is wrong , it outlines the bigger container not the msg-content
}

function exitEditMode() {
    isEditMode = false;
    editingMessageId = null;
    messageInput.value = '';
    messageInput.placeholder = 'Type a message...';
    document.getElementById('editBanner').style.display = 'none';
    document.getElementById('sendBtn').innerHTML = '➤';
    document.querySelectorAll('.msg.editing').forEach(msg => msg.classList.remove('editing'));
}

document.getElementById('cancelEditBtn').onclick = exitEditMode;

// ===== DELETE MODAL =====
let currentDeleteMessageId = null;
const deleteModal = document.getElementById('deleteModal');

function openDeleteModal(msgId) {
    currentDeleteMessageId = msgId;
    deleteModal.classList.add('show');
}

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    currentDeleteMessageId = null;
}

document.getElementById('closeDeleteModal').onclick = closeDeleteModal;
document.getElementById('cancelDelete').onclick = closeDeleteModal;
document.getElementById('confirmDelete').onclick = async () => {
    if (currentDeleteMessageId) {
        await deleteMessageHTTP(currentDeleteMessageId);
        closeDeleteModal();
    }
};

// ===== LOAD CONVERSATION =====
async function loadConversation(convId, title) {
    if (currentConversationId) leaveConversation();
    currentConversationId = convId;
    document.getElementById('chatTitle').textContent = title;
    exitEditMode();
    joinConversation(convId);

    try {
        const response = await fetch(`index.php?c=chatC&a=getMessages&id=${convId}`);
        const data = await response.json();
        const messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML = '';

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

    document.getElementById('convPanel').classList.remove('show');
    document.getElementById('chatPanel').classList.add('show');
    document.getElementById('backBtn').style.display = 'block';
}

// ===== POPUP CONTROLS =====
const popup = document.getElementById('dm-popup');
const btn = document.getElementById('dm-btn');

document.getElementById('dm-btn').onclick = () => {
    popup.classList.add('show');
    btn.style.display = 'none';
};

document.getElementById('closeBtn').onclick = () => {
    popup.classList.remove('show');
    btn.style.display = 'flex';
    if (currentConversationId) leaveConversation();
};

document.getElementById('minimizeBtn').onclick = () => popup.classList.toggle('minimized');
document.getElementById('maximizeBtn').onclick = () => popup.classList.toggle('maximized');

document.getElementById('backBtn').onclick = () => {
    if (currentConversationId) leaveConversation();
    currentConversationId = null;
    exitEditMode();
    document.getElementById('chatPanel').classList.remove('show');
    document.getElementById('convPanel').classList.add('show');
    document.getElementById('backBtn').style.display = 'none';
    document.getElementById('chatTitle').textContent = 'Messages';
};

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

newConvBtn.addEventListener('click', () => {
    convPanel.classList.remove('show');
    newConvPanel.classList.add('show');
    document.getElementById('backBtn').style.display = 'none';
});

backToConvBtn.addEventListener('click', () => {
    newConvPanel.classList.remove('show');
    convPanel.classList.add('show');
    resetNewConvPanel();
});

async function searchUsers() {
    const query = userSearchInput.value.trim();

    if (query.length < 2) {
        searchResults.innerHTML = '<p style="padding:15px;opacity:0.6;text-align:center;">Type at least 2 characters to search</p>';
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

searchUsersBtn.addEventListener('click', searchUsers);
userSearchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchUsers();
    }
});

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
        selectedUsersDiv.style.display = 'none';
        createConvBtn.disabled = true;
    } else {
        selectedUsersDiv.style.display = 'block';
        createConvBtn.disabled = false;

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

createConvBtn.addEventListener('click', async () => {
    if (selectedUsers.length === 0) return;

    const title = convTitleInput.value.trim();
    const isGroup = isGroupCheck.checked || selectedUsers.length > 1;

    createConvBtn.disabled = true;
    createConvBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

    try {
        const formData = new FormData();
        formData.append('mode', 'create');
        formData.append('title', title);
        if (isGroup) formData.append('is_group', '1');
        selectedUsers.forEach(user => {
            formData.append('participants[]', user.id);
        });

        const response = await fetch('index.php?c=chatC&a=newConversation', {
            method: 'POST',
            body: formData
        });

        if (response.redirected) {
            window.location.href = response.url;
        } else {
            throw new Error('Failed to create conversation');
        }
    } catch (error) {
        console.error('Error creating conversation:', error);
        alert('Failed to create conversation. Please try again.');
        createConvBtn.disabled = false;
        createConvBtn.innerHTML = 'Create Conversation';
    }
});

function resetNewConvPanel() {
    selectedUsers = [];
    userSearchInput.value = '';
    convTitleInput.value = '';
    isGroupCheck.checked = false;
    searchResults.innerHTML = '';
    updateSelectedUsers();
}

// ===== INITIALIZE =====
connectWebSocket();

// Stars Animation
const canvas = document.getElementById('galaxyCanvas');
const ctx = canvas.getContext('2d');
canvas.width = innerWidth;
canvas.height = innerHeight;
const stars = Array.from({length: 600}, () => ({
    x: Math.random()*canvas.width, y: Math.random()*canvas.height,
    r: Math.random()*1.8+0.5, a: Math.random(), s: Math.random()*0.7+0.2
}));
function drawStars() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    stars.forEach(s => {
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI*2);
        ctx.fillStyle = `rgba(255,255,255,${s.a})`;
        ctx.fill();
        s.x += s.s;
        if (s.x > canvas.width) s.x = 0;
    });
    requestAnimationFrame(drawStars);
}
drawStars();
window.addEventListener('resize', () => {
    canvas.width = innerWidth;
    canvas.height = innerHeight;
});

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
themeToggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    themeToggle.textContent = document.documentElement.classList.contains('dark') ? '🌙' : '☀️';
});




