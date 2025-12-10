<?php
// model/Message.php

require_once __DIR__ . '/../../config/config.php';

class Message
{
    private ?int $id = null;
    private int $conversation_id = 0;
    private int $user_id = 0;
    private string $content = '';
    private string $created_at = '';
    private array $reactions = []; // emoji => [userId1, userId2, ...]
    private ?int $reply_to_id = null;

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConversationId(): int
    {
        return $this->conversation_id;
    }

    public function setConversationId(int $cid): void
    {
        $this->conversation_id = $cid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $uid): void
    {
        $this->user_id = $uid;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getReactions(): array
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): void
    {
        $this->reactions = $reactions;
    }

    public function getReplyToId(): ?int
    {
        return $this->reply_to_id;
    }

    public function setReplyToId(?int $reply_to_id): void
    {
        $this->reply_to_id = $reply_to_id;
    }

    private static function fromArray(array $row): self
    {
        $m = new self();
        $m->id = (int)$row['id'];
        $m->conversation_id = (int)$row['conversation_id'];
        $m->user_id = (int)$row['user_id'];
        $m->content = $row['content'];
        $m->created_at = $row['created_at'];
        $m->reply_to_id = isset($row['reply_to_id']) ? (int)$row['reply_to_id'] : null;

        // Parse reactions from JSON
        if (!empty($row['reaction'])) {
            $reactions = json_decode($row['reaction'], true);
            $m->reactions = is_array($reactions) ? $reactions : [];
        } else {
            $m->reactions = [];
        }

        return $m;
    }

    public static function findAll(int $limit = 200): array
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }

    public static function findById(int $id): ?self
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    public static function findByConversation(int $conversationId, int $limit = 200): array
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            SELECT m.*, u.username
            FROM messages m
            JOIN user u ON u.id = m.user_id
            WHERE m.conversation_id = :cid
            ORDER BY m.created_at ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function save(): bool
    {
        return $this->id === null ? $this->insert() : $this->update();
    }

    private function insert(): bool
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO messages (conversation_id, user_id, content, reaction, reply_to_id)
            VALUES (:conversation_id, :user_id, :content, :reaction, :reply_to_id)
        ");
        $ok = $stmt->execute([
            ':conversation_id' => $this->conversation_id,
            ':user_id' => $this->user_id,
            ':content' => $this->content,
            ':reaction' => json_encode($this->reactions),
            ':reply_to_id' => $this->reply_to_id
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

    private function update(): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            UPDATE messages 
            SET content = :content, 
                reaction = :reaction,
                reply_to_id = :reply_to_id
            WHERE id = :id
        ");
        return $stmt->execute([
            ':content' => $this->content,
            ':reaction' => json_encode($this->reactions),
            ':reply_to_id' => $this->reply_to_id,
            ':id' => $this->id
        ]);
    }

    public function delete(): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}