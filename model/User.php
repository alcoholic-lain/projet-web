<?php
// model/User.php

require_once __DIR__ . '/config/config.php';

class User
{
    private ?int $id = null;
    private string $username = '';
    private string $email = '';
    private string $password_hash = '';
    private string $role = 'front'; // 'front' or 'back'
    private string $created_at = '';
    private bool $is_active = true;

    // ===== Getters / Setters =====

    public function getId(): ?int { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getPasswordHash(): string { return $this->password_hash; }
    public function setPasswordHash(string $hash): void { $this->password_hash = $hash; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void
    {
        $role = strtolower($role);
        if (!in_array($role, ['front', 'back'], true)) {
            $role = 'front';
        }
        $this->role = $role;
    }

    public function getCreatedAt(): string { return $this->created_at; }
    public function setCreatedAt(string $created_at): void { $this->created_at = $created_at; }

    public function isActive(): bool { return $this->is_active; }
    public function setIsActive(bool $is_active): void { $this->is_active = $is_active; }

    // ===== Hydration =====

    private static function fromArray(array $row): self
    {
        $u = new self();
        $u->id            = (int)$row['id'];
        $u->username      = $row['username'];
        $u->email         = $row['email'];
        $u->password_hash = $row['password_hash'];
        $u->role          = $row['role'] ?? 'front';
        $u->created_at    = $row['created_at'];
        $u->is_active     = (bool)$row['is_active'];
        return $u;
    }

    // ===== Static finders =====

    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
        $rows = $stmt->fetchAll();

        $users = [];
        foreach ($rows as $row) {
            $users[] = self::fromArray($row);
        }
        return $users;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    /**
     * Search users by username or email (for starting conversations).
     * Excludes the current user.
     */
    public static function search(string $term, int $excludeUserId = 0): array
    {
        $pdo = Database::getConnection();
        $sql = "
            SELECT * FROM users
            WHERE (username LIKE :term OR email LIKE :term)
        ";
        if ($excludeUserId > 0) {
            $sql .= " AND id <> :exclude";
        }
        $sql .= " ORDER BY username ASC LIMIT 50";

        $stmt = $pdo->prepare($sql);
        $like = '%' . $term . '%';
        $stmt->bindValue(':term', $like, PDO::PARAM_STR);
        if ($excludeUserId > 0) {
            $stmt->bindValue(':exclude', $excludeUserId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $users = [];
        foreach ($rows as $row) {
            $users[] = self::fromArray($row);
        }
        return $users;
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
            INSERT INTO users (username, email, password_hash, role, is_active)
            VALUES (:username, :email, :password_hash, :role, :is_active)
        ");

        $ok = $stmt->execute([
            ':username'      => $this->username,
            ':email'         => $this->email,
            ':password_hash' => $this->password_hash,
            ':role'          => $this->role,
            ':is_active'     => $this->is_active ? 1 : 0,
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
            UPDATE users
            SET username = :username,
                email = :email,
                password_hash = :password_hash,
                role = :role,
                is_active = :is_active
            WHERE id = :id
        ");

        return $stmt->execute([
            ':username'      => $this->username,
            ':email'         => $this->email,
            ':password_hash' => $this->password_hash,
            ':role'          => $this->role,
            ':is_active'     => $this->is_active ? 1 : 0,
            ':id'            => $this->id,
        ]);
    }

    public function delete(): bool
    {
        if ($this->id === null) return false;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}
