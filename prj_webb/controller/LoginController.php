<?php
require_once '../config.php';
require_once '../model/User.php';

class LoginController {

    public function handleLogin(){
        if(isset($_POST['email'], $_POST['password'])){ // attention au name dans le form
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if(empty($email) || empty($password)){
                echo json_encode(['success'=>false, 'message'=>'❌ Tous les champs sont obligatoires.']);
                return;
            }

            try {
                $db = Config::getConnexion();
                $stmt = $db->prepare("SELECT * FROM user WHERE email=:email");
                $stmt->execute([':email'=>$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if($user && password_verify($password, $user['password'])){
                    // ✅ Session
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['pseudo'] = $user['pseudo'];

                    echo json_encode(['success'=>true, 'message'=>'✅ Connexion réussie ! Redirection en cours...']);
                } else {
                    echo json_encode(['success'=>false, 'message'=>'❌ Email ou mot de passe incorrect.']);
                }

            } catch(Exception $e){
                echo json_encode(['success'=>false, 'message'=>'❌ Erreur serveur : '.$e->getMessage()]);
            }
        } else {
            echo json_encode(['success'=>false, 'message'=>'❌ Formulaire incomplet.']);
        }
    }
}

// --- Point d’entrée ---
$controller = new LoginController();
$controller->handleLogin();
