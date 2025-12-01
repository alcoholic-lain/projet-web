<?php
session_start();
require_once '../../../config.php';
header('Content-Type: application/json; charset=utf-8');

class UpdateProfileController {

    private $db;
    private $userId;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->sendJson(['errors' => ['general' => 'Non autorisé.']]);
            exit;
        }
        $this->db = Config::getConnexion();
        $this->userId = $_SESSION['user_id'];
    }

    public function handleUpdate() {
        $errors = [];

        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validation serveur
        if ($pseudo === '') $errors['pseudo'] = "Le pseudo ne peut pas être vide.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide.";

        if (!empty($errors)) {
            $this->sendJson(['errors' => $errors]);
            return;
        }

        // Vérifier doublon email
        $stmt = $this->db->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
        $stmt->execute([$email, $this->userId]);

        if ($stmt->rowCount() > 0) {
            $this->sendJson(['errors' => ['email' => "Email déjà utilisé."]]);
            return;
        }

        // Mise à jour
        $update = $this->db->prepare("UPDATE user SET pseudo = ?, email = ? WHERE id = ?");
        $update->execute([$pseudo, $email, $this->userId]);

        // --- ENREGISTRER L'ACTION DE MODIFICATION ---
        $stmtAct = $this->db->prepare("INSERT INTO user_activity (user_id, action, created_at) VALUES (?, ?, NOW())");
        $stmtAct->execute([$this->userId, 'modification']);

        $this->sendJson(['success' => true]);
    }

    private function sendJson($data) {
        echo json_encode($data);
    }
}

// --- Point d’entrée ---
$controller = new UpdateProfileController();
$controller->handleUpdate();
