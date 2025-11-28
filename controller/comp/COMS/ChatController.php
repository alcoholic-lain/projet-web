<?php
// controller/chatCController.php

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

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?c=chatC&a=listConversations');
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
        // Demo only: plain password. Real app: password_hash().
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
            // Demo only: plain compare
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

    // Handles send/edit/delete + displays chat
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
            $mode = $_POST['mode'] ?? ''; // send | edit | delete

            if ($mode === 'send') {
                $content = trim($_POST['content'] ?? '');
                if ($content !== '') {
                    $m = new Message();
                    $m->setConversationId($conversationId);
                    $m->setUserId($userId);
                    $m->setContent($content);
                    $m->save();
                }
            } elseif ($mode === 'edit') {
                $msgId   = (int)($_POST['message_id'] ?? 0);
                $content = trim($_POST['content'] ?? '');
                $msg     = Message::findById($msgId);
                if ($msg && $msg->getUserId() === $userId && $content !== '') {
                    $msg->setContent($content);
                    $msg->save();
                }
            } elseif ($mode === 'delete') {
                $msgId = (int)($_POST['message_id'] ?? 0);
                $msg   = Message::findById($msgId);
                if ($msg && $msg->getUserId() === $userId) {
                    $msg->delete();
                }
            }

            header('Location: index.php?c=chatC&a=conversation&id=' . $conversationId);
            exit;
        }

        // GET: show chat UI
        $conversation  = Conversation::findById($conversationId);
        $messages      = Message::findByConversation($conversationId, 200);
        $participants  = $conversation->getParticipants();
        $conversations = Conversation::findByUser($userId, true); // sidebar
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
        // Empty title allowed: will fall back to "other members" logic.
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
                $title    = trim($_POST['title'] ?? ''); // may be empty
                $is_group = isset($_POST['is_group']);

                $participantIds = isset($_POST['participants']) ? (array)$_POST['participants'] : [];
                $participantIds = array_map('intval', $participantIds);

                // Clean participant IDs: unique, >0, not current user
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
                    // If title is '', we'll dynamically display other members later.
                    $conv->setTitle($title);
                    // Consider group if checkbox checked OR more than one participant
                    $conv->setIsGroup($is_group || count($participantIds) > 1);
                    $conv->save();

                    // Add current user as admin
                    $conv->addUser($userId, true);
                    // Add selected users
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




