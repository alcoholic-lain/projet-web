<?php
ob_start(); // ✅ empêche toute sortie parasite
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config.php';
require_once '../../../model/login/user.php';

class RegisterController {

    public function handleRegister() {

        try {
            if(!isset($_POST['pseudo'], $_POST['email'], $_POST['psw'], $_POST['planet'])) {
                throw new Exception("Formulaire incomplet.");
            }

            $pseudo  = trim($_POST['pseudo']);
            $email   = trim($_POST['email']);
            $password = trim($_POST['psw']);
            $planet  = strtolower(trim($_POST['planet'])); // ✅ enum OK

            if(empty($pseudo) || empty($email) || empty($password) || empty($planet)) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            if ($this->existsUser($pseudo, $email)) {
                throw new Exception("Pseudo ou email déjà utilisé !");
            }

            $user = new User(null, $pseudo, $email, $password, $planet);
            $this->addUser($user);

        } catch (Exception $e) {
            ob_clean(); // ✅ nettoie toute sortie HTML
            echo json_encode([
                'success' => false,
                'message' => "❌ Erreur : " . $e->getMessage()
            ]);
            exit;
        }
    }

    public function existsUser($pseudo, $email) {
        $db = Config::getConnexion();
        $sql = "SELECT id FROM user WHERE pseudo = :pseudo OR email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':email'  => $email
        ]);
        return $stmt->rowCount() > 0;
    }

    public function addUser(User $user) {

        $db = Config::getConnexion();

        $sql = "INSERT INTO user (pseudo, email, password, statut, role_id, planet)
                VALUES (:pseudo, :email, :password, 'actif', 2, :planet)";

        $req = $db->prepare($sql);
        $req->execute([
            ':pseudo'   => $user->getPseudo(),
            ':email'    => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            ':planet'   => strtolower($user->getPlanet())
        ]);

        ob_clean(); // ✅ supprime tout HTML parasite
        echo json_encode([
            'success' => true,
            'message' => "✅ Inscription réussie ! Redirection vers login..."
        ]);
        exit;
    }
}

$controller = new RegisterController();
$controller->handleRegister();
