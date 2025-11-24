<?php
// view/B/index.php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chat Admin</title>

    <!-- Bootstrap CSS -->
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
    >

    <link rel="stylesheet" href="../view/F/assets/css/style.css">
</head>
<body class="bg-light">

<!-- Top navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?c=admin&a=index">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarAdmin" aria-controls="navbarAdmin"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>

            <a class="btn btn-outline-light btn-sm me-2"
               href="index.php?c=chat&a=listConversations">
                Frontoffice
            </a>
            <a class="btn btn-outline-light btn-sm"
               href="index.php?c=chat&a=logout">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container ">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-3">
            <div class="list-group">
                <a href="index.php?c=admin&a=index"
                   class="list-group-item list-group-item-action<?= $page === 'dashboard' ? ' active' : ''; ?>">
                    Dashboard
                </a>
                <a href="index.php?c=admin&a=users"
                   class="list-group-item list-group-item-action<?= $page === 'users' ? ' active' : ''; ?>">
                    Users
                </a>
                <a href="index.php?c=admin&a=conversations"
                   class="list-group-item list-group-item-action<?= ($page === 'conversations' || $page === 'conversationDetail') ? ' active' : ''; ?>">
                    Conversations
                </a>
                <a href="index.php?c=admin&a=messages"
                   class="list-group-item list-group-item-action<?= $page === 'messages' ? ' active' : ''; ?>">
                    Messages
                </a>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">

            <?php if ($page === 'dashboard'): ?>

                <h2 class="mb-4">Dashboard</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">Users</h5>
                                <p class="display-6"><?= $userCount; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">Conversations</h5>
                                <p class="display-6"><?= $conversationCount; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">Messages</h5>
                                <p class="display-6"><?= $messageCount; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($page === 'users'): ?>

                <h2 class="mb-3">Users</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u->getId(); ?></td>
                                    <td><?= htmlspecialchars($u->getUsername()); ?></td>
                                    <td><?= htmlspecialchars($u->getEmail()); ?></td>
                                    <td>
                                        <?php if ($u->getRole() === 'back'): ?>
                                            <span class="badge bg-warning text-dark">Backoffice</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Frontoffice</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u->isActive()): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="index.php?c=admin&a=users&edit_id=<?= $u->getId(); ?>">Edit</a>
                                        <a class="btn btn-sm btn-outline-danger"
                                           href="index.php?c=admin&a=deleteUser&id=<?= $u->getId(); ?>"
                                           onclick="return confirm('Delete user?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?= isset($editUser) ? 'Edit user' : 'Create new user'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?c=admin&a=saveUser">
                            <input type="hidden" name="id"
                                   value="<?= isset($editUser) ? $editUser->getId() : ''; ?>">

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input class="form-control" type="text" name="username"
                                       value="<?= isset($editUser) ? htmlspecialchars($editUser->getUsername()) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email"
                                       value="<?= isset($editUser) ? htmlspecialchars($editUser->getEmail()) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password (plain for demo)</label>
                                <input class="form-control" type="text" name="password"
                                       placeholder="<?= isset($editUser) ? '(leave empty to keep current)' : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role">
                                    <option value="front"
                                            <?= isset($editUser) && $editUser->getRole() === 'front' ? 'selected' : ''; ?>>
                                        Frontoffice (client)
                                    </option>
                                    <option value="back"
                                            <?= isset($editUser) && $editUser->getRole() === 'back' ? 'selected' : ''; ?>>
                                        Backoffice (admin)
                                    </option>
                                </select>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        <?= !isset($editUser) || $editUser->isActive() ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($page === 'conversations'): ?>

                <h2 class="mb-3">Conversations</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th><th>Title</th><th>Type</th><th>Created</th><th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($conversations as $c): ?>
                                <tr>
                                    <td><?= $c->getId(); ?></td>
                                    <td><?= htmlspecialchars($c->getTitle()); ?></td>
                                    <td>
                                        <span class="badge <?= $c->isGroup() ? 'bg-info' : 'bg-secondary'; ?>">
                                            <?= $c->isGroup() ? 'Group' : 'DM'; ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($c->getCreatedAt()); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="index.php?c=admin&a=viewConversation&id=<?= $c->getId(); ?>">View</a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="index.php?c=admin&a=conversations&edit_id=<?= $c->getId(); ?>">Edit</a>
                                        <a class="btn btn-sm btn-outline-danger"
                                           href="index.php?c=admin&a=deleteConversation&id=<?= $c->getId(); ?>"
                                           onclick="return confirm('Delete conversation?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?= isset($editConversation) ? 'Edit conversation' : 'Create new conversation'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?c=admin&a=saveConversation">
                            <input type="hidden" name="id"
                                   value="<?= isset($editConversation) ? $editConversation->getId() : ''; ?>">

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input class="form-control" type="text" name="title"
                                       value="<?= isset($editConversation) ? htmlspecialchars($editConversation->getTitle()) : ''; ?>">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_group" id="is_group"
                                        <?= isset($editConversation) && $editConversation->isGroup() ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_group">
                                    Group conversation
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($page === 'conversationDetail'): ?>

                <h2 class="mb-3">Conversation details</h2>

                <div class="card shadow-sm mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($conversation->getTitle()); ?></h5>
                            <p class="mb-0 text-muted">
                                ID: <?= (int)$conversation->getId(); ?> •
                                <?= $conversation->isGroup() ? 'Group' : 'DM'; ?> •
                                Created: <?= htmlspecialchars($conversation->getCreatedAt()); ?>
                            </p>
                        </div>
                        <div>
                            <a class="btn btn-sm btn-outline-danger"
                               href="index.php?c=admin&a=deleteConversation&id=<?= $conversation->getId(); ?>"
                               onclick="return confirm('Delete this conversation?');">
                                Delete conversation
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Participants -->
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Participants</h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($participants as $p): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($p['username']); ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($p['email']); ?></small>
                                            <?php if ($p['is_admin']): ?>
                                                <span class="badge bg-primary ms-1">admin</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-outline-danger"
                                               href="index.php?c=admin&a=removeParticipant&conversation_id=<?= $conversation->getId(); ?>&user_id=<?= (int)$p['user_id']; ?>"
                                               onclick="return confirm('Remove this user from conversation?');">
                                                Remove
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="col-md-8">
                        <div class="card shadow-sm chat-card">
                            <div class="card-body chat-messages" id="messages">
                                <?php foreach ($messages as $m): ?>
                                    <div class="message">
                                        <div class="meta">
                                            <strong><?= htmlspecialchars($m['username']); ?></strong>
                                            <span class="text-muted small ms-2">
                                                <?= htmlspecialchars($m['created_at']); ?>
                                            </span>
                                            <span class="text-muted small ms-2">
                                                (User #<?= (int)$m['user_id']; ?>)
                                            </span>
                                        </div>
                                        <div class="text">
                                            <?= nl2br(htmlspecialchars($m['content'])); ?>
                                        </div>
                                        <div class="actions">
                                            <a class="btn btn-sm btn-link text-danger p-0"
                                               href="index.php?c=admin&a=deleteMessage&id=<?= (int)$m['id']; ?>&from_conversation=<?= (int)$conversation->getId(); ?>"
                                               onclick="return confirm('Delete this message?');">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($page === 'messages'): ?>

                <h2 class="mb-3">Messages (latest)</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th><th>Conversation</th><th>User</th><th>Content</th><th>Created</th><th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($messages as $m): ?>
                                <tr>
                                    <td><?= $m->getId(); ?></td>
                                    <td><?= $m->getConversationId(); ?></td>
                                    <td><?= $m->getUserId(); ?></td>
                                    <td><?= htmlspecialchars($m->getContent()); ?></td>
                                    <td><?= htmlspecialchars($m->getCreatedAt()); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="index.php?c=admin&a=messages&edit_id=<?= $m->getId(); ?>">Edit</a>
                                        <a class="btn btn-sm btn-outline-danger"
                                           href="index.php?c=admin&a=deleteMessage&id=<?= $m->getId(); ?>"
                                           onclick="return confirm('Delete message?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><?= isset($editMessage) ? 'Edit message' : 'Create message'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?c=admin&a=saveMessage">
                            <input type="hidden" name="id"
                                   value="<?= isset($editMessage) ? $editMessage->getId() : ''; ?>">

                            <div class="mb-3">
                                <label class="form-label">Conversation ID</label>
                                <input class="form-control" type="number" name="conversation_id"
                                       value="<?= isset($editMessage) ? $editMessage->getConversationId() : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">User ID</label>
                                <input class="form-control" type="number" name="user_id"
                                       value="<?= isset($editMessage) ? $editMessage->getUserId() : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control" name="content" rows="3"><?= isset($editMessage) ? htmlspecialchars($editMessage->getContent()) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>

            <?php else: ?>

                <div class="alert alert-warning">
                    Unknown admin page.
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
