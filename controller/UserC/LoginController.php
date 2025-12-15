<?php
require_once '../../config.php';
require_once '../../model/User/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

class LoginController {

    private $db;
    private $recaptchaSecret = '6LcoLSMsAAAAANk5nB_W2BaCOlU0MFPXsh_7M_ic';

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function handleLogin() {
        $response = ['success' => false, 'message' => ''];

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1;
        $recaptchaToken = $_POST['recaptcha_token'] ?? '';

        if (!$email || !$password) {
            $response['message'] = '❌ Tous les champs sont obligatoires.';
            echo json_encode($response);
            return;
        }

        // Vérification reCAPTCHA
        if (!$recaptchaToken) {
            $response['message'] = '❌ reCAPTCHA manquant.';
            echo json_encode($response);
            return;
        }

        $recaptchaResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$this->recaptchaSecret}&response={$recaptchaToken}");
        $recaptchaData = json_decode($recaptchaResponse, true);

        if (!$recaptchaData['success'] || ($recaptchaData['score'] ?? 0) < 0.5) {
            $response['message'] = '❌ reCAPTCHA non validé.';
            echo json_encode($response);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $response['message'] = '❌ Email ou mot de passe incorrect.';
                echo json_encode($response);
                return;
            }

            if ($user['statut'] === 'banni') {
                $response['message'] = '⛔ Votre compte est banni.';
                echo json_encode($response);
                return;
            }

            // Connexion OK → créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['remember'] = $remember;

            // Mettre le statut actif
            $stmtStatus = $this->db->prepare("UPDATE user SET statut = 'actif' WHERE id = ?");
            $stmtStatus->execute([$user['id']]);

            // Remember Me → créer les cookies
            if ($remember) {
                $expire = time() + 60*60*24*30; // 30 jours
                setcookie('user_id', $user['id'], $expire, "/");
                setcookie('user_pseudo', $user['pseudo'], $expire, "/");
                setcookie('role_id', $user['role_id'], $expire, "/");
            }

            $response = [
                'success' => true,
                'message' => '✅ Connexion réussie ! Redirection en cours...',
                'role_id' => $user['role_id']
            ];

            // Journaliser la connexion
            try {
                $stmtLog = $this->db->prepare("INSERT INTO user_activity (user_id, action) VALUES (?, ?)");
                $stmtLog->execute([$user['id'], 'connexion']);
            } catch (Exception $e) {
                error_log("Erreur insertion user_activity: " . $e->getMessage());
            }

        } catch (Exception $e) {
            $response['message'] = '❌ Erreur serveur : ' . $e->getMessage();
        }

        echo json_encode($response);
    }
}

// Entrée
$controller = new LoginController();
$controller->handleLogin();
?>