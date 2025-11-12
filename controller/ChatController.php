<?php
// controller/ChatController.php

require __DIR__ . '/../model/Conversation.php';
require __DIR__ . '/../model/Message.php';
require __DIR__ . '/../model/User.php';

class ChatController {
    private $pdo, $conv, $msg, $user;

    public function __construct($pdo) {
        $this->pdo  = $pdo;
        $this->conv = new Conversation($pdo);
        $this->msg  = new Message($pdo);
        $this->user = new User($pdo);
    }

    public function handle() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'lain';
        }

        // ALWAYS load conversations
        $convs = $this->conv->getForUser($_SESSION['user_id']);
        $conv = $msgs = $members = null;

        $action = $_GET['a'] ?? 'list';

        if ($action === 'send') {
            $this->sendMessage();
            $convId = (int)($_POST['conv_id'] ?? 0);
            header("Location: " . __DIR__ . "/../view/F/index.php?a=view&id=$convId");
            exit;
        }

        if ($action === 'view') {
            $convId = (int)($_GET['id'] ?? 0);
            if ($convId) {
                $conv = $this->conv->find($convId);
                if ($conv) {
                    $msgs    = $this->msg->getByConversation($convId);
                    $members = $this->conv->getMembers($convId);
                }
            }
        }

        $this->render($convs, $conv, $msgs, $members);
    }

    private function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text   = trim($_POST['text'] ?? '');
            $convId = (int)($_POST['conv_id'] ?? 0);
            if ($text && $convId) {
                $this->msg->create($_SESSION['user_id'], $convId, $text);
            }
        }
    }

    private function render($convs, $conv, $msgs, $members) {
        $convs   = is_array($convs) ? $convs : [];
        $msgs    = is_array($msgs) ? $msgs : [];
        $members = is_array($members) ? $members : [];

        require __DIR__ . '/../view/F/index.html';
    }
}