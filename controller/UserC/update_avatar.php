<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ob_start(); // empêche tout warning/HTML de casser le JSON

class UpdateAvatarController {

    public function upload() {
        $response = ['errors' => [], 'success' => false];

        // Vérification session
        if (!isset($_SESSION['user_id'])) {
            $response['errors']['avatar'] = "Utilisateur non connecté";
            return $this->send($response);
        }

        // Vérification fichier
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) {
            $response['errors']['avatar'] = "Aucun fichier sélectionné ou erreur d'upload";
            return $this->send($response);
        }

        $file = $_FILES['avatar'];

        // Vérification extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $response['errors']['avatar'] = "Format de fichier non autorisé";
            return $this->send($response);
        }

        // ---- CHEMIN CORRIGÉ ----
        // update_avatar.php → controller/UserC
        // on veut aller → view/LoginC/uploads/avatars/
        $root = dirname(__DIR__, 2); // remonte 2 dossiers
        $targetDir = $root . "/view/Client/uploads/avatars/";
        $publicPath = "uploads/avatars/";

        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $filename = uniqid("avatar_") . '.' . $ext;
        $targetFile = $targetDir . $filename;
        $publicFile = $publicPath . $filename;

        // Déplacement fichier
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            $response['errors']['avatar'] = "Impossible de sauvegarder l'image (permissions ?)";
            return $this->send($response);
        }

        // DB
        try {
            $db = Config::getConnexion();
            $stmt = $db->prepare("UPDATE user SET avatar=? WHERE id=?");
            $stmt->execute([$publicFile, $_SESSION['user_id']]);

            $stmtAct = $db->prepare("INSERT INTO user_activity (user_id, action, created_at) VALUES (?, ?, NOW())");
            $stmtAct->execute([$_SESSION['user_id'], 'modification']);

            $response['success'] = true;
            $response['avatar'] = $publicFile;

        } catch (Exception $e) {
            $response['errors']['avatar'] = "Erreur base de données: " . $e->getMessage();
        }

        return $this->send($response);
    }

    private function send($response) {
        ob_end_clean(); // supprime tout warning/HTML
        echo json_encode($response);
        exit;
    }
}

$controller = new UpdateAvatarController();
$controller->upload();
