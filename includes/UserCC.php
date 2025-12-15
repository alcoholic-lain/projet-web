<?php
require_once __DIR__ . '/../config.php';

class User {
    private $db;
    private $data;

    public function __construct() {
        $this->db = config::getConnexion();

        // Charger les données de session si l'utilisateur est connecté
        if (isset($_SESSION['user'])) {
            $this->data = $_SESSION['user'];
        } else {
            $this->data = null;
        }
    }

    // ==========================
    // LOGIN
    // ==========================
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $this->data = $user;
            return true;
        }
        return false;
    }

    // ==========================
    // LOGOUT
    // ==========================
    public function logout() {
        unset($_SESSION['user']);
        $this->data = null;
        session_destroy();
    }

    // ==========================
    // GET DATA
    // ==========================
    public function get($key) {
        return $this->data[$key] ?? null;
    }

    public function getAll() {
        return $this->data;
    }

    // ==========================
    // UPDATE INFO
    // ==========================
    public function update($fields) {
        if (!$this->data) return false;

        $setParts = [];
        $values = [];
        foreach ($fields as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $this->data['id'];

        $sql = "UPDATE user SET " . implode(',', $setParts) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            // Mettre à jour les données de session
            foreach ($fields as $key => $value) {
                $this->data[$key] = $value;
            }
            $_SESSION['user'] = $this->data;
        }

        return $result;
    }

    // ==========================
    // UPDATE PASSWORD
    // ==========================
    public function updatePassword($currentPassword, $newPassword) {
        if (!$this->data) return false;

        if (!password_verify($currentPassword, $this->data['password'])) {
            return false; // Mot de passe actuel incorrect
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update(['password' => $hashed]);
    }

    // ==========================
    // BADGES
    // ==========================
    public function getBadges() {
        if (!$this->data) return [];

        $stmt = $this->db->prepare("
            SELECT b.id, b.name, b.description, b.icon
            FROM user_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.user_id = ?
        ");
        $stmt->execute([$this->data['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBadge($badgeId) {
        if (!$this->data) return false;

        $stmt = $this->db->prepare("SELECT * FROM user_badges WHERE user_id = ? AND badge_id = ?");
        $stmt->execute([$this->data['id'], $badgeId]);

        if ($stmt->rowCount() == 0) {
            $stmtInsert = $this->db->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            return $stmtInsert->execute([$this->data['id'], $badgeId]);
        }
        return false;
    }

    // ==========================
    // STATUT
    // ==========================
    public function getStatus() {
        return $this->data['statut'] ?? null;
    }

    public function setStatus($statut) {
        return $this->update(['statut' => $statut]);
    }

    // ==========================
    // AVATAR
    // ==========================
    public function getAvatar() {
        return $this->data['avatar'] ?? 'assets/img/default-avatar.png';
    }

    public function setAvatar($avatarPath) {
        return $this->update(['avatar' => $avatarPath]);
    }
}
