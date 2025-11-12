<?php
class Message {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getByConversation($convId) {
        $sql = "SELECT m.*, u.name, u.profile_picture
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.conversation_id = ?
                ORDER BY m.sent_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$convId]);
        return $stmt->fetchAll();
    }

    public function create($senderId, $convId, $text) {
        $sql = "INSERT INTO messages (sender_id, conversation_id, message_text)
                VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$senderId, $convId, $text]);
    }
}