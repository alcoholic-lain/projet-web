<?php
// model/Conversation.php

require_once __DIR__ . '/config/config.php';

class Conversation
{
    private ?int $id = null;
    private string $title = '';
    private bool $is_group = false;
    private string $created_at = '';

    // ===== Getters / Setters =====

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function isGroup(): bool { return $this->is_group; }
    public function setIsGroup(bool $is_group): void { $this->is_group = $is_group; }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }

    // ===== Hydration =====

    private static function fromArray(array $row): self
    {
        $c = new self();
        $c->id         = (int)$row['id'];
        $c->title      = $row['title'];
        $c->is_group   = (bool)$row['is_group'];
        $c->created_at = $row['created_at'];
        return $c;
    }

    // ===== Static finders =====

    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM conversations ORDER BY id ASC");
        $rows = $stmt->fetchAll();

        $list = [];
        foreach ($rows as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM conversations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    /**
     * List conversations for a user.
     * If $withDisplayTitle = true, adds 'display_title' key to each row.
     */
    public static function findByUser(int $userId, bool $withDisplayTitle = false): array
    {
        $pdo = Database::getConnection();
        $sql = "
            SELECT c.id, c.title, c.is_group, cu.is_admin
            FROM conversations c
            JOIN conversation_users cu ON cu.conversation_id = c.id
            WHERE cu.user_id = :uid
            ORDER BY c.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll();

        if ($withDisplayTitle) {
            foreach ($rows as &$row) {
                $conv = self::findById((int)$row['id']);
                if ($conv) {
                    $row['display_title'] = $conv->getDisplayTitleForUser($userId);
                } else {
                    $row['display_title'] = $row['title'] ?? 'Conversation';
                }
            }
        }

        return $rows;
    }

    public static function userInConversation(int $conversationId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt
            FROM conversation_users
            WHERE conversation_id = :cid AND user_id = :uid
        ");
        $stmt->execute([
            ':cid' => $conversationId,
            ':uid' => $userId,
        ]);
        $row = $stmt->fetch();
        return $row && (int)$row['cnt'] > 0;
    }

    /**
     * Check if user is admin in a conversation.
     */
    public static function isUserAdmin(int $conversationId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT is_admin
            FROM conversation_users
            WHERE conversation_id = :cid AND user_id = :uid
        ");
        $stmt->execute([
            ':cid' => $conversationId,
            ':uid' => $userId,
        ]);
        $row = $stmt->fetch();
        return $row && (int)$row['is_admin'] === 1;
    }

    // Participants with user info
    public function getParticipants(): array
    {
        if ($this->id === null) return [];

        $pdo = Database::getConnection();
        $sql = "
            SELECT u.id AS user_id, u.username, u.email, cu.is_admin
            FROM conversation_users cu
            JOIN users u ON u.id = cu.user_id
            WHERE cu.conversation_id = :cid
            ORDER BY u.username ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cid' => $this->id]);
        return $stmt->fetchAll();
    }

    /**
     * Display title depending on current user:
     * - If a title is set (non-empty) => use it.
     * - Else => show other participants' usernames (exclude current user).
     */
    public function getDisplayTitleForUser(int $currentUserId): string
    {
        $baseTitle = trim($this->title);
        if ($baseTitle !== '') {
            return $baseTitle;
        }

        $participants = $this->getParticipants();
        $names = [];
        foreach ($participants as $p) {
            if ((int)$p['user_id'] !== $currentUserId) {
                $names[] = $p['username'];
            }
        }

        if (empty($names)) {
            return 'New conversation';
        }

        if (count($names) <= 3) {
            return implode(', ', $names);
        }

        return implode(', ', array_slice($names, 0, 3)) . ' +' . (count($names) - 3);
    }

    // ===== Manage participants =====

    public function addUser(int $userId, bool $isAdmin = false): bool
    {
        if ($this->id === null) return false;

        $pdo = Database::getConnection();
        $sql = "
            INSERT INTO conversation_users (conversation_id, user_id, is_admin)
            VALUES (:cid, :uid, :is_admin)
            ON DUPLICATE KEY UPDATE is_admin = VALUES(is_admin)
        ";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':cid'      => $this->id,
            ':uid'      => $userId,
            ':is_admin' => $isAdmin ? 1 : 0,
        ]);
    }

    public function removeUser(int $userId): bool
    {
        if ($this->id === null) return false;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            DELETE FROM conversation_users
            WHERE conversation_id = :cid AND user_id = :uid
        ");
        return $stmt->execute([
            ':cid' => $this->id,
            ':uid' => $userId,
        ]);
    }

    // ===== CRUD =====

    public function save(): bool
    {
        return $this->id === null ? $this->insert() : $this->update();
    }

    private function insert(): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO conversations (title, is_group)
            VALUES (:title, :is_group)
        ");
        $ok = $stmt->execute([
            ':title'    => $this->title,
            ':is_group' => $this->is_group ? 1 : 0,
        ]);

        if ($ok) {
            $this->id = (int)$pdo->lastInsertId();
        }
        return $ok;
    }

    private function update(): bool
    {
        if ($this->id === null) return false;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE conversations
            SET title = :title,
                is_group = :is_group
            WHERE id = :id
        ");
        return $stmt->execute([
            ':title'    => $this->title,
            ':is_group' => $this->is_group ? 1 : 0,
            ':id'       => $this->id,
        ]);
    }

    public function delete(): bool
    {
        if ($this->id === null) return false;
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM conversations WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}
