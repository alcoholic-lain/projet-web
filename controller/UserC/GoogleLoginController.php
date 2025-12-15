<?php
session_start();
require_once '../../google-api-php-client/vendor/autoload.php';
require_once '../../config.php';
require_once 'mail_functions.php'; // 🔥 AJOUT IMPORTANT
$clientID = "93972698160-8h8lv6eb0oft792emkdo96c2dl417458.apps.googleusercontent.com";
$clientSecret = "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";
$redirectURI = "http://localhost/projet-web/controller/UserC/GoogleLoginController.php";

$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectURI);
$client->addScope("email");
$client->addScope("profile");

$db = Config::getConnexion();

// ---------------------------------------------------
if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google\Service\Oauth2($client);
    $info = $oauth->userinfo->get();

    $google_id = $info->id;
    $pseudo    = $info->name;
    $email     = $info->email;
    $avatar    = $info->picture;

    // ---------------------------------------------------
    // Vérifier par email
    // ---------------------------------------------------
    $checkEmail = $db->prepare("SELECT * FROM user WHERE email = :email");
    $checkEmail->execute(['email' => $email]);
    $existingUser = $checkEmail->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {

        // 1️⃣ Empêcher login si banni
        if ($existingUser['statut'] === 'banni') {
            sendBanEmail($email, $existingUser['pseudo'], "Tentative de connexion Google");
            echo "<script>alert('Votre compte est banni.');window.location='../view/LoginC/login.html';</script>";
            exit;
        }

        // 2️⃣ Connexion normale
        $_SESSION['user_id'] = $existingUser['id'];
        $_SESSION['role_id'] = $existingUser['role_id'];
        $_SESSION['pseudo']  = $existingUser['pseudo'];

        // 🔹 Cookies Remember Me (1 semaine)
        setcookie('user_id', $_SESSION['user_id'], time()+604800, '/');
        setcookie('user_pseudo', $_SESSION['pseudo'], time()+604800, '/');
        setcookie('role_id', $_SESSION['role_id'], time()+604800, '/');

        // Ajouter google_id si pas existant
        if (empty($existingUser['google_id'])) {
            $update = $db->prepare("UPDATE user SET google_id = :gid WHERE id = :id");
            $update->execute(['gid' => $google_id, 'id' => $existingUser['id']]);
        }

        header("Location: ../../view/Client/F_index.php");
        exit;
    }

    // ---------------------------------------------------
    // Vérifier par google_id si email masqué
    // ---------------------------------------------------
    $checkGoogle = $db->prepare("SELECT * FROM user WHERE google_id = :gid");
    $checkGoogle->execute(['gid' => $google_id]);
    $userGoogle = $checkGoogle->fetch(PDO::FETCH_ASSOC);

    if ($userGoogle) {

        if ($userGoogle['statut'] === 'banni') {
            sendBanEmail($userGoogle['email'], $userGoogle['pseudo'], "Tentative de connexion Google");
            echo "<script>alert('Votre compte est banni.');window.location='../../view/LoginC/login.html';</script>";
            exit;
        }

        $_SESSION['user_id'] = $userGoogle['id'];
        $_SESSION['role_id'] = $userGoogle['role_id'];
        $_SESSION['pseudo']  = $userGoogle['pseudo'];

        // 🔹 Cookies Remember Me (1 semaine)
        setcookie('user_id', $_SESSION['user_id'], time()+604800, '/');
        setcookie('user_pseudo', $_SESSION['pseudo'], time()+604800, '/');
        setcookie('role_id', $_SESSION['role_id'], time()+604800, '/');

        header("Location: ../../view/Client/F_index.php");
        exit;
    }

    // ---------------------------------------------------
    // Nouvel utilisateur Google
    // ---------------------------------------------------
    $insert = $db->prepare("
        INSERT INTO user (pseudo, email, password, statut, role_id, planet, avatar, google_id)
        VALUES (:pseudo, :email, NULL, 'actif', 2, 'terra', :avatar, :google_id)
    ");

    $insert->execute([
        'pseudo'     => $pseudo,
        'email'      => $email,
        'avatar'     => $avatar,
        'google_id'  => $google_id
    ]);

    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['role_id'] = 2;
    $_SESSION['pseudo']  = $pseudo;

    // 🔹 Cookies Remember Me (1 semaine)
    setcookie('user_id', $_SESSION['user_id'], time()+604800, '/');
    setcookie('user_pseudo', $_SESSION['pseudo'], time()+604800, '/');
    setcookie('role_id', $_SESSION['role_id'], time()+604800, '/');

    header("Location: ../../view/Client/F_index.php");
    exit;
}

// ---------------------------------------------------
$authUrl = $client->createAuthUrl();
header("Location: " . $authUrl);
exit;
?>
