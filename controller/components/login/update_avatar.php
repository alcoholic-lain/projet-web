<?php
session_start();
require_once '../../../config.php';

header('Content-Type: application/json; charset=utf-8');

class UpdateAvatarController {

    public function upload() {
        $response = ['errors' => [], 'success' => false];

        // Vérification session
        if (!isset($_SESSION['user_id'])) {
            $response['errors']['avatar'] = "Utilisateur non connecté";
            echo json_encode($response);
            return;
        }

        // Vérification fichier
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) {
            $response['errors']['avatar'] = "Aucun fichier sélectionné ou erreur d'upload";
            echo json_encode($response);
            return;
        }

        $file = $_FILES['avatar'];

        // Vérification extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $response['errors']['avatar'] = "Format de fichier non autorisé";
            echo json_encode($response);
            return;
        }

        // Chemins
        $targetDir = dirname(__DIR__) . "../../../view/Client/login/uploads/avatars/"; // chemin serveur
        $publicPath = "view/Client/login/uploads/avatars/";

        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $filename = uniqid() . 'controller.' . $ext;
        $targetFile = $targetDir . $filename;
        $publicFile = $publicPath . $filename;

        // Déplacement fichier
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            try {
                $db = Config::getConnexion();
                $stmt = $db->prepare("UPDATE user SET avatar=? WHERE id=?");
                $stmt->execute([$publicFile, $_SESSION['user_id']]);

                // --- ENREGISTRER L'ACTION ---
                $stmtAct = $db->prepare("INSERT INTO user_activity (user_id, action) VALUES (?, ?)");
                $stmtAct->execute([$_SESSION['user_id'], 'modification']);


                $response['success'] = true;
                $response['avatar'] = $publicFile;
            } catch (Exception $e) {
                $response['errors']['avatar'] = "Erreur base de données: " . $e->getMessage();
            }
        } else {
            $response['errors']['avatar'] = "Impossible de sauvegarder l'image (permissions ?)";
        }

        echo json_encode($response);
    }
}

// --- Point d'entrée ---
$controller = new UpdateAvatarController();
$controller->upload();
