<?php
// view/F/index.php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chat Frontoffice</title>

    <!-- Bootstrap CSS -->
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
    >

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../view/F/assets/css/style.css">
</head>
<body class="theme-dark">
<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php?c=chatC&a=index">
            <span class="me-1">💅</span>chat demo
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array($page, ['listConversations', 'conversation', 'newConversation']) ? ' active' : ''; ?>"
                           href="index.php?c=chatC&a=listConversations">Chat</a>
                    </li>
                    <?php if (($_SESSION['role'] ?? 'front') === 'back'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?c=chatA&a=index">Backoffice</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <button id="themeToggle" class="btn btn-sm btn-outline-light" type="button">
                    🌙
                </button>

                <span class="navbar-text me-2 small">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                        <span class="badge bg-secondary ms-1"><?= htmlspecialchars($_SESSION['role'] ?? 'front'); ?></span>
                    <?php else: ?>
                        Not logged in
                    <?php endif; ?>
                </span>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="btn btn-outline-light btn-sm" href="index.php?c=chatC&a=logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mb-5">

    <?php if ($page === 'auth'): ?>

        <!-- LOGIN / REGISTER -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <?php if (!empty($authErrors ?? [])): ?>
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            <?php foreach ($authErrors as $e): ?>
                                <li><?= htmlspecialchars($e); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab"
                                data-bs-target="#login-tab-pane" type="button" role="tab">
                            Login
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab"
                                data-bs-target="#register-tab-pane" type="button" role="tab">
                            Register
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="authTabsContent">
                    <!-- Login -->
                    <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Login</h5>
                                <form method="post" action="index.php?c=chatC&a=login">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email" >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control" type="password" name="password" >
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Register -->
                    <div class="tab-pane fade" id="register-tab-pane" role="tabpanel">
                        <div class="card shadow-sm mt-3 mt-md-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Register</h5>
                                <form method="post" action="index.php?c=chatC&a=register">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input class="form-control" type="text" name="username" >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email" >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control" type="password" name="password" >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Account type</label>
                                        <select class="form-select" name="role" >
                                            <option value="front">Frontoffice (client)</option>
                                            <option value="back">Backoffice (admin)</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Register</button>
                                </form>
                            </div>
                            <div class="card-footer text-muted small">
                                jwana behi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'listConversations'): ?>

        <!-- Sidebar + placeholder -->
        <div class="row chat-layout">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Conversations</h6>
                        <a href="index.php?c=chatC&a=newConversation"
                           class="btn btn-sm btn-outline-primary">
                            + New
                        </a>
                    </div>
                    <div class="list-group list-group-flush sidebar-conversations">
                        <?php foreach ($conversations as $c): ?>
                            <?php
                            $titleToShow = $c['display_title'] ?? $c['title'] ?? 'Conversation';
                            ?>
                            <a href="index.php?c=chatC&a=conversation&id=<?= (int)$c['id']; ?>"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($titleToShow); ?></div>
                                        <small class="text-muted">
                                            <?= $c['is_group'] ? 'Group' : 'DM'; ?>
                                        </small>
                                    </div>
                                    <?php if ($c['is_admin']): ?>
                                        <span class="badge bg-primary">admin</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm h-100 d-flex align-items-center justify-content-center glass-card">
                    <div class="card-body text-center">
                        <h4 class="mb-2">Welcome to your inbox 👋</h4>
                        <p class="text-muted mb-3">
                            Choose a conversation on the left, or start a new one.
                        </p>
                        <a href="index.php?c=chatC&a=newConversation" class="btn btn-primary">
                            Start a new conversation
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'conversation'): ?>

        <div class="row chat-layout">
            <!-- Sidebar -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Conversations</h6>
                        <a href="index.php?c=chatC&a=newConversation"
                           class="btn btn-sm btn-outline-primary">
                            + New
                        </a>
                    </div>
                    <div class="list-group list-group-flush sidebar-conversations">
                        <?php foreach ($conversations as $c): ?>
                            <?php
                            $titleToShow = $c['display_title'] ?? $c['title'] ?? 'Conversation';
                            $active = ((int)$c['id'] === (int)$conversation->getId());
                            ?>
                            <a href="index.php?c=chatC&a=conversation&id=<?= (int)$c['id']; ?>"
                               class="list-group-item list-group-item-action<?= $active ? ' active' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($titleToShow); ?></div>
                                        <small class="text-muted">
                                            <?= $c['is_group'] ? 'Group' : 'DM'; ?>
                                        </small>
                                    </div>
                                    <?php if ($c['is_admin']): ?>
                                        <span class="badge bg-light text-dark">admin</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Chat main -->
            <div class="col-md-8">
                <?php
                $currentUserId   = (int)($_SESSION['user_id'] ?? 0);
                $currentIsAdmin  = false;
                foreach ($participants as $p) {
                    if ((int)$p['user_id'] === $currentUserId && (int)$p['is_admin'] === 1) {
                        $currentIsAdmin = true;
                        break;
                    }
                }
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 d-flex align-items-center gap-2">
                                <?= htmlspecialchars($displayTitle); ?>
                                <span class="badge rounded-pill <?= $conversation->isGroup() ? 'bg-info' : 'bg-secondary'; ?>">
                                    <?= $conversation->isGroup() ? 'Group' : 'DM'; ?>
                                </span>
                            </h4>
                            <div class="small text-muted">
                                Conversation #<?= (int)$conversation->getId(); ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($currentIsAdmin): ?>
                                <button class="btn btn-sm btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#renameConversationForm">
                                    Rename
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <small class="text-muted">
                        Participants:
                        <?php foreach ($participants as $idx => $p): ?>
                            <?= $idx > 0 ? ', ' : ''; ?><?= htmlspecialchars($p['username']); ?>
                        <?php endforeach; ?>
                    </small>
                </div>

                <?php if ($currentIsAdmin): ?>
                    <div class="collapse mb-2" id="renameConversationForm">
                        <div class="card card-body py-2 px-3 shadow-sm">
                            <form class="row g-2 align-items-center"
                                  method="post"
                                  action="index.php?c=chatC&a=renameConversation&id=<?= (int)$conversation->getId(); ?>">
                                <div class="col-sm-8">
                                    <input class="form-control form-control-sm" type="text" name="title"
                                           value="<?= htmlspecialchars($conversation->getTitle()); ?>"
                                           placeholder="Leave empty to show members' names">
                                </div>
                                <div class="col-sm-4 d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" type="submit">Save name</button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#renameConversationForm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm chat-card">
                    <div class="card-body chat-messages" id="messages">
                        <?php foreach ($messages as $m): ?>
                            <?php $isOwn = ((int)$m['user_id'] === (int)$_SESSION['user_id']); ?>
                            <div class="message <?= $isOwn ? 'message-own' : 'message-other'; ?>"
                                 data-message-id="<?= (int)$m['id']; ?>">
                                <div class="meta">
                                    <strong><?= htmlspecialchars($m['username']); ?></strong>
                                    <span class="text-muted small ms-2">
                                        <?= htmlspecialchars($m['created_at']); ?>
                                    </span>
                                </div>
                                <div class="text">
                                    <?= nl2br(htmlspecialchars($m['content'])); ?>
                                </div>

                                <?php if ($isOwn): ?>
                                    <div class="actions mt-1">
                                        <button type="button"
                                                class="btn btn-sm btn-link p-0 edit-message">
                                            Edit
                                        </button>

                                        <!-- Unsend (delete) -->
                                        <form method="post"
                                              action="index.php?c=chatC&a=conversation&id=<?= (int)$conversation->getId(); ?>"
                                              class="d-inline">
                                            <input type="hidden" name="mode" value="delete">
                                            <input type="hidden" name="message_id"
                                                   value="<?= (int)$m['id']; ?>">
                                            <button type="submit"
                                                    class="btn btn-sm btn-link text-danger p-0 ms-2">
                                                Unsend
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit form -->
                                    <form method="post"
                                          action="index.php?c=chatC&a=conversation&id=<?= (int)$conversation->getId(); ?>"
                                          class="edit-message-form d-none mt-1">
                                        <input type="hidden" name="mode" value="edit">
                                        <input type="hidden" name="message_id"
                                               value="<?= (int)$m['id']; ?>">
                                        <div class="input-group input-group-sm">
                                            <textarea name="content"
                                                      class="form-control"
                                                      rows="1"><?= htmlspecialchars($m['content']); ?></textarea>
                                            <button class="btn btn-success" type="submit">Save</button>
                                            <button class="btn btn-outline-secondary cancel-edit"
                                                    type="button">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="card-footer">
                        <!-- Send message -->
                        <form class="message-form d-flex flex-column gap-2"
                              method="post"
                              action="index.php?c=chatC&a=conversation&id=<?= (int)$conversation->getId(); ?>">
                            <input type="hidden" name="mode" value="send">
                            <textarea class="form-control" name="content" rows="2"
                                      placeholder="Type a message..."></textarea>
                            <button type="submit" class="btn btn-primary align-self-end">
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'newConversation'): ?>

        <div class="row chat-layout">
            <!-- Sidebar -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Conversations</h6>
                        <a href="index.php?c=chatC&a=newConversation"
                           class="btn btn-sm btn-outline-primary active">
                            + New
                        </a>
                    </div>
                    <div class="list-group list-group-flush sidebar-conversations">
                        <?php foreach ($conversations as $c): ?>
                            <?php
                            $titleToShow = $c['display_title'] ?? $c['title'] ?? 'Conversation';
                            ?>
                            <a href="index.php?c=chatC&a=conversation&id=<?= (int)$c['id']; ?>"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($titleToShow); ?></div>
                                        <small class="text-muted">
                                            <?= $c['is_group'] ? 'Group' : 'DM'; ?>
                                        </small>
                                    </div>
                                    <?php if ($c['is_admin']): ?>
                                        <span class="badge bg-primary">admin</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- New conversation form -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">Start a new conversation</h4>
                        <form method="post" action="index.php?c=chatC&a=newConversation">
                            <div class="mb-3">
                                <label class="form-label">Search users</label>
                                <div class="input-group">
                                    <span class="input-group-text">🔍</span>
                                    <input class="form-control" type="text" name="q"
                                           placeholder="Type a username or email..."
                                           value="<?= htmlspecialchars($searchTerm ?? ''); ?>">
                                    <button type="submit" name="mode" value="search"
                                            class="btn btn-outline-secondary">
                                        Search
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Select one user for a DM, or multiple users for a group chat.
                                    If you don’t set a name, the title will be based on other members.
                                </small>
                            </div>

                            <?php if (!empty($searchResults ?? [])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Select participants</label>
                                    <div class="list-group user-search-results">
                                        <?php foreach ($searchResults as $u): ?>
                                            <label class="list-group-item d-flex align-items-center">
                                                <input class="form-check-input me-2" type="checkbox"
                                                       name="participants[]" value="<?= $u->getId(); ?>">
                                                <div>
                                                    <strong><?= htmlspecialchars($u->getUsername()); ?></strong>
                                                    <div class="small text-muted">
                                                        <?= htmlspecialchars($u->getEmail()); ?>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Conversation title (optional)</label>
                                <input class="form-control" type="text" name="title"
                                       placeholder="Leave empty to auto-show members' names">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_group" id="is_group">
                                <label class="form-check-label" for="is_group">
                                    This is a group conversation
                                </label>
                            </div>

                            <?php if (!empty($errorCreate ?? '')): ?>
                                <div class="alert alert-danger py-2">
                                    <?= htmlspecialchars($errorCreate); ?>
                                </div>
                            <?php endif; ?>

                            <button type="submit" name="mode" value="create"
                                    class="btn btn-primary">
                                Create conversation
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'error'): ?>

        <div class="alert alert-danger shadow-sm" role="alert">
            <h4 class="alert-heading">Error</h4>
            <p><?= htmlspecialchars($error ?? 'Unknown error'); ?></p>
        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            Unknown page.
        </div>

    <?php endif; ?>

</div>






</body>
</html>
