<?php
session_start();
require_once '../../../google-api-php-client/vendor/autoload.php';
$clientID = "93972698160-8h8lv6eb0oft792emkdo96c2dl417458.apps.googleusercontent.com";
$clientSecret = "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";
$redirectURI = "http://localhost/projet-web/controller/components/login/GoogleLoginController.php";

$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectURI);
$client->addScope("email");
$client->addScope("profile");

// --------------------------------------------
// 3. Vérifier si Google renvoie le code
// --------------------------------------------
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google\Service\Oauth2($client);
    $info = $oauth->userinfo->get();

    $google_id = $info->id;
    $pseudo    = $info->name;      // correspond à ta colonne 'pseudo'
    $email     = $info->email;
    $avatar    = $info->picture;

    // --------------------------------------------
    // 4. Connexion à la base de données
    // --------------------------------------------
    require_once '../../../config.php'; // adapter le chemin si nécessaire
    $db = Config::getConnexion();

    // Vérifier si l'utilisateur existe déjà via google_id
    $stmt = $db->prepare("SELECT * FROM user WHERE google_id = :gid");
    $stmt->execute(['gid' => $google_id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        // Utilisateur existant
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['role_id'] = $userData['role_id'];
    } else {
        // Nouvel utilisateur → insérer dans la BDD
        $stmt = $db->prepare(
            "INSERT INTO user (pseudo, email, password, statut, role_id, planet, avatar, google_id)
             VALUES (:pseudo, :email, :password, :statut, :role_id, :planet, :avatar, :google_id)"
        );
        $stmt->execute([
            'pseudo'    => $pseudo,
            'email'     => $email,
            'password'  => null,        // pas de mot de passe pour Google
            'statut'    => 'actif',
            'role_id'   => 2,           // rôle utilisateur normal
            'planet'    => 'terra',     // valeur par défaut
            'avatar'    => $avatar,
            'google_id' => $google_id
        ]);

        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['role_id'] = 2;
    }

    // Redirection après login
    header("Location: ../../../view/Client/login/profile.php"); // adapter le chemin si nécessaire
    exit;
}

// --------------------------------------------
// 5. Sinon → rediriger vers Google
// --------------------------------------------
$authUrl = $client->createAuthUrl();
header("Location: " . $authUrl);
exit;


?>


