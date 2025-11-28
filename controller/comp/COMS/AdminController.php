<?php




require_once __DIR__ . '/Coms_Config.php';


class AdminController
{
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

    public function index()
    {
        $this->requireAdmin();

        // Replaced Database::getConnection() with config::getConnexion()
        $pdo = config::getConnexion();

        $userCount = (int)$pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
        $conversationCount = (int)$pdo->query("SELECT COUNT(*) FROM conversations")->fetchColumn();
        $messageCount = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

        $page = 'dashboard';
        require __DIR__ . COMS2B_PATH;
    }

    // ===== USERS =====

    public function users()
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

    public function saveUser()
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

        header('Location: index.php?c=chatA&a=users');
        exit;
    }

    public function deleteUser()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $user = User::findById($id);
        if ($user) {
            $user->delete();
        }
        header('Location: index.php?c=chatA&a=users');
        exit;
    }

    // ===== CONVERSATIONS =====

    public function conversations()
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

    public function saveConversation()
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

        header('Location: index.php?c=chatA&a=conversations');
        exit;
    }

    public function deleteConversation()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $conv = Conversation::findById($id);
        if ($conv) {
            $conv->delete();
        }
        header('Location: index.php?c=chatA&a=conversations');
        exit;
    }

    public function viewConversation()
    {
        $this->requireAdmin();

        $conversationId = (int)($_GET['id'] ?? 0);
        $conversation = Conversation::findById($conversationId);
        if (!$conversation) {
            header('Location: index.php?c=chatA&a=conversations');
            exit;
        }

        $participants = $conversation->getParticipants();
        $messages = Message::findByConversation($conversationId, 500);

        $page = 'conversationDetail';
        require __DIR__ . COMS2B_PATH;
    }

    public function removeParticipant()
    {
        $this->requireAdmin();

        $conversationId = (int)($_GET['conversation_id'] ?? 0);
        $userId = (int)($_GET['user_id'] ?? 0);

        $conversation = Conversation::findById($conversationId);
        if ($conversation) {
            $conversation->removeUser($userId);
        }

        header('Location: index.php?c=chatA&a=viewConversation&id=' . $conversationId);
        exit;
    }

    // ===== MESSAGES =====

    public function messages()
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

    public function saveMessage()
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

        header('Location: index.php?c=chatA&a=messages');
        exit;
    }

    public function deleteMessage()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $fromConversation = isset($_GET['from_conversation']) ? (int)$_GET['from_conversation'] : 0;

        $msg = Message::findById($id);
        if ($msg) {
            $msg->delete();
        }

        if ($fromConversation > 0) {
            header('Location: index.php?c=chatA&a=viewConversation&id=' . $fromConversation);
        } else {
            header('Location: index.php?c=chatA&a=messages');
        }
        exit;
    }
}

