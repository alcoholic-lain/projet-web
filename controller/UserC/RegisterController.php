<?php

use User\User;

require_once '../../config.php';
require_once '../../model/User/User.php';

class RegisterController {

    public function handleRegister() {
        if(isset($_POST['pseudo'], $_POST['email'], $_POST['psw'], $_POST['planet'])) {

            $pseudo = trim($_POST['pseudo']);
            $email = trim($_POST['email']);
            $password = trim($_POST['psw']);
            $planet = trim($_POST['planet']);

            if(empty($pseudo) || empty($email) || empty($password) || empty($planet)) {
                echo json_encode([
                    'success' => false,
                    'message' => "❌ Tous les champs sont obligatoires."
                ]);
                return;
            }

            // Vérifier si pseudo ou email existe déjà
            if ($this->existsUser($pseudo, $email)) {
                echo json_encode([
                    'success' => false,
                    'message' => "❌ Pseudo ou email déjà utilisé !"
                ]);
                return;
            }

            $user = new User(null, $pseudo, $email, $password, $planet);
            $this->addUser($user);

        } else {
            echo json_encode([
                'success' => false,
                'message' => "❌ Formulaire incomplet."
            ]);
        }
    }

    public function existsUser($pseudo, $email) {
        try {
            $db = Config::getConnexion();
            $sql = "SELECT * FROM user WHERE pseudo = :pseudo OR email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute([':pseudo' => $pseudo, ':email' => $email]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addUser(User $user) {
        try {
            $db = Config::getConnexion();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO user (pseudo, email, password, planet)
                    VALUES (:pseudo, :email, :password, :planet)";
            $req = $db->prepare($sql);
            $req->execute([
                ':pseudo'   => $user->getPseudo(),
                ':email'    => $user->getEmail(),
                ':password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
                ':planet'   => $user->getPlanet()
            ]);

            echo json_encode([
                'success' => true,
                'message' => "✅ Inscription réussie ! Redirection vers login..."
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => "❌ Erreur lors de l'inscription : " . $e->getMessage()
            ]);
        }
    }
}

// --- Point d’entrée ---
$controller = new RegisterController();
$controller->handleRegister();