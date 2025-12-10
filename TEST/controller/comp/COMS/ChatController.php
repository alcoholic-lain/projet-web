<?php
// controller/comp/COMS/ChatController.php

require_once __DIR__ . '/Coms_Config.php';

class ChatController
{
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?c=logC&a=index');
            exit;
        }
    }

    // ===== AUTH =====

    public function index()
    {
        $this->requireLogin();

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?c=chatC&a=listConversations');
            echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
            require __DIR__ . COMS2F_PATH;
            echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
            exit;
        }

        $page = 'auth';
        echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
        require __DIR__ . COMS2F_PATH;
        echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
    }

    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = $_POST['role'] ?? 'front';

        $errors = [];
        if ($username === '' || $email === '' || $password === '') {
            $errors[] = 'All fields are required.';
        }

        if (User::findByEmail($email)) {
            $errors[] = 'Email is already used.';
        }

        if (!empty($errors)) {
            $page = 'auth';
            $authErrors = $errors;
            require __DIR__ . COMS2F_PATH;
            return;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPasswordHash($password);
        $user->setRole($role);
        $user->setIsActive(true);
        $user->save();

        $_SESSION['user_id']  = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role']     = $user->getRole();

        if ($user->getRole() === 'back') {
            header('Location: index.php?c=chatA&a=index');
        } else {
            header('Location: index.php?c=chatC&a=listConversations');
        }
        exit;
    }

    public function login()
    {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $user   = User::findByEmail($email);
        $errors = [];

        if (!$user || !$user->isActive()) {
            $errors[] = 'Invalid credentials.';
        } else {
            if ($user->getPasswordHash() !== $password) {
                $errors[] = 'Invalid credentials.';
            }
        }

        if (!empty($errors)) {
            $page = 'auth';
            $authErrors = $errors;

            echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
            require __DIR__ . COMS2F_PATH;
            echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
            return;
        }

        $_SESSION['user_id']  = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role']     = $user->getRole();

        if ($user->getRole() === 'back') {
            header('Location: index.php?c=chatA&a=index');
        } else {
            header('Location: index.php?c=chatC&a=listConversations');
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: index.php?c=chatC&a=index');
        exit;
    }

    // ===== CHAT PAGES =====

    public function listConversations()
    {
        $this->requireLogin();

        $userId        = (int)$_SESSION['user_id'];
        $conversations = Conversation::findByUser($userId, true);

        $page = 'listConversations';

        echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
        require __DIR__ . COMS2F_PATH;
        echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
    }

    /**
     * AJAX endpoint to search users
     */
    public function searchUsers()
    {
        $this->requireLogin();

        header('Content-Type: application/json');

        $userId = (int)$_SESSION['user_id'];
        $query = trim($_GET['q'] ?? '');

        if ($query === '' || strlen($query) < 2) {
            echo json_encode(['success' => false, 'message' => 'Query too short']);
            exit;
        }

        try {
            $users = User::search($query, $userId);

            $formattedUsers = array_map(function($user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()
                ];
            }, $users);

            echo json_encode([
                'success' => true,
                'users' => $formattedUsers
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error searching users'
            ]);
        }
        exit;
    }

    /**
     * AJAX endpoint to get messages for a conversation
     */
    public function getMessages()
    {
        $this->requireLogin();

        header('Content-Type: application/json');

        $userId         = (int)$_SESSION['user_id'];
        $conversationId = (int)($_GET['id'] ?? 0);

        if (!Conversation::userInConversation($conversationId, $userId)) {
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $messages = Message::findByConversation($conversationId, 200);

        $formattedMessages = array_map(function($msg) use ($userId) {
            // Parse reactions from JSON
            $reactions = [];
            if (!empty($msg['reaction'])) {
                $reactions = json_decode($msg['reaction'], true) ?? [];
            }

            return [
                'id' => $msg['id'],
                'content' => $msg['content'],
                'user_id' => $msg['user_id'],
                'username' => $msg['username'],
                'created_at' => $msg['created_at'],
                'is_own' => (int)$msg['user_id'] === $userId,
                'reactions' => $reactions,
                'reply_to_id' => $msg['reply_to_id']
            ];
        }, $messages);

        echo json_encode([
            'success' => true,
            'messages' => $formattedMessages
        ]);
        exit;
    }

    /**
     * NEW: Toggle reaction on a message
     */
    public function toggleReaction()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId = (int)$_SESSION['user_id'];
        $messageId = (int)($_POST['message_id'] ?? 0);
        $emoji = trim($_POST['emoji'] ?? '');

        if (!$messageId || !$emoji) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        try {
            $message = Message::findById($messageId);
            if (!$message) {
                echo json_encode(['success' => false, 'message' => 'Message not found']);
                exit;
            }

            // Check if user has access to this conversation
            $conversationId = $message->getConversationId();
            if (!Conversation::userInConversation($conversationId, $userId)) {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }

            // Get current reactions
            $reactions = $message->getReactions();

            // Toggle user's reaction for this emoji
            if (!isset($reactions[$emoji])) {
                $reactions[$emoji] = [];
            }

            $userIdStr = (string)$userId;
            $key = array_search($userIdStr, $reactions[$emoji]);

            if ($key !== false) {
                // Remove reaction
                unset($reactions[$emoji][$key]);
                $reactions[$emoji] = array_values($reactions[$emoji]); // Re-index

                // Remove emoji key if no users reacted with it
                if (empty($reactions[$emoji])) {
                    unset($reactions[$emoji]);
                }
            } else {
                // Add reaction
                $reactions[$emoji][] = $userIdStr;
            }

            // Update message
            $message->setReactions($reactions);
            $message->save();

            echo json_encode([
                'success' => true,
                'reactions' => $reactions
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error toggling reaction: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Handles send/edit/delete + displays chat
     */
    public function conversation()
    {
        $this->requireLogin();

        $userId         = (int)$_SESSION['user_id'];
        $conversationId = (int)($_GET['id'] ?? 0);

        if (!Conversation::userInConversation($conversationId, $userId)) {
            $error = "You are not a member of this conversation.";
            $page = 'error';

            echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
            require __DIR__ . COMS2F_PATH;
            echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
            return;
        }

        // POST = send / edit / delete
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode = $_POST['mode'] ?? '';

            if ($mode === 'send') {
                $content = trim($_POST['content'] ?? '');
                $replyToId = isset($_POST['reply_to_id']) ? (int)$_POST['reply_to_id'] : null;

                if ($content !== '') {
                    $m = new Message();
                    $m->setConversationId($conversationId);
                    $m->setUserId($userId);
                    $m->setContent($content);
                    if ($replyToId) {
                        $m->setReplyToId($replyToId);
                    }
                    $m->save();

                    // Return message ID for WebSocket
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message_id' => $m->getId()
                        ]);
                        exit;
                    }
                }
            } elseif ($mode === 'edit') {
                $msgId   = (int)($_POST['message_id'] ?? 0);
                $content = trim($_POST['content'] ?? '');
                $msg     = Message::findById($msgId);
                if ($msg && $msg->getUserId() === $userId && $content !== '') {
                    $msg->setContent($content);
                    $msg->save();

                    // Return success for WebSocket
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true]);
                        exit;
                    }
                }
            } elseif ($mode === 'delete') {
                $msgId = (int)($_POST['message_id'] ?? 0);
                $msg   = Message::findById($msgId);
                if ($msg && $msg->getUserId() === $userId) {
                    $msg->delete();

                    // Return success for WebSocket
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true]);
                        exit;
                    }
                }
            }

            header('Location: index.php?c=chatC&a=conversation&id=' . $conversationId);
            exit;
        }

        // GET: show chat UI
        $conversation  = Conversation::findById($conversationId);
        $messages      = Message::findByConversation($conversationId, 200);
        $participants  = $conversation->getParticipants();
        $conversations = Conversation::findByUser($userId, true);
        $displayTitle  = $conversation->getDisplayTitleForUser($userId);

        $page = 'conversation';
        echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
        require __DIR__ . COMS2F_PATH;
        echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
    }

    /**
     * Rename conversation (only admin in that conversation).
     */
    public function renameConversation()
    {
        $this->requireLogin();

        $conversationId = (int)($_GET['id'] ?? 0);
        $userId         = (int)$_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=chatC&a=conversation&id=' . $conversationId);
            exit;
        }

        if (!Conversation::userInConversation($conversationId, $userId)
            || !Conversation::isUserAdmin($conversationId, $userId)) {
            header('Location: index.php?c=chatC&a=conversation&id=' . $conversationId);
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $conv = Conversation::findById($conversationId);
        if ($conv) {
            $conv->setTitle($title);
            $conv->save();
        }

        header('Location: index.php?c=chatC&a=conversation&id=' . $conversationId);
        exit;
    }

    /**
     * Page to search users and create new conversations (DM or group).
     */
    public function newConversation()
    {
        $this->requireLogin();

        $userId        = (int)$_SESSION['user_id'];
        $searchTerm    = '';
        $searchResults = [];
        $errorCreate   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode       = $_POST['mode'] ?? '';
            $searchTerm = trim($_POST['q'] ?? '');

            if ($searchTerm !== '') {
                $searchResults = User::search($searchTerm, $userId);
            }

            if ($mode === 'create') {
                $title    = trim($_POST['title'] ?? '');
                $is_group = isset($_POST['is_group']);

                $participantIds = isset($_POST['participants']) ? (array)$_POST['participants'] : [];
                $participantIds = array_map('intval', $participantIds);

                $clean = [];
                foreach ($participantIds as $pid) {
                    if ($pid > 0 && $pid !== $userId && !in_array($pid, $clean, true)) {
                        $clean[] = $pid;
                    }
                }
                $participantIds = $clean;

                if (empty($participantIds)) {
                    $errorCreate = 'Please select at least one other user.';
                } else {
                    $conv = new Conversation();
                    $conv->setTitle($title);
                    $conv->setIsGroup($is_group || count($participantIds) > 1);
                    $conv->save();

                    $conv->addUser($userId, true);
                    foreach ($participantIds as $pid) {
                        $conv->addUser($pid, false);
                    }

                    header('Location: index.php?c=chatC&a=conversation&id=' . $conv->getId());
                    exit;
                }
            }
        }

        $conversations = Conversation::findByUser($userId, true);
        $page = 'newConversation';

        echo '<style>'. file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/css/style.css') .'</style>';
        require __DIR__ . COMS2F_PATH;
        echo '<script>'.file_get_contents(__DIR__ . '/../../../view/F/comp/COMS/assets/js/chat.js') . ' </script>';
    }
}