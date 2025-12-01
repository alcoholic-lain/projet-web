<?php // view/F/comp/COMS/index.php - WITH WEBSOCKET INTEGRATION


?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuniSpace • Live Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script >
        // ===== CONFIGURATION =====
        const WS_URL = 'ws://localhost:8080';
        const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?? 0 ?> ;
        const CURRENT_USERNAME = '<?= addslashes($_SESSION['username'] ?? 'Guest') ?>';
    </script>
    <style>
        /* Typing indicator styles */
        .typing-indicator {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
            display: none;
            align-items: center;
            gap: 8px;
            margin: 0 20px 10px 20px;
        }
        .typing-indicator.show { display: flex; }
        .typing-dots { display: flex; gap: 4px; }
        .typing-dots span {
            width: 6px; height: 6px;
            background: #ff7f2a; border-radius: 50%;
            animation: typing-bounce 1.4s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing-bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-8px); }
        }

        /* Connection status */
        .connection-status {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11px; padding: 4px 10px; border-radius: 12px;
            background: rgba(0, 0, 0, 0.2);
        }
        .connection-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #86efac;
            box-shadow: 0 0 8px rgba(134, 239, 172, 0.6);
            animation: pulse 2s infinite;
        }
        .connection-dot.disconnected {
            background: #ff6b6b;
            box-shadow: 0 0 8px rgba(255, 107, 107, 0.6);
            animation: none;
        }
        .connection-dot.connecting {
            background: #fbbf24;
            box-shadow: 0 0 8px rgba(251, 191, 36, 0.6);
            animation: blink 1s infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @keyframes blink { 0%, 50%, 100% { opacity: 1; } 25%, 75% { opacity: 0.3; } }
    </style>
</head>
<body class="dark">




<header>
    <h1>TuniSpace</h1>
    <div style="display:flex;align-items:center;gap:20px">
        <div class="connection-status" id="wsStatus">
            <span class="connection-dot connecting"></span>
            <span id="wsStatusText">Connecting...</span>
        </div>
        <span id="themeToggle" style="font-size:1.7rem;cursor:pointer">🌙</span>
        <span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                <small class="badge bg-secondary"><?= $_SESSION['role'] ?? 'front' ?></small>
            <?php else: ?>Guest<?php endif; ?>
        </span>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?c=chatA&a=index" style="color:#ff6b6b">back office</a>
            <a href="index.php?c=chatC&a=logout" style="color:#ff6b6b">Logout</a>
        <?php endif; ?>
    </div>

</header>

<div id="dm-btn">💬</div>

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

        <div class="conv-panel" id="newConvPanel">
            <div class="new-conv-header">
                <button id="backToConvBtn">←</button>
                <h3>New Conversation</h3>
            </div>
            <div class="user-search-box">
                <input type="text" id="userSearchInput" placeholder="Search users by name or email...">
                <button id="searchUsersBtn"><i class="fas fa-search"></i></button>
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
                <button id="createConvBtn" class="btn-create-conv" disabled>Create Conversation</button>
            </div>
        </div>

        <div class="chat-panel" id="chatPanel">
            <div id="messages"></div>

            <div id="typingIndicator" class="typing-indicator">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
                <span id="typingText">Someone is typing...</span>
            </div>

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
<script >



</script>

</body>
</html>