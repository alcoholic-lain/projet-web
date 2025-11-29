<?php // view/F/COMS/index.php - COSMIC CHAT WITH INLINE EDIT ?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuniSpace • Chat</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="path/to/style.css">
</head>
<body class="dark">

<!-- Stars + Aurora -->
<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<!-- Header -->
<header>
    <h1>TuniSpace</h1>
    <div style="display:flex;align-items:center;gap:20px">
        <span id="themeToggle" style="font-size:1.7rem;cursor:pointer">🌙</span>
        <span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                <small class="badge bg-secondary"><?= $_SESSION['role'] ?? 'front' ?></small>
            <?php else: ?>Guest<?php endif; ?>
        </span>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?c=chatC&a=logout" style="color:#ff6b6b">Logout</a>
        <?php endif; ?>
    </div>
</header>

<!-- Floating Chat Button -->
<div id="dm-btn">💬</div>

<!-- Floating Messenger Popup -->
<div id="dm-popup">
    <div class="chat-header">
        <div class="chat-header-left">
            <button id="backBtn">←</button>
            <div id="popup-avatar" class="avatar">TS</div>
            <div class="chat-info">
                <h3 id="chatTitle">Messages</h3>
                <p id="chatSubtitle"></p>
            </div>
        </div>
        <div class="header-btns">
            <button id="minimizeBtn">−</button>
            <button id="maximizeBtn">□</button>
            <button id="closeBtn">✕</button>
        </div>
    </div>

    <div class="popup-body">
        <!-- Conversation List -->
        <div class="conv-panel show" id="convPanel">
            <div class="search">
                <input type="text" id="searchConvInput" placeholder="Search conversations...">
                <button id="newConvBtn" class="new-conv-btn" title="New Conversation">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="conv-list">
                <?php foreach ($conversations as $c):
                    $title = $c['display_title'] ?? $c['title'] ?? 'Chat';
                    ?>
                    <div class="conv-item" data-id="<?= $c['id'] ?>">
                        <div class="avatar"><?= strtoupper(substr($title,0,2)) ?></div>
                        <div>
                            <div class="name"><?= htmlspecialchars($title) ?></div>
                            <div style="font-size:12.5px;opacity:.8"><?= $c['is_group'] ? 'Group' : 'Direct' ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- New Conversation Panel -->
        <div class="conv-panel" id="newConvPanel">
            <div class="new-conv-header">
                <button id="backToConvBtn">←</button>
                <h3>New Conversation</h3>
            </div>

            <div class="user-search-box">
                <input type="text" id="userSearchInput" placeholder="Search users by name or email...">
                <button id="searchUsersBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <div id="searchResults" class="search-results"></div>

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
                <button id="createConvBtn" class="btn-create-conv" disabled>
                    Create Conversation
                </button>
            </div>
        </div>

        <!-- Active Chat -->
        <div class="chat-panel" id="chatPanel">
            <div id="messages"></div>

            <!-- Edit Mode Banner -->
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
                <input type="hidden" name="mode" value="send" id="messageMode">
                <input type="hidden" name="message_id" id="messageId">
                <input type="hidden" name="conversation_id" id="currentConvId">
                <textarea name="content" id="messageInput" placeholder="Type a message..." required></textarea>
                <button type="submit" id="sendBtn">➤</button>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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

<script>
    // Stars Animation
    const canvas = document.getElementById('galaxyCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = innerWidth;
    canvas.height = innerHeight;

    const stars = Array.from({length: 600}, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 1.8 + 0.5,
        a: Math.random(),
        s: Math.random() * 0.7 + 0.2
    }));

    function drawStars() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        stars.forEach(s => {
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
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

    // Popup Controls
    const popup = document.getElementById('dm-popup');
    const btn = document.getElementById('dm-btn');
    const closeBtn = document.getElementById('closeBtn');
    const minBtn = document.getElementById('minimizeBtn');
    const maxBtn = document.getElementById('maximizeBtn');
    const backBtn = document.getElementById('backBtn');
    const convPanel = document.getElementById('convPanel');
    const chatPanel = document.getElementById('chatPanel');
    const messagesContainer = document.getElementById('messages');
    const messageForm = document.getElementById('messageForm');
    const currentConvIdInput = document.getElementById('currentConvId');
    const messageInput = document.getElementById('messageInput');
    const messageMode = document.getElementById('messageMode');
    const messageId = document.getElementById('messageId');
    const sendBtn = document.getElementById('sendBtn');
    const editBanner = document.getElementById('editBanner');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    // New Conversation Elements
    const newConvBtn = document.getElementById('newConvBtn');
    const newConvPanel = document.getElementById('newConvPanel');
    const backToConvBtn = document.getElementById('backToConvBtn');
    const userSearchInput = document.getElementById('userSearchInput');
    const searchUsersBtn = document.getElementById('searchUsersBtn');
    const searchResults = document.getElementById('searchResults');
    const selectedUsersDiv = document.getElementById('selectedUsers');
    const selectedUsersList = document.getElementById('selectedUsersList');
    const createConvBtn = document.getElementById('createConvBtn');
    const isGroupCheck = document.getElementById('isGroupCheck');
    const convTitleInput = document.getElementById('convTitleInput');

    // Modal Elements
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');

    let activeConversationId = null;
    let currentDeleteMessageId = null;
    let isEditMode = false;
    let selectedUsers = new Set();

    btn.onclick = () => {
        popup.classList.add('show');
        btn.style.display = 'none';
    };

    closeBtn.onclick = () => {
        popup.classList.remove('show');
        btn.style.display = 'flex';
    };

    minBtn.onclick = () => popup.classList.toggle('minimized');
    maxBtn.onclick = () => popup.classList.toggle('maximized');

    // Edit Mode Functions
    function enterEditMode(msgId, content) {
        isEditMode = true;
        messageMode.value = 'edit';
        messageId.value = msgId;
        messageInput.value = content;
        messageInput.placeholder = 'Edit your message...';
        editBanner.style.display = 'flex';
        sendBtn.innerHTML = '<i class="fas fa-check"></i>';
        messageInput.focus();

        // Scroll to the message being edited
        const msgElement = document.querySelector(`[data-message-id="${msgId}"]`);
        if (msgElement) {
            msgElement.classList.add('editing');
        }
    }

    function exitEditMode() {
        isEditMode = false;
        messageMode.value = 'send';
        messageId.value = '';
        messageInput.value = '';
        messageInput.placeholder = 'Type a message...';
        editBanner.style.display = 'none';
        sendBtn.innerHTML = '➤';

        // Remove editing highlight
        document.querySelectorAll('.msg.editing').forEach(msg => {
            msg.classList.remove('editing');
        });
    }

    cancelEditBtn.onclick = exitEditMode;

    // Delete Modal Functions
    function openDeleteModal(msgId) {
        currentDeleteMessageId = msgId;
        deleteModal.classList.add('show');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('show');
        currentDeleteMessageId = null;
    }

    closeDeleteModalBtn.onclick = closeDeleteModal;
    cancelDeleteBtn.onclick = closeDeleteModal;

    // Close modal on background click
    window.onclick = (e) => {
        if (e.target === deleteModal) closeDeleteModal();
    };

    // Confirm Delete
    confirmDeleteBtn.onclick = async () => {
        if (!currentDeleteMessageId) return;

        try {
            const formData = new FormData();
            formData.append('mode', 'delete');
            formData.append('message_id', currentDeleteMessageId);

            const response = await fetch(`index.php?c=chatC&a=conversation&id=${activeConversationId}`, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                closeDeleteModal();
                const title = document.getElementById('chatTitle').textContent;
                loadConversation(activeConversationId, title);
            }
        } catch (error) {
            console.error('Error deleting message:', error);
        }
    };

    // Load conversation messages
    async function loadConversation(convId, title) {
        activeConversationId = convId;
        currentConvIdInput.value = convId;
        document.getElementById('chatTitle').textContent = title;
        exitEditMode(); // Exit edit mode when switching conversations

        try {
            const response = await fetch(`index.php?c=chatC&a=getMessages&id=${convId}`);
            const data = await response.json();

            messagesContainer.innerHTML = '';

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const isOwn = msg.user_id == <?= $_SESSION['user_id'] ?? 0 ?>;
                    const msgDiv = document.createElement('div');
                    msgDiv.className = `msg ${isOwn ? 'sent' : 'received'}`;
                    msgDiv.dataset.messageId = msg.id;

                    msgDiv.innerHTML = `
                        <div class="msg-content">
                            <div class="msg-text">${msg.content.replace(/\n/g, '<br>')}</div>
                            <small>${msg.username} • ${msg.created_at}</small>
                        </div>
                    `;

                    // Create action buttons wrapper outside message
                    if (isOwn) {
                        const actionsWrapper = document.createElement('div');
                        actionsWrapper.className = 'msg-actions-wrapper';
                        actionsWrapper.innerHTML = `
                            <div class="msg-actions">
                                <button class="msg-action-btn edit-btn" data-id="${msg.id}" data-content="${msg.content.replace(/"/g, '&quot;').replace(/\n/g, '&#10;')}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="msg-action-btn delete-btn" data-id="${msg.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        msgDiv.appendChild(actionsWrapper);
                    }

                    messagesContainer.appendChild(msgDiv);
                });

                // Add event listeners to action buttons
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        const msgId = btn.dataset.id;
                        const content = btn.dataset.content.replace(/&#10;/g, '\n');
                        enterEditMode(msgId, content);
                    };
                });

                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        const msgId = btn.dataset.id;
                        openDeleteModal(msgId);
                    };
                });

                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }

        convPanel.classList.remove('show');
        chatPanel.classList.add('show');
        backBtn.style.display = 'block';
    }

    // Open chat on conversation click
    document.querySelectorAll('.conv-item').forEach(item => {
        item.onclick = () => {
            const convId = item.dataset.id;
            const title = item.querySelector('.name').textContent;
            loadConversation(convId, title);
        };
    });

    // Back button - reset to conversation list
    backBtn.onclick = () => {
        activeConversationId = null;
        currentConvIdInput.value = '';
        messagesContainer.innerHTML = '';
        exitEditMode();
        chatPanel.classList.remove('show');
        convPanel.classList.add('show');
        backBtn.style.display = 'none';
        document.getElementById('chatTitle').textContent = 'Messages';
    };

    // Send or Edit message
    messageForm.onsubmit = async (e) => {
        e.preventDefault();

        if (!activeConversationId) return;

        const formData = new FormData(messageForm);

        try {
            const response = await fetch(`index.php?c=chatC&a=conversation&id=${activeConversationId}`, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                exitEditMode();
                const title = document.getElementById('chatTitle').textContent;
                loadConversation(activeConversationId, title);
            }
        } catch (error) {
            console.error('Error sending/editing message:', error);
        }
    };

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // ESC key to cancel edit
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isEditMode) {
            exitEditMode();
        }
    });

    // ===== NEW CONVERSATION FUNCTIONALITY =====

    // Show new conversation panel
    newConvBtn.onclick = () => {
        convPanel.classList.remove('show');
        newConvPanel.classList.add('show');
        userSearchInput.value = '';
        searchResults.innerHTML = '';
        selectedUsers.clear();
        updateSelectedUsers();
        createConvBtn.disabled = true;
    };

    // Back to conversation list
    backToConvBtn.onclick = () => {
        newConvPanel.classList.remove('show');
        convPanel.classList.add('show');
        selectedUsers.clear();
        updateSelectedUsers();
    };

    // Search users
    async function searchUsers() {
        const query = userSearchInput.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '<div class="search-hint">Type at least 2 characters to search</div>';
            return;
        }

        try {
            const response = await fetch(`index.php?c=chatC&a=searchUsers&q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success && data.users.length > 0) {
                searchResults.innerHTML = data.users.map(user => `
                    <div class="user-result" data-user-id="${user.id}">
                        <div class="user-avatar">${user.username.substring(0, 2).toUpperCase()}</div>
                        <div class="user-info">
                            <div class="user-name">${user.username}</div>
                            <div class="user-email">${user.email}</div>
                        </div>
                        <button class="btn-select-user ${selectedUsers.has(user.id) ? 'selected' : ''}" data-user-id="${user.id}">
                            ${selectedUsers.has(user.id) ? '<i class="fas fa-check"></i>' : '<i class="fas fa-plus"></i>'}
                        </button>
                    </div>
                `).join('');

                // Add click handlers to select buttons
                document.querySelectorAll('.btn-select-user').forEach(btn => {
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        const userId = parseInt(btn.dataset.userId);
                        const userResult = btn.closest('.user-result');
                        const userName = userResult.querySelector('.user-name').textContent;
                        const userEmail = userResult.querySelector('.user-email').textContent;

                        if (selectedUsers.has(userId)) {
                            selectedUsers.delete(userId);
                            btn.innerHTML = '<i class="fas fa-plus"></i>';
                            btn.classList.remove('selected');
                        } else {
                            selectedUsers.add(userId);
                            btn.innerHTML = '<i class="fas fa-check"></i>';
                            btn.classList.add('selected');

                            // Store user data for display
                            if (!window.selectedUsersData) window.selectedUsersData = {};
                            window.selectedUsersData[userId] = { id: userId, username: userName, email: userEmail };
                        }

                        updateSelectedUsers();
                    };
                });
            } else {
                searchResults.innerHTML = '<div class="no-results">No users found</div>';
            }
        } catch (error) {
            console.error('Error searching users:', error);
            searchResults.innerHTML = '<div class="error-message">Error searching users</div>';
        }
    }

    searchUsersBtn.onclick = searchUsers;
    userSearchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchUsers();
    });

    // Update selected users display
    function updateSelectedUsers() {
        if (selectedUsers.size === 0) {
            selectedUsersDiv.style.display = 'none';
            createConvBtn.disabled = true;
            return;
        }

        selectedUsersDiv.style.display = 'block';
        createConvBtn.disabled = false;

        if (!window.selectedUsersData) window.selectedUsersData = {};

        selectedUsersList.innerHTML = Array.from(selectedUsers).map(userId => {
            const userData = window.selectedUsersData[userId] || { username: 'User', email: '' };
            return `
                <div class="selected-user-chip" data-user-id="${userId}">
                    <span>${userData.username}</span>
                    <button class="remove-user-btn" data-user-id="${userId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }).join('');

        // Add remove handlers
        document.querySelectorAll('.remove-user-btn').forEach(btn => {
            btn.onclick = () => {
                const userId = parseInt(btn.dataset.userId);
                selectedUsers.delete(userId);
                updateSelectedUsers();

                // Update button in search results if visible
                const selectBtn = document.querySelector(`.btn-select-user[data-user-id="${userId}"]`);
                if (selectBtn) {
                    selectBtn.innerHTML = '<i class="fas fa-plus"></i>';
                    selectBtn.classList.remove('selected');
                }
            };
        });
    }

    // Create conversation
    createConvBtn.onclick = async () => {
        if (selectedUsers.size === 0) return;

        const title = convTitleInput.value.trim();
        const isGroup = isGroupCheck.checked || selectedUsers.size > 1;

        try {
            const formData = new FormData();
            formData.append('mode', 'create');
            formData.append('title', title);
            if (isGroup) formData.append('is_group', '1');
            selectedUsers.forEach(userId => {
                formData.append('participants[]', userId);
            });

            const response = await fetch('index.php?c=chatC&a=newConversation', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                // Reload page to show new conversation
                window.location.href = 'index.php?c=chatC&a=listConversations';
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
            alert('Error creating conversation. Please try again.');
        }
    };

</script>
</body>
</html>