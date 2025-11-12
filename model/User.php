<?php
class User {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function all() {
        return $this->pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}