<?php
// controller/COMS/ChatController.php

require_once __DIR__ . '/Coms_Config.php';

class ChatController
{
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id']) or $_SESSION['user_id'] == "0") {
            header('Location: index.php?c=logC&a=index');
            exit;
        }
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?c=chatC&a=index');
            exit;
        }

        $user = User::findById((int)$_SESSION['user_id']);
        if (!$user || $user->getRole() !== 'back') {
            header('Location: index.php?c=chatC&a=listConversations');
            exit;
        }
    }

    /**
     * Helper method to render views with DMPopup.js
     */
    private function renderView($page, $data = [])
    {
        // Extract data to variables
        extract($data);

        echo '<style>'. file_get_contents(__DIR__ . '/../../../view/Client/assets/css/style.css') .'</style>';
        require __DIR__ . COMS2F_PATH;
        echo '<script>'.file_get_contents(__DIR__ . '/../../../view/Client/assets/js/DMPopup.js') . ' </script>';
    }

    // ===== AUTH =====

    public function index()
    {
        $this->requireLogin();

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?c=chatC&a=listConversations');
            exit;
        }

        $page = 'auth';
        $this->renderView($page);
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
            $this->renderView($page, compact('authErrors'));
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
            header('Location: index.php?c=chatC&a=adminIndex');
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
            $this->renderView($page, compact('authErrors'));
            return;
        }

        $_SESSION['user_id']  = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role']     = $user->getRole();

        if ($user->getRole() === 'back') {
            header('Location: index.php?c=chatC&a=adminIndex');
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
        $this->renderView($page, compact('conversations'));
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
     * Toggle reaction on a message
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
            $this->renderView($page, compact('error'));
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
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
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
        $this->renderView($page, compact('conversation', 'messages', 'participants', 'conversations', 'displayTitle'));
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

        // Handle AJAX request from DMPopup.js
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

            header('Content-Type: application/json');

            $userId = (int)$_SESSION['user_id'];

            // Get JSON data or form data
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                // Fallback to POST
                $data = $_POST;
            }

            $title = trim($data['title'] ?? '');
            $isGroup = isset($data['is_group']) && $data['is_group'] == '1';

            // Get user IDs from JSON string
            $userIdsJson = $data['user_ids'] ?? '[]';
            $participantIds = json_decode($userIdsJson, true);

            if (!is_array($participantIds)) {
                $participantIds = [];
            }

            $participantIds = array_map('intval', $participantIds);

            // Remove current user and duplicates
            $clean = [];
            foreach ($participantIds as $pid) {
                if ($pid > 0 && $pid !== $userId && !in_array($pid, $clean, true)) {
                    $clean[] = $pid;
                }
            }
            $participantIds = $clean;

            if (empty($participantIds)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please select at least one other user.'
                ]);
                exit;
            }

            try {
                // Create conversation
                $conv = new Conversation();

                // Set title - if no title provided for DM, leave empty for auto-generation
                if ($isGroup || !empty($title)) {
                    $conv->setTitle($title);
                }

                $conv->setIsGroup($isGroup || count($participantIds) > 1);
                $conv->save();

                // Add current user as admin
                $conv->addUser($userId, true);

                // Add selected participants
                foreach ($participantIds as $pid) {
                    $conv->addUser($pid, false);
                }

                // Get display title for response
                $displayTitle = $conv->getDisplayTitleForUser($userId);
                if (empty($displayTitle)) {
                    // Fallback: get first participant's username
                    $firstParticipant = User::findById($participantIds[0]);
                    $displayTitle = $firstParticipant ? $firstParticipant->getUsername() : 'New Chat';
                }

                echo json_encode([
                    'success' => true,
                    'conversation' => [
                        'id' => $conv->getId(),
                        'title' => $displayTitle,
                        'is_group' => $conv->isGroup()
                    ]
                ]);
                exit;

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error creating conversation: ' . $e->getMessage()
                ]);
                exit;
            }
        }

        // Regular GET request - show page (if you still need this)
        $userId = (int)$_SESSION['user_id'];
        $conversations = Conversation::findByUser($userId, true);
        $page = 'newConversation';
        $this->renderView($page, compact('conversations'));
    }

    // ===== ADMIN SECTION =====

    /**
     * Admin Dashboard
     */
    public function adminIndex()
    {
        $this->requireAdmin();

        $pdo = config::getConnexion();

        // Existing counts
        $userCount = (int)$pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
        $conversationCount = (int)$pdo->query("SELECT COUNT(*) FROM conversations")->fetchColumn();
        $messageCount = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

        // Get most active user and conversation
        $mostActiveUser = User::getMostActive();
        $mostActiveConversation = Conversation::getMostActive();

        $page = 'dashboard';
        require __DIR__ . COMS2B_PATH;
    }

    // ===== ADMIN: USERS =====

    public function adminUsers()
    {
        $this->requireAdmin();
        $users = User::findAll();

        $editUser = null;
        if (isset($_GET['edit_id'])) {
            $editUser = User::findById((int)$_GET['edit_id']);
        }

        $page = 'users';
        require __DIR__ . COMS2B_PATH;
    }

    public function adminSaveUser()
    {
        $this->requireAdmin();

        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $is_active = isset($_POST['is_active']);
        $role = $_POST['role'] ?? 'front';

        if ($id > 0) {
            $user = User::findById($id);
        } else {
            $user = new User();
        }

        if ($user) {
            $user->setUsername($username);
            $user->setEmail($email);

            if ($password !== '') {
                $user->setPasswordHash($password);
            } elseif ($id === 0) {
                $user->setPasswordHash('123456'); // default demo
            }

            $user->setIsActive($is_active);
            $user->setRole($role);
            $user->save();
        }

        header('Location: index.php?c=chatC&a=adminUsers');
        exit;
    }

    public function adminDeleteUser()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $user = User::findById($id);
        if ($user) {
            $user->delete();
        }
        header('Location: index.php?c=chatC&a=adminUsers');
        exit;
    }

    // ===== ADMIN: CONVERSATIONS =====

    public function adminConversations()
    {
        $this->requireAdmin();
        $conversations = Conversation::findAll();

        $editConversation = null;
        if (isset($_GET['edit_id'])) {
            $editConversation = Conversation::findById((int)$_GET['edit_id']);
        }

        $page = 'conversations';
        require __DIR__ . COMS2B_PATH;
    }

    public function adminSaveConversation()
    {
        $this->requireAdmin();

        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $is_group = isset($_POST['is_group']);

        if ($id > 0) {
            $conv = Conversation::findById($id);
        } else {
            $conv = new Conversation();
        }

        if ($conv) {
            $conv->setTitle($title);
            $conv->setIsGroup($is_group);
            $conv->save();
        }

        header('Location: index.php?c=chatC&a=adminConversations');
        exit;
    }

    public function adminDeleteConversation()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $conv = Conversation::findById($id);
        if ($conv) {
            $conv->delete();
        }
        header('Location: index.php?c=chatC&a=adminConversations');
        exit;
    }

    public function adminViewConversation()
    {
        $this->requireAdmin();

        $conversationId = (int)($_GET['id'] ?? 0);
        $conversation = Conversation::findById($conversationId);
        if (!$conversation) {
            header('Location: index.php?c=chatC&a=adminConversations');
            exit;
        }

        $participants = $conversation->getParticipants();
        $messages = Message::findByConversation($conversationId, 500);

        $page = 'conversationDetail';
        require __DIR__ . COMS2B_PATH;
    }

    public function adminRemoveParticipant()
    {
        $this->requireAdmin();

        $conversationId = (int)($_GET['conversation_id'] ?? 0);
        $userId = (int)($_GET['user_id'] ?? 0);

        $conversation = Conversation::findById($conversationId);
        if ($conversation) {
            $conversation->removeUser($userId);
        }

        header('Location: index.php?c=chatC&a=adminViewConversation&id=' . $conversationId);
        exit;
    }

    // ===== ADMIN: MESSAGES =====

    public function adminMessages()
    {
        $this->requireAdmin();
        $messages = Message::findAll(200);

        $editMessage = null;
        if (isset($_GET['edit_id'])) {
            $editMessage = Message::findById((int)$_GET['edit_id']);
        }

        $page = 'messages';
        require __DIR__ . COMS2B_PATH;
    }

    public function adminSaveMessage()
    {
        $this->requireAdmin();

        $id = (int)($_POST['id'] ?? 0);
        $conversationId = (int)($_POST['conversation_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($id > 0) {
            $msg = Message::findById($id);
        } else {
            $msg = new Message();
        }

        if ($msg) {
            $msg->setConversationId($conversationId);
            $msg->setUserId($userId);
            $msg->setContent($content);
            $msg->save();
        }

        header('Location: index.php?c=chatC&a=adminMessages');
        exit;
    }

    public function adminDeleteMessage()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $fromConversation = isset($_GET['from_conversation']) ? (int)$_GET['from_conversation'] : 0;

        $msg = Message::findById($id);
        if ($msg) {
            $msg->delete();
        }

        if ($fromConversation > 0) {
            header('Location: index.php?c=chatC&a=adminViewConversation&id=' . $fromConversation);
        } else {
            header('Location: index.php?c=chatC&a=adminMessages');
        }
        exit;
    }

    // ===== ADMIN: AI/ORBIT =====

    public function adminOrbit()
    {
        $this->requireAdmin();
        $page = 'orbit';
        require __DIR__ . COMS2B_PATH;
    }
}


