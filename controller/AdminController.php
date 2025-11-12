<?php
require __DIR__ . '/../model/User.php';

class AdminController {
    private $pdo, $user;

    public function __construct($pdo) {
        $this->pdo  = $pdo;
        $this->user = new User($pdo);
    }

    public function run() {
        session_start();
        $_SESSION['admin'] = true;

        $act = $_GET['act'] ?? 'users';
        switch ($act) {
            case 'users': $this->listUsers(); break;
            case 'delete': $this->deleteUser(); break;
            default: $this->listUsers();
        }
    }

    private function listUsers() {
        $users = $this->user->all();
        require __DIR__ . '/../view/B/index.html';
    }

    private function deleteUser() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) $this->user->delete($id);
        header('Location: ' . __DIR__ . '/../view/B/index.php');
        exit;
    }
}