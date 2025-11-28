<?php
// model/User.php


require_once __DIR__ . '/../../config/config.php';


class User
{
    private ?int $id = null;
    private string $username = '';
    private string $email = '';
    private string $password_hash = '';
    private string $role = 'front';
    private string $created_at = '';
    private bool $is_active = true;

    // Getters & Setters (unchanged)
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $hash): void
    {
        $this->password_hash = $hash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $role = strtolower($role);
        $this->role = in_array($role, ['front', 'back'], true) ? $role : 'front';
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    private static function fromArray(array $row): self
    {
        $u = new self();
        $u->id = (int)$row['id'];
        $u->username = $row['username'];
        $u->email = $row['email'];
        $u->password_hash = $row['password'];
        $u->role = $row['chat_role'] ?? 'front';
        $u->created_at = $row['created_at'];
        $u->is_active = (bool)$row['is_active'];
        return $u;
    }

    public static function findAll(): array
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM user ORDER BY id ASC");
        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            $users[] = self::fromArray($row);
        }
        return $users;
    }

    public static function findById(int $id): ?self
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? self::fromArray($row) : null;
    }

    public static function search(string $term, int $excludeUserId = 0): array
    {
        $pdo = config::getConnexion();
        $sql = "SELECT * FROM user WHERE ( username LIKE :term OR email LIKE :term)";
        if ($excludeUserId > 0) $sql .= " AND id <> :exclude";
        $sql .= " ORDER BY username ASC LIMIT 50";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':term', '%' . $term . '%');
        if ($excludeUserId > 0) $stmt->bindValue(':exclude', $excludeUserId, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            $users[] = self::fromArray($row);
        }
        return $users;
    }

    public function save(): bool
    {
        return $this->id === null ? $this->insert() : $this->update();
    }

    private function insert(): bool
    {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO user (username, email, password, chat_role, is_active)
            VALUES (:username, :email, :password_hash, :role, :is_active)
        ");
        $ok = $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':password_hash' => $this->password_hash,
            ':role' => $this->role,
            ':is_active' => $this->is_active ? 1 : 0,
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

    private function update(): bool
    {
        if ($this->id === null) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("
            UPDATE user SET username = :username, email = :email,
                password = :password_hash, chat_role = :role, is_active = :is_active
            WHERE id = :id
        ");
        return $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':password_hash' => $this->password_hash,
            ':role' => $this->role,
            ':is_active' => $this->is_active ? 1 : 0,
            ':id' => $this->id,
        ]);
    }

    public function delete(): bool
    {
        if ($this->id === null) return false;
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }


}


