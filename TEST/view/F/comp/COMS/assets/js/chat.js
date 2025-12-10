// view/F/assets/js/chat.js - PART 1: Core & WebSocket

document.addEventListener("DOMContentLoaded", function () {
    const msgBox = document.getElementById("messages");
    if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;

    const popup = document.getElementById('dm-popup');
    const dmBtn = document.getElementById('dm-btn');
    const closeBtn = document.getElementById('closeBtn');
    const maximizeBtn = document.getElementById('maximizeBtn');

    if (dmBtn) dmBtn.addEventListener('click', () => { if (popup) { popup.classList.add('show'); dmBtn.style.display = 'none'; } });
    if (closeBtn) closeBtn.addEventListener('click', () => { if (popup) { popup.classList.remove('show'); dmBtn.style.display = 'flex'; if (currentConversationId) leaveConversation(); } });
    if (maximizeBtn) maximizeBtn.addEventListener('click', () => { if (popup) popup.classList.toggle('maximized'); });

    // Galaxy animation
    const canvas = document.getElementById("galaxyCanvas");
    if (canvas) {
        const ctx = canvas.getContext("2d");
        let w, h;
        const resize = () => { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; };
        resize();
        window.addEventListener("resize", resize);
        const stars = [];
        for (let i = 0; i < 600; i++) stars.push({ x: Math.random() * w, y: Math.random() * h, radius: Math.random() * 1.8 + 0.5, alpha: Math.random() * 0.8 + 0.2, speed: Math.random() * 0.7 + 0.2 });
        function drawStars() {
            ctx.clearRect(0, 0, w, h);
            const isDark = document.documentElement.classList.contains('dark');
            stars.forEach(s => { ctx.beginPath(); ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2); ctx.fillStyle = `rgba(255, 255, 255, ${isDark ? s.alpha : s.alpha * 0.4})`; ctx.fill(); s.alpha += (Math.random() - 0.5) * 0.05; s.alpha = Math.max(0.2, Math.min(1, s.alpha)); s.x += s.speed; if (s.x > w) s.x = 0; });
            requestAnimationFrame(drawStars);
        }
        drawStars();
    }
});

let ws = null, wsReconnectAttempts = 0, wsReconnectTimeout = null, typingTimeout = null;
let currentConversationId = null, isEditMode = false, editingMessageId = null;
let replyingToMessageId = null, replyingToContent = null, messagesCache = new Map();
const activeTypingUsers = new Map();

function showTypingIndicator(userId, username) {
    if (userId === CURRENT_USER_ID) return;
    if (activeTypingUsers.has(userId)) clearTimeout(activeTypingUsers.get(userId).timeout);
    const timeout = setTimeout(() => { activeTypingUsers.delete(userId); updateTypingDisplay(); }, 3000);
    activeTypingUsers.set(userId, { username, timeout });
    updateTypingDisplay();
}

function hideTypingIndicator(userId) { if (activeTypingUsers.has(userId)) { clearTimeout(activeTypingUsers.get(userId).timeout); activeTypingUsers.delete(userId); updateTypingDisplay(); } }

function updateTypingDisplay() {
    const indicator = document.getElementById('typingIndicator'), text = document.getElementById('typingText');
    if (!indicator || !text) return;
    if (activeTypingUsers.size === 0) { indicator.classList.remove('show'); return; }
    const usernames = Array.from(activeTypingUsers.values()).map(u => u.username);
    if (usernames.length === 1) text.textContent = `${usernames[0]} is typing...`;
    else if (usernames.length === 2) text.textContent = `${usernames[0]} and ${usernames[1]} are typing...`;
    else text.textContent = `${usernames[0]} and ${usernames.length - 1} others are typing...`;
    indicator.classList.add('show');
}

function connectWebSocket() {
    try {
        ws = new WebSocket(WS_URL);
        ws.onopen = () => { console.log('[WS] ✅ Connected'); wsReconnectAttempts = 0; updateConnectionStatus('connected'); if (currentConversationId) joinConversation(currentConversationId); };
        ws.onmessage = (event) => { try { handleWebSocketMessage(JSON.parse(event.data)); } catch (e) { console.error('[WS] Parse error:', e); } };
        ws.onclose = () => { console.log('[WS] ⚠️ Disconnected'); updateConnectionStatus('disconnected'); attemptReconnect(); };
        ws.onerror = (error) => { console.error('[WS] ❌ Error:', error); updateConnectionStatus('disconnected'); };
    } catch (error) { console.error('[WS] Connection failed:', error); updateConnectionStatus('disconnected'); attemptReconnect(); }
}

function attemptReconnect() {
    if (wsReconnectAttempts >= 10) { console.log('[WS] Max reconnection attempts reached'); return; }
    wsReconnectAttempts++;
    const delay = Math.min(1000 * Math.pow(2, wsReconnectAttempts), 30000);
    console.log(`[WS] Reconnecting in ${delay/1000}s (attempt ${wsReconnectAttempts})`);
    updateConnectionStatus('connecting');
    wsReconnectTimeout = setTimeout(connectWebSocket, delay);
}

function updateConnectionStatus(status) {
    const statusEl = document.getElementById('wsStatus');
    if (!statusEl) return;
    const dotEl = statusEl.querySelector('.connection-dot'), textEl = document.getElementById('wsStatusText');
    if (!dotEl || !textEl) return;
    dotEl.className = 'connection-dot';
    switch(status) {
        case 'connected': dotEl.classList.add('connected'); textEl.textContent = 'Live'; break;
        case 'connecting': dotEl.classList.add('connecting'); textEl.textContent = 'Connecting...'; break;
        case 'disconnected': dotEl.classList.add('disconnected'); textEl.textContent = 'Offline'; break;
    }
}

function sendWSMessage(data) { if (ws && ws.readyState === WebSocket.OPEN) { ws.send(JSON.stringify(data)); return true; } console.warn('[WS] Cannot send, not connected'); return false; }

function handleWebSocketMessage(data) {
    console.log('[WS] Received:', data.type, data);
    switch(data.type) {
        case 'connected': console.log('[WS] Client ID:', data.clientId); break;
        case 'new_message': if (data.conversationId == currentConversationId && data.userId != CURRENT_USER_ID) { addMessageToUI(data); scrollToBottom(); hideTypingIndicator(data.userId); } break;
        case 'typing': if (data.conversationId == currentConversationId) { if (data.isTyping) showTypingIndicator(data.userId, data.username); else hideTypingIndicator(data.userId); } break;
        case 'message_edited': if (data.conversationId == currentConversationId) updateMessageInUI(data.messageId, data.content); break;
        case 'message_deleted': if (data.conversationId == currentConversationId) removeMessageFromUI(data.messageId); break;
        case 'reaction_updated': if (data.conversationId == currentConversationId) { console.log('[WS] Updating reactions:', data.messageId, data.reactions); updateReactionsInUI(data.messageId, data.reactions); } break;
    }
}

function joinConversation(conversationId) { console.log('🚪 Joining:', conversationId); sendWSMessage({ type: 'join', conversationId, userId: CURRENT_USER_ID, username: CURRENT_USERNAME }); activeTypingUsers.clear(); updateTypingDisplay(); }
function leaveConversation() { if (currentConversationId) { console.log('👋 Leaving:', currentConversationId); sendWSMessage({ type: 'leave' }); activeTypingUsers.clear(); updateTypingDisplay(); } }
function sendTypingIndicator(isTyping) { if (!currentConversationId) return; sendWSMessage({ type: 'typing', isTyping, conversationId: currentConversationId, userId: CURRENT_USER_ID, username: CURRENT_USERNAME }); }

// See PART 2 for emoji picker, reactions, and UI functions
// view/F/assets/js/chat.js - PART 2: Emoji Picker, Reactions & UI
// Add this after PART 1

// ===== ENHANCED EMOJI PICKER (INSTAGRAM-STYLE) =====

const EMOJI_CATEGORIES = {
    ':D': ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🤧','🥵','🥶','🥴','😵','🤯','🤠','🥳','🥸','😎','🤓','🧐'],
    ':<': ['😕','😟','🙁','☹️','😮','😯','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
    ':3': ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏'],
    '<3': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','♥️'],
    '☘︎': ['✨','⭐','🌟','💫','✔️','❌','❗','❓','⚠️','🔥','💯','💢','💥','💨','💦','💤','🎉','🎊','🎈','🎁','🏆','🥇','🥈','🥉','⚽','🏀','🏈','⚾','🎾','🏐','🏉','🎱','🏓','🏸','🥅','🎯'],
    '꩜': ['💎','👑','🎓','🎭','🎨','🎬','🎤','🎧','🎼','🎹','🥁','🎷','🎺','🎸','🪕','🎻','📱','💻','⌨️','🖥️','🖨️','🖱️','💿','📀','📷','📹','🎥','📞','☎️','📟','📠','📺','📻','🔔','⏰','⏱️','⏲️','⏳','📡'],
    '✿': ['🌍','🌎','🌏','🌐','🗺️','🏔️','⛰️','🌋','🗻','🏕️','🏖️','🏜️','🏝️','🏞️','☀️','🌤️','⛅','🌥️','☁️','🌦️','🌧️','⛈️','🌩️','🌨️','❄️','☃️','⛄','🌬️','💨','🌪️','🌫️','🌈','☔','💧','💦','⚡','🔥','✨','🌟','💫','⭐','🌙','☄️'],
    '☕︎': ['🍎','🍊','🍋','🍌','🍉','🍇','🍓','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶️','🌽','🥕','🧄','🧅','🥔','🍠','🥐','🥯','🍞','🥖','🥨','🧀','🥚','🍳','🧈','🥞','🧇','🥓','🥩','🍗','🍖','🦴','🌭','🍔','🍟','🍕','🥪','🥙','🧆','🌮','🌯','🥗','🥘','🥫','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🦪','🍤','🍙','🍚','🍘','🍥','🥠','🥮','🍢','🍡','🍧','🍨','🍦','🥧','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍿','🍩','🍪','🌰','🥜','🍯']
};

const QUICK_REACTIONS = ['❤️','👍','😂','💀','😢','🔥','🎉'];

let currentEmojiPicker = null;
let lastClickTime = 0;
let lastClickedMessageId = null;

function createEmojiPicker(messageId, buttonElement) {
    closeAllEmojiPickers();

    const picker = document.createElement('div');
    picker.className = 'emoji-picker-full';
    picker.id = `emoji-picker-${messageId}`;

    // Quick reactions bar
    const quickBar = document.createElement('div');
    quickBar.className = 'emoji-quick-bar';
    QUICK_REACTIONS.forEach(emoji => {
        const btn = document.createElement('button');
        btn.className = 'emoji-quick-btn';
        btn.textContent = emoji;
        btn.onclick = (e) => { e.stopPropagation(); toggleReaction(messageId, emoji); closeAllEmojiPickers(); };
        quickBar.appendChild(btn);
    });
    picker.appendChild(quickBar);

    // Category tabs
    const tabs = document.createElement('div');
    tabs.className = 'emoji-tabs';
    const categoryNames = Object.keys(EMOJI_CATEGORIES);
    categoryNames.forEach((cat, index) => {
        const tab = document.createElement('button');
        tab.className = 'emoji-tab' + (index === 0 ? ' active' : '');
        tab.textContent = cat;
        tab.onclick = (e) => {
            e.stopPropagation();
            document.querySelectorAll('.emoji-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            showEmojiCategory(cat, emojiGrid, messageId);
        };
        tabs.appendChild(tab);
    });
    picker.appendChild(tabs);

    // Emoji grid
    const emojiGrid = document.createElement('div');
    emojiGrid.className = 'emoji-grid';
    picker.appendChild(emojiGrid);

    // Show first category
    showEmojiCategory(categoryNames[0], emojiGrid, messageId);

    // Position picker
    // lain wrote this code section btw :D
    const msgElement = buttonElement.closest('.msg');
    const messageRect = msgElement.getBoundingClientRect();
    const dmPopup = document.getElementById('dm-popup');
    dmPopupRect = dmPopup.getBoundingClientRect();
    const messageCenterY = messageRect.y + (messageRect.height / 2);
    const dmPopupCenterY = dmPopupRect.y + (dmPopupRect.height / 2);

    if (msgElement) {
        msgElement.appendChild(picker);
        currentEmojiPicker = picker;
    }
    if (messageCenterY > dmPopupCenterY) {

    // stick it to the bottom
        picker.style.bottom = '5%';
    }else {
    // stick it to the top
        picker.style.top = '0%';
    }








    if (messageCenterY > dmPopupCenterY) {

        picker.style.top = 'auto';
        picker.style.marginBottom = '10px';
        picker.style.marginTop = 'auto';

    } else {


        picker.style.bottom = 'auto';
        picker.style.marginTop = '10px';
        picker.style.marginBottom = 'auto';
    }










    // Close on outside click
    setTimeout(() => { document.addEventListener('click', closeAllEmojiPickers, { once: true }); }, 0);
}

function showEmojiCategory(category, container, messageId) {
    container.innerHTML = '';
    const emojis = EMOJI_CATEGORIES[category] || [];
    emojis.forEach(emoji => {
        const btn = document.createElement('button');
        btn.className = 'emoji-grid-btn';
        btn.textContent = emoji;
        btn.onclick = (e) => { e.stopPropagation(); toggleReaction(messageId, emoji); closeAllEmojiPickers(); };
        container.appendChild(btn);
    });
}

function closeAllEmojiPickers() {
    document.querySelectorAll('.emoji-picker-full').forEach(p => p.remove());
    currentEmojiPicker = null;
}

// Double-click to heart
function handleMessageDoubleClick(messageId, event) {
    const now = Date.now();
    if (lastClickedMessageId === messageId && now - lastClickTime < 500) {
        event.preventDefault();
        toggleReaction(messageId, '❤️');
        showHeartAnimation(event.target);
    }
    lastClickTime = now;
    lastClickedMessageId = messageId;
}

function showHeartAnimation(element) {
    const heart = document.createElement('div');
    heart.className = 'heart-animation';
    heart.textContent = '❤️';
    const rect = element.getBoundingClientRect();
    heart.style.left = rect.left + rect.width / 2 + 'px';
    heart.style.top = rect.top + rect.height / 2 + 'px';
    document.body.appendChild(heart);
    setTimeout(() => heart.remove(), 1000);
}

async function toggleReaction(messageId, emoji) {
    console.log('[REACTION] Toggling:', messageId, emoji);

    // Optimistic UI update
    const currentReactions = getCurrentReactions(messageId);
    const userIdStr = String(CURRENT_USER_ID);

    if (!currentReactions[emoji]) currentReactions[emoji] = [];

    const index = currentReactions[emoji].indexOf(userIdStr);
    if (index !== -1) {
        currentReactions[emoji].splice(index, 1);
        if (currentReactions[emoji].length === 0) delete currentReactions[emoji];
    } else {
        currentReactions[emoji].push(userIdStr);
    }

    // Update UI immediately
    updateReactionsInUI(messageId, currentReactions);

    // Broadcast via WebSocket immediately
    console.log('[REACTION] Broadcasting via WS');
    sendWSMessage({
        type: 'reaction_update',
        messageId,
        reactions: currentReactions,
        conversationId: currentConversationId
    });

    // Persist to database
    try {
        const formData = new FormData();
        formData.append('message_id', messageId);
        formData.append('emoji', emoji);
        const response = await fetch('index.php?c=chatC&a=toggleReaction', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            console.log('[REACTION] DB updated');
            updateReactionsInUI(messageId, data.reactions);
        }
    } catch (error) {
        console.error('Error toggling reaction:', error);
    }
}

function getCurrentReactions(messageId) {
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (!msgEl) return {};
    const reactionsContainer = msgEl.querySelector('.msg-reactions');
    if (!reactionsContainer) return {};
    const reactions = {};
    reactionsContainer.querySelectorAll('.reaction-bubble').forEach(bubble => {
        const emoji = bubble.querySelector('.reaction-emoji').textContent;
        const hasUserReacted = bubble.classList.contains('user-reacted');
        reactions[emoji] = reactions[emoji] || [];
        if (hasUserReacted) reactions[emoji].push(String(CURRENT_USER_ID));
    });
    return reactions;
}

function updateReactionsInUI(messageId, reactions) {
    console.log('[UI] Updating reactions:', messageId, reactions);
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (!msgEl) { console.warn('[UI] Message not found:', messageId); return; }

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
            const userIdStr = String(CURRENT_USER_ID);
            if (userIds.includes(userIdStr) || userIds.includes(CURRENT_USER_ID)) {
                reactionBtn.classList.add('user-reacted');
            }
            reactionBtn.innerHTML = `<span class="reaction-emoji">${emoji}</span><span class="reaction-count">${userIds.length}</span>`;
            reactionBtn.onclick = (e) => { e.stopPropagation(); toggleReaction(messageId, emoji); };
            reactionsContainer.appendChild(reactionBtn);
        });
    }
}

// ===== REPLY FUNCTIONS =====

function enterReplyMode(messageId, content, username) {
    replyingToMessageId = messageId;
    replyingToContent = content;
    let replyBanner = document.getElementById('replyBanner');
    if (!replyBanner) replyBanner = createEnhancedReplyBanner();
    const replyToUser = document.getElementById('replyToUser');
    const replyPreviewText = document.getElementById('replyPreviewText');
    if (replyToUser) replyToUser.textContent = `Replying to ${username}`;
    if (replyPreviewText) {
        const truncated = content.length > 50 ? content.substring(0, 50) + '...' : content;
        replyPreviewText.textContent = truncated;
    }
    if (replyBanner) replyBanner.classList.add('active');
    const messageInput = document.getElementById('messageInput');
    if (messageInput) messageInput.focus();
}

function exitReplyMode() {
    replyingToMessageId = null;
    replyingToContent = null;
    const banner = document.getElementById('replyBanner');
    if (banner) banner.classList.remove('active');
}

function createEnhancedReplyBanner() {
    const existingBanner = document.getElementById('replyBanner');
    if (existingBanner) return existingBanner;
    const banner = document.createElement('div');
    banner.id = 'replyBanner';
    banner.className = 'reply-banner';
    banner.innerHTML = `
        <div class="reply-banner-content">
            <div class="reply-icon">↩</div>
            <div class="reply-info">
                <div class="reply-to-user" id="replyToUser">Replying to...</div>
                <div class="reply-preview-text" id="replyPreviewText"></div>
            </div>
            <button id="cancelReplyBtn" class="cancel-reply-btn">
                <i class="fas fa-times"></i><span>Cancel</span>
            </button>
        </div>
    `;
    const chatPanel = document.getElementById('chatPanel');
    const inputBar = chatPanel.querySelector('.input-bar');
    chatPanel.insertBefore(banner, inputBar);
    document.getElementById('cancelReplyBtn').onclick = exitReplyMode;
    return banner;
}

function scrollToMessage(messageId) {
    const targetMsg = document.querySelector(`[data-message-id="${messageId}"]`);
    if (targetMsg) {
        targetMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        targetMsg.classList.add('highlight');
        setTimeout(() => targetMsg.classList.remove('highlight'), 1000);
    }
}
window.scrollToMessage = scrollToMessage;

// Continue with rest of chat.js functions...
// (addMessageToUI, updateMessageInUI, input handlers, etc.)
// PART 3: UI Functions - Add message display and interactions

function addMessageToUI(data) {
    const messagesDiv = document.getElementById('messages');
    const isOwn = data.userId == CURRENT_USER_ID;
    const msgDiv = document.createElement('div');
    msgDiv.className = `msg ${isOwn ? 'sent' : 'received'}`;
    msgDiv.dataset.messageId = data.messageId;

    // Store in cache
    messagesCache.set(String(data.messageId), {
        content: data.content,
        username: data.username,
        userId: data.userId
    });

    const time = data.timestamp ? new Date(data.timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '';
    const content = escapeHtml(data.content).replace(/\n/g, '<br>');

    // Create reply preview if this is a reply
    let replyPreview = '';
    if (data.replyToId) {
        const replyTo = messagesCache.get(String(data.replyToId));
        if (replyTo) {
            const replyContent = replyTo.content.length > 50 ? replyTo.content.substring(0, 50) + '...' : replyTo.content;
            replyPreview = `
                <div class="reply-reference" onclick="scrollToMessage('${data.replyToId}')">
                    <div class="reply-reference-header">
                        <span>↩</span>
                        <span>${escapeHtml(replyTo.username)}</span>
                    </div>
                    <div class="reply-reference-content">${escapeHtml(replyContent)}</div>
                </div>
            `;
        }
    }

    // Build message bubble
    msgDiv.innerHTML = `
        <div class="msg-content">
            ${replyPreview}
            <div class="msg-text">${content}</div>
            <small>${escapeHtml(data.username)} • ${time}</small>
            <div class="msg-reactions"></div>
        </div>
    `;

    // Actions wrapper
    const actionsWrapper = document.createElement('div');
    actionsWrapper.className = 'msg-actions-wrapper';
    actionsWrapper.innerHTML = `
        <div class="msg-actions">
            <button class="msg-action-btn reply-btn" data-id="${data.messageId}" title="Reply">
                <i class="fas fa-reply"></i>
            </button>
            <button class="msg-action-btn react-btn" data-id="${data.messageId}" title="React">
                <i class="fas fa-smile"></i>
            </button>
            ${isOwn ? `
            <button class="msg-action-btn edit-btn" data-id="${data.messageId}" data-content="${escapeHtml(data.content)}" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="msg-action-btn delete-btn" data-id="${data.messageId}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
            ` : ''}
        </div>
    `;
    msgDiv.appendChild(actionsWrapper);

    // Reply button
    const replyBtn = actionsWrapper.querySelector('.reply-btn');
    replyBtn.onclick = (e) => { e.stopPropagation(); enterReplyMode(data.messageId, data.content, data.username); };

    // React button - open full emoji picker
    const reactBtn = actionsWrapper.querySelector('.react-btn');
    reactBtn.onclick = (e) => { e.stopPropagation(); createEmojiPicker(data.messageId, reactBtn); };

    // Double-click to heart
    msgDiv.onclick = (e) => handleMessageDoubleClick(data.messageId, e);

    if (isOwn) {
        actionsWrapper.querySelector('.edit-btn').onclick = (e) => { e.stopPropagation(); enterEditMode(data.messageId, data.content); };
        actionsWrapper.querySelector('.delete-btn').onclick = (e) => { e.stopPropagation(); openDeleteModal(data.messageId); };
    }

    messagesDiv.appendChild(msgDiv);

    if (data.reactions) updateReactionsInUI(data.messageId, data.reactions);

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
        if (messagesCache.has(messageId)) {
            messagesCache.get(messageId).content = newContent;
        }
    }
}

function removeMessageFromUI(messageId) {
    const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (msgEl) {
        msgEl.style.opacity = '0';
        msgEl.style.transform = 'scale(0.8)';
        setTimeout(() => { msgEl.remove(); messagesCache.delete(messageId); }, 300);
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
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';

        if (isEditMode || !currentConversationId) return;

        const hasContent = this.value.trim().length > 0;

        if (hasContent) {
            if (!isCurrentlyTyping) {
                sendTypingIndicator(true);
                isCurrentlyTyping = true;
            }
            if (typingTimeout) clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                sendTypingIndicator(false);
                isCurrentlyTyping = false;
            }, 2000);
        } else {
            if (isCurrentlyTyping) {
                if (typingTimeout) clearTimeout(typingTimeout);
                sendTypingIndicator(false);
                isCurrentlyTyping = false;
            }
        }
    });

    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() !== '') {
                if (isCurrentlyTyping) {
                    if (typingTimeout) clearTimeout(typingTimeout);
                    sendTypingIndicator(false);
                    isCurrentlyTyping = false;
                }
                messageForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        } else if (e.key === 'Escape') {
            if (replyingToMessageId) exitReplyMode();
            else if (isEditMode) exitEditMode();
        }
    });

    messageInput.addEventListener('blur', function() {
        if (isCurrentlyTyping) {
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

        if (typingTimeout) clearTimeout(typingTimeout);
        sendTypingIndicator(false);

        if (isEditMode) {
            await editMessageHTTP(editingMessageId, content);
            exitEditMode();
        } else {
            const tempMsgId = 'temp_' + Date.now();
            const messageData = {
                messageId: tempMsgId,
                userId: CURRENT_USER_ID,
                username: CURRENT_USERNAME,
                content,
                conversationId: currentConversationId,
                timestamp: new Date().toISOString(),
                replyToId: replyingToMessageId
            };

            addMessageToUI(messageData);

            sendWSMessage({
                type: 'message',
                messageId: tempMsgId,
                content,
                replyToId: replyingToMessageId
            });

            await saveMessageHTTP(content, replyingToMessageId);
            exitReplyMode();
            scrollToBottom();
        }

        messageInput.value = '';
        messageInput.style.height = 'auto';
    };
}

async function saveMessageHTTP(content, replyToId = null) {
    try {
        const formData = new FormData();
        formData.append('mode', 'send');
        formData.append('content', content);
        if (replyToId) formData.append('reply_to_id', replyToId);
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
    exitReplyMode();
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
if (cancelEditBtn) cancelEditBtn.onclick = exitEditMode;

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
    messagesCache.clear();

    const chatTitle = document.getElementById('chatTitle');
    const popupAvatar = document.getElementById('popup-avatar');

    if (chatTitle) chatTitle.textContent = title;
    if (popupAvatar) {
        popupAvatar.textContent = title.substring(0, 2).toUpperCase();
        popupAvatar.style.display = 'flex';
    }

    exitEditMode();
    exitReplyMode();
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
                    timestamp: msg.created_at,
                    reactions: msg.reactions,
                    replyToId: msg.reply_to_id
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
        if (popupAvatar) popupAvatar.style.display = 'none';
    };
}

document.querySelectorAll('.conv-item').forEach(item => {
    item.onclick = () => loadConversation(item.dataset.id, item.querySelector('.name').textContent);
});

// ===== CONVERSATION SEARCH =====
const searchConvInput = document.getElementById('searchConvInput');
const convList = document.getElementById('convList');

if (searchConvInput && convList) {
    searchConvInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const convItems = convList.querySelectorAll('.conv-item');
        convItems.forEach(item => {
            const name = item.dataset.name || '';
            item.style.display = name.includes(query) ? 'flex' : 'none';
        });
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
            if (noResultsMsg) noResultsMsg.remove();
        }
    });
}

// ===== INITIALIZE =====
connectWebSocket();