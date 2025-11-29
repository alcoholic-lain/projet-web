<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../model/login/user.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

class LoginController {

    private $db;

    public function __construct() {

        // ❗ Tu voulais garder ta logique → OK
        // MAIS Database n'existe pas → donc on retire JUSTE cette ligne
        // $database = new Database();

        // ❗ config.php contient "class config" (minuscule)
        // donc on doit utiliser "config::getConnexion()"
        $this->db = config::getConnexion();
    }

    public function handleLogin() {
        $response = ['success' => false, 'message' => ''];

        // Vérifie si les champs existent
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $response['message'] = '❌ Tous les champs sont obligatoires.';
            echo json_encode($response);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['avatar'] = $user['avatar'];


                // Log activité
                $stmtLog = $this->db->prepare("INSERT INTO user_activity (user_id, action) VALUES (?, ?)");
                @$stmtLog->execute([$user['id'], 'connexion']); // @ pour éviter erreur si table manque

                $response = [
                    'success' => true,
                    'message' => '✅ Connexion réussie ! Redirection en cours...',
                    'role_id' => $user['role_id']
                ];

            } else {
                $response['message'] = '❌ Email ou mot de passe incorrect.';
            }

        } catch (Exception $e) {
            $response['message'] = '❌ Erreur serveur : ' . $e->getMessage();
        }

        echo json_encode($response);
    }
}

$controller = new LoginController();
$controller->handleLogin();
