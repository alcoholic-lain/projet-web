<?php
// model/Conversation.php




require_once __DIR__ . '/../../config/config.php';


class Conversation
{
    private ?int $id = null;
    private string $title = '';
    private bool $is_group = false;
    private string $created_at = '';

    // Getters & Setters (same as before)
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isGroup(): bool
    {
        return $this->is_group;
    }

    public function setIsGroup(bool $is_group): void
    {
        $this->is_group = $is_group;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    private static function fromArray(array $row): self
    {
        $c = new self();
        $c->id = (int)$row['id'];
        $c->title = $row['title'];
        $c->is_group = (bool)$row['is_group'];
        $c->created_at = $row['created_at'];
        return $c;
    }

    public static function findAll(): array
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM conversations ORDER BY id ASC");
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }

    public static function findById(int $id): ?self
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM conversations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    public static function findByUser(int $userId, bool $withDisplayTitle = false): array
    {
        $pdo = config::getConnexion();
        $sql = "SELECT c.id, c.title, c.is_group, cu.is_admin
                FROM conversations c
                JOIN conversation_users cu ON cu.conversation_id = c.id
                WHERE cu.user_id = :uid
                ORDER BY c.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll();

        if ($withDisplayTitle) {
            foreach ($rows as &$row) {
                $conv = self::findById((int)$row['id']);
                $row['display_title'] = $conv ? $conv->getDisplayTitleForUser($userId) : ($row['title'] ?? 'Conversation');
            }
        }
        return $rows;
    }

    public static function userInConversation(int $conversationId, int $userId): bool
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT 1 FROM conversation_users WHERE conversation_id = :cid AND user_id = :uid");
        $stmt->execute([':cid' => $conversationId, ':uid' => $userId]);
        return $stmt->fetch() !== false;
    }

    public static function isUserAdmin(int $conversationId, int $userId): bool
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT is_admin FROM conversation_users WHERE conversation_id = :cid AND user_id = :uid");
        $stmt->execute([':cid' => $conversationId, ':uid' => $userId]);
        $row = $stmt->fetch();
        return $row && (int)$row['is_admin'] === 1;
    }

    public function getParticipants(): array
    {
        if (!$this->id) return [];
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            SELECT u.id AS user_id, u.username, u.email, cu.is_admin
            FROM conversation_users cu
            JOIN user u ON u.id = cu.user_id
            WHERE cu.conversation_id = :cid
            ORDER BY u.username ASC
        ");
        $stmt->execute([':cid' => $this->id]);
        return $stmt->fetchAll();
    }

    public function getDisplayTitleForUser(int $currentUserId): string
    {
        $title = trim($this->title);
        if ($title !== '') return $title;

        $names = array_filter(
            array_column($this->getParticipants(), 'username'),
            fn($name) => $name !== null
        );

        $names = array_values(array_diff($names, [User::findById($currentUserId)?->getUsername()]));

        if (empty($names)) return 'New conversation';
        return count($names) <= 3
            ? implode(', ', $names)
            : implode(', ', array_slice($names, 0, 3)) . ' +' . (count($names) - 3);
    }

    public function addUser(int $userId, bool $isAdmin = false): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO conversation_users (conversation_id, user_id, is_admin)
            VALUES (:cid, :uid, :is_admin)
            ON DUPLICATE KEY UPDATE is_admin = VALUES(is_admin)
        ");
        return $stmt->execute([
            ':cid' => $this->id,
            ':uid' => $userId,
            ':is_admin' => $isAdmin ? 1 : 0
        ]);
    }

    public function removeUser(int $userId): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM conversation_users WHERE conversation_id = :cid AND user_id = :uid");
        return $stmt->execute([':cid' => $this->id, ':uid' => $userId]);
    }

    public function save(): bool
    {
        return $this->id === null ? $this->insert() : $this->update();
    }

    private function insert(): bool
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO conversations (title, is_group) VALUES (:title, :is_group)");
        $ok = $stmt->execute([
            ':title' => $this->title,
            ':is_group' => $this->is_group ? 1 : 0
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

    private function update(): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE conversations SET title = :title, is_group = :is_group WHERE id = :id");
        return $stmt->execute([
            ':title' => $this->title,
            ':is_group' => $this->is_group ? 1 : 0,
            ':id' => $this->id
        ]);
    }

    public function delete(): bool
    {
        if (!$this->id) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM conversations WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
    public static function getMostActive(): ?array
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("
            SELECT 
                c.id,
                c.title,
                c.is_group,
                c.created_at,
                COUNT(DISTINCT m.id) as message_count,
                COUNT(DISTINCT cu.user_id) as participant_count
            FROM conversations c
            LEFT JOIN messages m ON m.conversation_id = c.id
            LEFT JOIN conversation_users cu ON cu.conversation_id = c.id
            GROUP BY c.id
            HAVING message_count > 0
            ORDER BY message_count DESC
            LIMIT 1
        ");

        $row = $stmt->fetch();
        return $row ? $row : null;
    }

}




