<?php
session_start();
require_once '../../../config.php';
header('Content-Type: application/json; charset=utf-8');

class UpdatePasswordController {

    public function update() {
        $response = ['errors' => [], 'success' => false];

        // Vérification session
        if (!isset($_SESSION['user_id'])) {
            $response['errors']['general'] = "Non autorisé";
            echo json_encode($response);
            return;
        }

        $db = Config::getConnexion();

        $current = $_POST['currentPassword'] ?? '';
        $new = $_POST['newPassword'] ?? '';
        $confirm = $_POST['confirmPassword'] ?? '';

        // Validation côté serveur
        if ($current === '') $response['errors']['currentPassword'] = "Veuillez saisir le mot de passe actuel";
        if (strlen($new) < 8) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins 8 caractères";
        if (!preg_match('/[A-Z]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins une majuscule";
        if (!preg_match('/[a-z]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins une minuscule";
        if (!preg_match('/[0-9]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins un chiffre";
        if (!preg_match('/[!@#$%^&*()_\-+=<>?]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins un symbole spécial (!@#$%^&*)";
        if ($new !== $confirm) $response['errors']['confirmPassword'] = "Les mots de passe ne correspondent pas";

        if (!empty($response['errors'])) {
            echo json_encode($response);
            return;
        }

        // Vérifier mot de passe actuel
        $stmt = $db->prepare("SELECT password FROM user WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current, $user['password'])) {
            $response['errors']['currentPassword'] = "Mot de passe actuel incorrect";
            echo json_encode($response);
            return;
        }

        // Vérifier mot de passe différent
        if (password_verify($new, $user['password'])) {
            $response['errors']['newPassword'] = "Vous ne pouvez pas réutiliser votre ancien mot de passe";
            echo json_encode($response);
            return;
        }

        // Update mot de passe
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $update = $db->prepare("UPDATE user SET password = ? WHERE id = ?");
        $update->execute([$hash, $_SESSION['user_id']]);

        // --- ENREGISTRER L'ACTION DE MODIFICATION ---
        $stmtAct = $db->prepare("INSERT INTO user_activity (user_id, action, created_at) VALUES (?, ?, NOW())");
        $stmtAct->execute([$_SESSION['user_id'], 'modification']);

        $response['success'] = true;
        echo json_encode($response);
    }
}

// --- Point d’entrée ---
$controller = new UpdatePasswordController();
$controller->update();
