<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Admin - <?= ucfirst($page ?? 'Dashboard') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global scrollbar styles */
        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        *::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }
        *::-webkit-scrollbar-thumb {
            background: rgba(179,140,255,0.3);
            border-radius: 10px;
            transition: var(--transition);
        }
        *::-webkit-scrollbar-thumb:hover {
            background: rgba(179,140,255,0.5);
        }
        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(179,140,255,0.3) rgba(255,255,255,0.05);
        }

        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --sidebar-bg: #0d1117;
            --sidebar-text: #cbd5e1;
            --bg: #0c0f14;
            --text: #dbe6ff;
            --card-bg: rgba(255,255,255,0.06);
            --card-border: rgba(255,255,255,0.12);
            --accent: #b38cff;
            --transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.light {
            --sidebar-bg: #ffffff;
            --sidebar-text: #1e293b;
            --bg: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            --text: #1e293b;
            --card-bg: rgba(255,255,255,0.85);
            --card-border: rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        html, body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: var(--transition);
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Layout */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            padding: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            border-right: 1px solid var(--card-border);
            transition: var(--transition);
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(179,140,255,0.2);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(179,140,255,0.4);
        }
        /* Firefox scrollbar for sidebar */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(179,140,255,0.2) transparent;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid var(--card-border);
            background: rgba(255,255,255,0.02);
        }
        .sidebar-header h3 {
            margin: 0;
            font-size: 22px;
            color: #b38cff;
            font-weight: 700;
            white-space: nowrap;
        }
        .sidebar-toggle-btn {
            width: 36px;
            height: 36px;
            background: rgba(179,140,255,0.15);
            border: 1px solid rgba(179,140,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--accent);
            font-size: 18px;
            transition: var(--transition);
            flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover {
            background: rgba(179,140,255,0.3);
            box-shadow: 0 0 20px rgba(179,140,255,0.5);
            transform: scale(1.1);
        }
        .sidebar-content {
            padding: 24px 20px;
        }
        header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 70px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 900;
            transition: var(--transition);
        }
        main {
            margin-left: var(--sidebar-width);
            margin-top: 70px;
            padding: 40px 30px;
            transition: var(--transition);
        }
        .container-max { max-width: 1400px; margin: 0 auto; }

        /* Collapsed State */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }
        body.sidebar-collapsed .sidebar-header h3,
        body.sidebar-collapsed .sidebar-text,
        body.sidebar-collapsed .menu-title {
            opacity: 0;
            width: 0;
            overflow: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, width 0s 0.2s;
        }
        body.sidebar-collapsed .sidebar-header {
            justify-content: center;
            padding: 20px 10px;
        }
        body.sidebar-collapsed .sidebar-content {
            padding: 24px 10px;
        }
        body.sidebar-collapsed .menu-link {
            justify-content: center;
            padding: 0;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            margin: 4px auto;
            display: flex;
            align-items: center;
            gap: 0;
        }
        body.sidebar-collapsed .menu-link i {
            margin: 0;
            padding: 0;
            width: 20px;
            min-width: 20px;
        }
        body.sidebar-collapsed header {
            left: var(--sidebar-collapsed-width);
        }
        body.sidebar-collapsed main {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Menu styles */
        .menu-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            margin: 30px 0 10px 6px;
            font-weight: 600;
            transition: var(--transition);
        }
        .menu-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 50px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            margin: 4px 0;
            transition: var(--transition);
        }
        .menu-link:hover, .menu-link.active {
            background: rgba(179,140,255,0.3);
            box-shadow: 0 0 20px rgba(179,140,255,0.4);
            transform: translateX(5px);
            color: white;
        }
        .menu-link.active {
            background: linear-gradient(90deg, rgba(179,140,255,0.4), rgba(179,140,255,0.2));
            font-weight: 600;
        }
        .menu-link i {
            font-size: 20px;
            width: 20px;
            min-width: 20px;
            text-align: center;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        /* Orbit AI iframe container */
        .orbit-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            height: calc(100vh - 150px);
        }
        .orbit-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }


        /* Header */
        .header-actions {
            display: flex;
            gap: 12px;
        }
        .header-actions a {
            padding: 10px 22px;
            background: rgba(179,140,255,0.15);
            border: 1px solid rgba(179,140,255,0.4);
            border-radius: 10px;
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .header-actions a:hover {
            background: rgba(179,140,255,0.3);
            box-shadow: 0 0 15px rgba(179,140,255,0.5);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: var(--transition);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(179,140,255,0.3);
        }
        .stat-card i {
            color: var(--accent);
            margin-bottom: 16px;
        }
        .stat-card h4 {
            margin: 12px 0;
            opacity: 0.8;
            font-size: 14px;
            font-weight: 500;
        }
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--accent);
        }

        /* Section Box */
        .section-box {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        .box-header {
            padding: 20px 28px;
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .box-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14.5px;
        }
        th {
            padding: 16px 24px;
            background: rgba(255,255,255,0.04);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.9;
            text-align: left;
        }
        td {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        tbody tr:hover {
            background: rgba(179,140,255,0.1);
        }

        /* Buttons */
        .btn {
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-sm {
            padding: 6px 14px;
            font-size: 13px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #b38cff 0%, #8b5cf6 100%);
            color: white;
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(179,140,255,0.4);
        }
        .btn-outline-primary {
            background: transparent;
            color: #b38cff;
            border: 1px solid #b38cff;
        }
        .btn-outline-primary:hover {
            background: rgba(179,140,255,0.2);
        }
        .btn-outline-danger {
            background: transparent;
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }
        .btn-outline-danger:hover {
            background: rgba(255,107,107,0.15);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-success {
            background: rgba(34,197,94,0.2);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.3);
        }
        .badge-warning {
            background: rgba(251,191,36,0.2);
            color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.3);
        }
        .badge-secondary {
            background: rgba(148,163,184,0.2);
            color: #94a3b8;
            border: 1px solid rgba(148,163,184,0.3);
        }
        .badge-info {
            background: rgba(59,130,246,0.2);
            color: #60a5fa;
            border: 1px solid rgba(59,130,246,0.3);
        }

        /* Form Controls */
        .form-control, .form-select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            transition: var(--transition);
            margin-top: 8px;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(179,140,255,0.2);
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 4px;
            font-size: 14px;
        }

        /* Chat Messages */
        .chat-messages {
            padding: 20px 28px;
            max-height: 600px;
            overflow-y: auto;
        }
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }
        .chat-messages::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }
        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(179,140,255,0.3);
            border-radius: 10px;
            transition: var(--transition);
        }
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgba(179,140,255,0.5);
        }
        /* Firefox scrollbar */
        .chat-messages {
            scrollbar-width: thin;
            scrollbar-color: rgba(179,140,255,0.3) rgba(255,255,255,0.05);
        }
        .message {
            padding: 20px;
            margin-bottom: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--card-border);
            border-radius: 12px;
        }
        .message .meta {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .message .meta strong {
            color: var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            .sidebar-text, .menu-title {
                display: none;
            }
            .menu-link {
                justify-content: center;
            }
            header, main {
                left: var(--sidebar-collapsed-width);
                margin-left: var(--sidebar-collapsed-width);
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-overlay.active {
            display: flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .modal {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: slideUp 0.3s ease;
        }
        .modal-header {
            padding: 24px 28px;
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .modal-close {
            width: 36px;
            height: 36px;
            background: rgba(255,107,107,0.15);
            border: 1px solid rgba(255,107,107,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #ff6b6b;
            font-size: 20px;
            transition: var(--transition);
        }
        .modal-close:hover {
            background: rgba(255,107,107,0.3);
            transform: scale(1.1);
        }
        .modal-body {
            padding: 28px;
        }
        .modal-footer {
            padding: 20px 28px;
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--card-border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .btn-secondary {
            background: rgba(148,163,184,0.2);
            color: #94a3b8;
            border: 1px solid rgba(148,163,184,0.3);
        }
        .btn-secondary:hover {
            background: rgba(148,163,184,0.3);
        }


    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Chat Admin</h3>
        <button class="sidebar-toggle-btn" id="sidebarToggle">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
    <div class="sidebar-content">
        <div class="menu-title">Navigation</div>
        <a href="index.php?c=chatA&a=index" class="menu-link <?= $page==='dashboard'?'active':'' ?>">
            <i class="bi bi-speedometer2"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="index.php?c=chatA&a=users" class="menu-link <?= $page==='users'?'active':'' ?>">
            <i class="bi bi-people"></i>
            <span class="sidebar-text">Users</span>
        </a>
        <a href="index.php?c=chatA&a=conversations" class="menu-link <?= in_array($page,['conversations','conversationDetail'])?'active':'' ?>">
            <i class="bi bi-chat-dots"></i>
            <span class="sidebar-text">Conversations</span>
        </a>
        <a href="index.php?c=chatA&a=messages" class="menu-link <?= $page==='messages'?'active':'' ?>">
            <i class="bi bi-chat-text"></i>
            <span class="sidebar-text">Messages</span>
        </a>
        <!-- NEW: Orbit AI Link -->
        <div class="menu-title" style="margin-top:30px">AI Assistant</div>
        <a href="index.php?c=chatA&a=orbit" class="menu-link <?= $page==='orbit'?'active':'' ?>">
            <i class="bi bi-robot"></i>
            <span class="sidebar-text">Orbit AI</span>
        </a>

        <div class="menu-title" style="margin-top:50px">Theme</div>
        <div class="menu-link" id="themeToggle" style="cursor:pointer">
            <i class="bi bi-moon-fill"></i>
            <span class="sidebar-text">Toggle Theme</span>
        </div>
    </div>
</aside>

<header>
    <div style="display:flex;align-items:center;gap:16px">
        <div>
            <h1 style="margin:0;font-size:24px">Administration Panel</h1>
            <p style="margin:0;opacity:0.7"><?= ucfirst($page ?? 'Home') ?></p>
        </div>
    </div>
    <div class="header-actions">
        <a href="index.php?c=chatC&a=listConversations">Frontoffice</a>
        <a href="index.php?c=chatC&a=logout">Logout</a>
    </div>
</header>

<main>
    <div class="container-max">

        <?php if ($page === 'dashboard'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">Dashboard Overview</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="bi bi-people" style="font-size:42px"></i>
                    <h4>Total Users</h4>
                    <div class="stat-number"><?= $userCount ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-chat-dots" style="font-size:42px"></i>
                    <h4>Conversations</h4>
                    <div class="stat-number"><?= $conversationCount ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-chat-text" style="font-size:42px"></i>
                    <h4>Messages</h4>
                    <div class="stat-number"><?= $messageCount ?? 0 ?></div>
                </div>
            </div>

            <!-- Most Active Section -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:30px;margin-top:30px">
                <!-- Most Active User -->
                <div class="section-box">
                    <div class="box-header">
                        <h3>Most Active User</h3>
                        <span class="badge badge-success">Top Contributor</span>
                    </div>
                    <div style="padding:28px">
                        <?php if (isset($mostActiveUser) && $mostActiveUser): ?>
                            <div style="text-align:center">
                                <div style="width:80px;height:80px;background:linear-gradient(135deg, #b38cff 0%, #8b5cf6 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;font-weight:700;color:white">
                                    <?= strtoupper(substr($mostActiveUser['username'], 0, 2)) ?>
                                </div>
                                <h4 style="margin:0 0 8px 0;font-size:20px"><?= htmlspecialchars($mostActiveUser['username']) ?></h4>
                                <p style="opacity:0.7;margin:0 0 20px 0"><?= htmlspecialchars($mostActiveUser['email']) ?></p>
                                <div style="display:flex;justify-content:center;gap:40px;padding:20px;background:rgba(255,255,255,0.03);border-radius:12px">
                                    <div>
                                        <div style="font-size:28px;font-weight:700;color:#b38cff"><?= $mostActiveUser['message_count'] ?></div>
                                        <div style="font-size:13px;opacity:0.7;margin-top:4px">Messages</div>
                                    </div>
                                    <div>
                                        <div style="font-size:28px;font-weight:700;color:#60a5fa"><?= $mostActiveUser['conversation_count'] ?></div>
                                        <div style="font-size:13px;opacity:0.7;margin-top:4px">Conversations</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="text-align:center;opacity:0.6;padding:40px 0">No user activity yet</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Most Active Conversation -->
                <div class="section-box">
                    <div class="box-header">
                        <h3>Most Active Conversation</h3>
                        <span class="badge badge-info">Trending</span>
                    </div>
                    <div style="padding:28px">
                        <?php if (isset($mostActiveConversation) && $mostActiveConversation): ?>
                            <div style="text-align:center">
                                <div style="width:80px;height:80px;background:linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px;color:white">
                                    <i class="bi bi-chat-<?= $mostActiveConversation['is_group'] ? 'dots' : 'text' ?>-fill"></i>
                                </div>
                                <h4 style="margin:0 0 8px 0;font-size:20px">
                                    <?= htmlspecialchars($mostActiveConversation['title'] ?: 'Direct Message') ?>
                                </h4>
                                <p style="opacity:0.7;margin:0 0 20px 0">
                                    <span class="badge <?= $mostActiveConversation['is_group']?'badge-info':'badge-secondary' ?>">
                                        <?= $mostActiveConversation['is_group']?'Group':'DM' ?>
                                    </span>
                                </p>
                                <div style="display:flex;justify-content:center;gap:40px;padding:20px;background:rgba(255,255,255,0.03);border-radius:12px">
                                    <div>
                                        <div style="font-size:28px;font-weight:700;color:#60a5fa"><?= $mostActiveConversation['message_count'] ?></div>
                                        <div style="font-size:13px;opacity:0.7;margin-top:4px">Messages</div>
                                    </div>
                                    <div>
                                        <div style="font-size:28px;font-weight:700;color:#4ade80"><?= $mostActiveConversation['participant_count'] ?></div>
                                        <div style="font-size:13px;opacity:0.7;margin-top:4px">Participants</div>
                                    </div>
                                </div>
                                <a href="index.php?c=chatA&a=viewConversation&id=<?= $mostActiveConversation['id'] ?>"
                                   class="btn btn-primary" style="margin-top:20px;width:100%">
                                    View Conversation
                                </a>
                            </div>
                        <?php else: ?>
                            <p style="text-align:center;opacity:0.6;padding:40px 0">No conversation activity yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($page === 'users'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">Users Management</h2>
            <div style="margin-bottom:20px">
                <button onclick="openUserModal()" class="btn btn-primary" style="padding:12px 28px;font-size:15px">
                    <i class="bi bi-plus-circle" style="margin-right:8px"></i> Create New User
                </button>
            </div>
            <div class="section-box">
                <div class="box-header"><h3>All Users</h3></div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users ?? [] as $u): ?>
                        <tr>
                            <td><?= $u->getId() ?></td>
                            <td><strong><?= htmlspecialchars($u->getUsername()) ?></strong></td>
                            <td><?= htmlspecialchars($u->getEmail()) ?></td>
                            <td>
                                <span class="badge <?= $u->getRole()==='back'?'badge-warning':'badge-secondary' ?>">
                                    <?= $u->getRole()==='back'?'Backoffice':'Frontoffice' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $u->isActive()?'badge-success':'badge-secondary' ?>">
                                    <?= $u->isActive()?'Active':'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="openUserModal(<?= $u->getId() ?>)" class="btn btn-outline-primary btn-sm">Edit</a>
                                <a href="index.php?c=chatA&a=deleteUser&id=<?= $u->getId() ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- User Modal -->
            <div class="modal-overlay" id="userModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3 id="modalTitle">Create New User</h3>
                        <button class="modal-close" onclick="closeUserModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form method="post" action="index.php?c=chatA&a=saveUser" id="userForm">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="userId" value="">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                                <div>
                                    <label>Username</label>
                                    <input class="form-control" type="text" name="username" id="username" required>
                                </div>
                                <div>
                                    <label>Email</label>
                                    <input class="form-control" type="email" name="email" id="email" required>
                                </div>
                            </div>
                            <div style="margin:20px 0">
                                <label>Password <span id="passwordHint" style="opacity:0.6;font-size:13px"></span></label>
                                <input class="form-control" type="password" name="password" id="password">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:end">
                                <div>
                                    <label>Role</label>
                                    <select class="form-select" name="role" id="role">
                                        <option value="front">Frontoffice</option>
                                        <option value="back">Backoffice (Admin)</option>
                                    </select>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <input type="checkbox" name="is_active" id="is_active" style="width:20px;height:20px" checked>
                                    <label for="is_active" style="margin:0">Active Account</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeUserModal()" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save User</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php elseif ($page === 'conversations'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">All Conversations</h2>
            <div class="section-box">
                <div class="box-header"><h3>Conversations List</h3></div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($conversations ?? [] as $c): ?>
                        <tr>
                            <td>#<?= $c->getId() ?></td>
                            <td><?= htmlspecialchars($c->getTitle() ?: 'Direct Message') ?></td>
                            <td>
                                <span class="badge <?= $c->isGroup()?'badge-info':'badge-secondary' ?>">
                                    <?= $c->isGroup()?'Group':'DM' ?>
                                </span>
                            </td>
                            <td><?= $c->getCreatedAt() ?></td>
                            <td>
                                <a href="index.php?c=chatA&a=viewConversation&id=<?= $c->getId() ?>" class="btn btn-outline-primary btn-sm">View</a>
                                <a href="index.php?c=chatA&a=deleteConversation&id=<?= $c->getId() ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page === 'conversationDetail'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">
                Conversation #<?= $conversation->getId() ?> - <?= htmlspecialchars($conversation->getTitle() ?: 'Direct Message') ?>
            </h2>
            <div style="display:grid;grid-template-columns:360px 1fr;gap:30px">
                <div>
                    <div class="section-box">
                        <div class="box-header"><h3>Participants</h3></div>
                        <div style="padding:10px">
                            <?php foreach ($participants ?? [] as $p): ?>
                                <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center">
                                    <div>
                                        <strong><?= htmlspecialchars($p['username']) ?></strong><br>
                                        <small style="opacity:0.7"><?= htmlspecialchars($p['email']) ?></small>
                                    </div>
                                    <a href="index.php?c=chatA&a=removeParticipant&conversation_id=<?= $conversation->getId() ?>&user_id=<?= $p['user_id'] ?>"
                                       class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove user?')">Remove</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="section-box">
                        <div class="box-header">
                            <h3>Messages</h3>
                            <a href="index.php?c=chatA&a=deleteConversation&id=<?= $conversation->getId() ?>"
                               class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete entire conversation?')">Delete All</a>
                        </div>
                        <div class="chat-messages">
                            <?php foreach ($messages ?? [] as $m): ?>
                                <div class="message">
                                    <div class="meta">
                                        <strong><?= htmlspecialchars($m['username']) ?></strong>
                                        <span style="margin-left:12px;opacity:0.8;font-size:13px"><?= $m['created_at'] ?></span>
                                    </div>
                                    <div style="margin:10px 0;line-height:1.6"><?= nl2br(htmlspecialchars($m['content'])) ?></div>
                                    <a href="index.php?c=chatA&a=deleteMessage&id=<?= $m['id'] ?>&from_conversation=<?= $conversation->getId() ?>"
                                       style="color:#ff6b6b;font-size:13px;text-decoration:none" onclick="return confirm('Delete message?')">delete message</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($page === 'messages'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">All Messages</h2>
            <div class="section-box">
                <div class="box-header"><h3>Message History</h3></div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conversation</th>
                        <th>User</th>
                        <th>Content</th>
                        <th>Sent</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages ?? [] as $m): ?>
                        <tr>
                            <td><?= $m->getId() ?></td>
                            <td>
                                <a href="index.php?c=chatA&a=viewConversation&id=<?= $m->getConversationId() ?>" style="color:var(--accent);text-decoration:none">
                                    #<?= $m->getConversationId() ?>
                                </a>
                            </td>
                            <td><?= $m->getUserId() ?></td>
                            <td><?= htmlspecialchars(substr($m->getContent(),0,80)) ?><?= strlen($m->getContent())>80?'...':'' ?></td>
                            <td><?= $m->getCreatedAt() ?></td>
                            <td>
                                <a href="index.php?c=chatA&a=deleteMessage&id=<?= $m->getId() ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>



        <?php elseif ($page === 'orbit'): ?>
            <h2 style="font-size:28px;font-weight:700;margin-bottom:30px">
                <i class="bi bi-robot" style="margin-right:12px;color:#b38cff"></i>
                Orbit AI Assistant
            </h2>
            <p style="opacity:0.8;margin-bottom:30px;font-size:15px">
                Chat with Orbit AI to get help with administration tasks, data analysis, and more.
            </p>

            <div class="orbit-container" style="position: relative;">
                <!-- Loading indicator -->
                <div id="orbitLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 1;">
                    <div style="font-size: 48px; color: #b38cff; margin-bottom: 20px;">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div style="font-size: 18px; opacity: 0.8;">Loading Orbit AI...</div>
                    <div style="margin-top: 10px; font-size: 13px; opacity: 0.6;">
                        If this takes too long, make sure the server is running on port 8080
                    </div>
                </div>

                <iframe
                        id="orbitFrame"
                        src="http://localhost:8080"
                        allow="microphone; camera; clipboard-read; clipboard-write"
                        sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals allow-downloads"
                        title="Orbit AI Assistant"
                        onload="document.getElementById('orbitLoading').style.display='none'"
                        onerror="handleIframeError()"
                        style="width: 100%; height: 100%; border: none; position: relative; z-index: 2;">
                </iframe>
            </div>

            <script>
                function handleIframeError() {
                    document.getElementById('orbitLoading').innerHTML = `
                        <div style="text-align: center;">
                            <div style="font-size: 48px; color: #ff6b6b; margin-bottom: 20px;">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div style="font-size: 18px; color: #ff6b6b; margin-bottom: 10px;">
                                Unable to connect to Orbit AI
                            </div>
                            <div style="font-size: 14px; opacity: 0.8; max-width: 400px; margin: 0 auto; line-height: 1.6;">
                                Please ensure:<br>
                                • The server is running on port 8080<br>
                                • No firewall is blocking the connection<br>
                                • CORS is properly configured
                            </div>
                            <button onclick="location.reload()" class="btn btn-primary" style="margin-top: 20px;">
                                Retry
                            </button>
                        </div>
                    `;
                }

                // Check if iframe loads within 5 seconds
                setTimeout(function() {
                    const loading = document.getElementById('orbitLoading');
                    if (loading && loading.style.display !== 'none') {
                        console.warn('Orbit AI iframe taking longer than expected to load');
                    }
                }, 5000);
            </script>
        <?php endif; ?>

    </div>
</main>


<script>
    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = themeToggle.querySelector('i');

    function updateThemeIcon() {
        const isLight = document.body.classList.contains('light');
        themeIcon.className = isLight ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light');
            const isLight = document.body.classList.contains('light');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            updateThemeIcon();
        });
    }

    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light');
        updateThemeIcon();
    }

    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const toggleIcon = sidebarToggle.querySelector('i');

    if (sidebarToggle) {
        if (localStorage.getItem('sidebar') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
            toggleIcon.className = 'bi bi-chevron-right';
        }

        sidebarToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            const collapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar', collapsed ? 'collapsed' : 'open');
            toggleIcon.className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        });
    }

    // User Modal Functions
    function openUserModal(userId = null) {
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('userForm');

        if (userId) {
            // Edit mode - fetch user data via AJAX or pass from PHP
            modalTitle.textContent = 'Edit User';
            document.getElementById('passwordHint').textContent = '(leave blank to keep current)';

            // Fetch user data
            fetch(`index.php?c=chatA&a=getUserData&id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('userId').value = data.id;
                    document.getElementById('username').value = data.username;
                    document.getElementById('email').value = data.email;
                    document.getElementById('role').value = data.role;
                    document.getElementById('is_active').checked = data.is_active;
                    document.getElementById('password').value = '';
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                });
        } else {
            // Create mode
            modalTitle.textContent = 'Create New User';
            document.getElementById('passwordHint').textContent = '';
            form.reset();
            document.getElementById('userId').value = '';
            document.getElementById('is_active').checked = true;
        }

        modal.classList.add('active');
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');
        modal.classList.remove('active');
    }

    // Close modal on overlay click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeUserModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUserModal();
        }
    });

    // Check if we need to open modal after page load (for edit)
    <?php if (isset($_GET['edit_id']) && $page === 'users'): ?>
    openUserModal(<?= (int)$_GET['edit_id'] ?>);
    <?php endif; ?>
</script>
</body>
</html>