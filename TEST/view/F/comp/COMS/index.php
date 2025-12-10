<?php // view/F/comp/COMS/index.php - WITH WEBSOCKET INTEGRATION ?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuniSpace • Live Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="view/F/assets/css/style.css">
    <script>
        // ===== CONFIGURATION =====
        // Use current hostname so it works on both PC and phone
        const WS_URL = `ws://${window.location.hostname}:8080`;
        const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?? 0 ?>;
        const CURRENT_USERNAME = '<?= addslashes($_SESSION['username'] ?? 'Guest') ?>';

        console.log('[CONFIG] WebSocket URL:', WS_URL);
        console.log('[CONFIG] User ID:', CURRENT_USER_ID);
        console.log('[CONFIG] Username:', CURRENT_USERNAME);
    </script>
    <style>
        /* User Avatar and Dropdown Styles */
        #user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff7f2a, #ff2a7f);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            border: 2.5px solid rgba(255, 127, 42, 0.6);
            box-shadow: 0 0 20px rgba(255, 127, 42, 0.4);
            user-select: none;
        }

        #user-avatar:hover {
            transform: scale(1.1);
        }

        #user-dropdown {
            position: absolute;
            top: 68px;
            right: 20px;
            width: 260px;
            background: rgba(20, 25, 50, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 10000;
        }

        #user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #e2e8f0;
            font-size: 14.5px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background: rgba(255, 127, 42, 0.2);
            color: white;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 8px 0;
        }

        .dropdown-item.logout {
            color: #ff6b6b;
        }

        .dropdown-item.logout:hover {
            background: rgba(255, 107, 107, 0.2);
        }

        /* Theme Toggle Switch */
        .toggle-wrapper {
            margin-left: auto;
        }

        .toggle-checkbox {
            display: none;
        }

        .toggle-label {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            background: #334155;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-label::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 4px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-checkbox:checked + .toggle-label {
            background: #ff7f2a;
        }

        .toggle-checkbox:checked + .toggle-label::after {
            transform: translateX(20px);
        }
    </style>
</head>
<body class="dark">

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <h1>TuniSpace</h1>
    <div style="display:flex;align-items:center;gap:20px">
        <div class="connection-status" id="wsStatus">
            <span class="connection-dot connecting"></span>
            <span id="wsStatusText">Connecting...</span>
        </div>

        <div style="position:relative">
            <div id="user-avatar">
                <?php
                $firstLetter = isset($_SESSION['username'])
                        ? strtoupper(substr($_SESSION['username'], 0, 1))
                        : 'G';
                echo $firstLetter;
                ?>
            </div>

            <div id="user-dropdown">
                <div class="dropdown-item" style="pointer-events:none;opacity:0.95;background:rgba(255,127,42,0.1)">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <div style="font-weight:600;font-size:15px">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?>
                        </div>
                        <div style="font-size:12px;opacity:0.7">
                            <?= $_SESSION['role'] ?? 'front' ?>
                        </div>
                    </div>
                </div>

                <div class="dropdown-divider"></div>

                <div class="dropdown-item" id="profileBtn">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </div>

                <div class="dropdown-item" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                    <span>Dark Mode</span>
                    <div class="toggle-wrapper">
                        <input type="checkbox" id="theme-switch" class="toggle-checkbox" checked>
                        <label for="theme-switch" class="toggle-label"></label>
                    </div>
                </div>

                <div class="dropdown-item" id="settingsBtn">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </div>

                <?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'back')): ?>
                    <div class="dropdown-divider"></div>

                    <a href="index.php?c=chatA&a=index" class="dropdown-item">
                        <i class="fas fa-user-shield"></i>
                        <span>Back Office</span>
                    </a>
                <?php endif; ?>

                <div class="dropdown-divider"></div>

                <a href="index.php?c=chatC&a=logout" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>


<div id="dm-btn"><i class="fas fa-comments"></i></div>

<div id="dm-popup">
    <div class="chat-header">
        <div class="chat-header-left">
            <button id="backBtn">←</button>
            <div id="popup-avatar" style="display: none;">TS</div>
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
            <div class="conv-list" id="convList">
                <?php foreach ($conversations as $c):
                    $title = $c['display_title'] ?? $c['title'] ?? 'Chat';
                    ?>
                    <div class="conv-item" data-id="<?= $c['id'] ?>" data-name="<?= strtolower(htmlspecialchars($title)) ?>">
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
                <input type="text" id="userSearchInput" placeholder="Search users by name or email..." autocomplete="off">
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

<script src="view/F/assets/js/chat.js"></script>
<script>
    // User Dropdown Toggle
    document.getElementById('user-avatar').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('user-dropdown').classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    });

    // Prevent dropdown from closing when clicking inside
    document.getElementById('user-dropdown').addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Theme Toggle
    const themeSwitch = document.getElementById('theme-switch');
    const html = document.documentElement;

    // Load saved theme
    if (localStorage.getItem('theme') === 'light') {
        html.classList.remove('dark');
        html.classList.add('light');
        themeSwitch.checked = false;
    } else {
        html.classList.add('dark');
        html.classList.remove('light');
        themeSwitch.checked = true;
    }

    // Toggle theme
    themeSwitch.addEventListener('change', function() {
        if (this.checked) {
            html.classList.add('dark');
            html.classList.remove('light');
            localStorage.setItem('theme', 'dark');
        } else {
            html.classList.remove('dark');
            html.classList.add('light');
            localStorage.setItem('theme', 'light');
        }
    });

    // Profile button (placeholder)
    document.getElementById('profileBtn').addEventListener('click', function() {
        alert('Profile page coming soon!');
    });

    // Settings button (placeholder)
    document.getElementById('settingsBtn').addEventListener('click', function() {
        alert('Settings page coming soon!');
    });
</script>

</body>
</html>