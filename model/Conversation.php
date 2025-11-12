<?php
class Conversation {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getForUser($userId) {
        $sql = "SELECT c.*, cm.joined_at
                FROM conversations c
                JOIN conversation_members cm ON c.id = cm.conversation_id
                WHERE cm.user_id = :uid
                ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM conversations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getMembers($convId) {
        $sql = "SELECT u.* FROM users u
                JOIN conversation_members cm ON u.id = cm.user_id
                WHERE cm.conversation_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$convId]);
        return $stmt->fetchAll();
    }
}