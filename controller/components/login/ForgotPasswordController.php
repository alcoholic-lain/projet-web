<?php
require_once '../../../config.php';

class ForgotPasswordController
{
    public function handleForgotPassword()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $conn = config::getConnexion();

            $email = trim($_POST['email'] ?? '');
            $pseudo = trim($_POST['pseudo'] ?? '');

            // --- Validation ---
            if ($email === '') {
                return $this->json(false, "Email manquant.");
            }
            if ($pseudo === '') {
                return $this->json(false, "Pseudo manquant.");
            }

            // Vérification utilisateur
            $stmt = $conn->prepare("
                SELECT id 
                FROM user 
                WHERE email = :email AND pseudo = :pseudo
            ");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->execute();

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                return $this->json(false, "Cet email ou pseudo n'existe pas.");
            }

            // Génération token
            $token = bin2hex(random_bytes(32));

            // Enregistrement token
            $insertStmt = $conn->prepare("
                INSERT INTO password_resets (email, token, created_at)
                VALUES (:email, :token, NOW())
                ON DUPLICATE KEY UPDATE 
                    token = :token,
                    created_at = NOW()
            ");
            $insertStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $insertStmt->bindParam(':token', $token, PDO::PARAM_STR);
            $insertStmt->execute();

            return $this->json(true, "Token généré.", ['token' => $token]);

        } catch (PDOException $e) {
            return $this->json(false, 'Erreur serveur : ' . $e->getMessage());
        }
    }

    // --- Fonction utilitaire JSON ---
    private function json($success, $message, $data = [])
    {
        $response = array_merge([
            'success' => $success,
            'message' => $message
        ], $data);

        echo json_encode($response);
        exit;
    }
}
$controller = new ForgotPasswordController();
$controller->handleForgotPassword();
