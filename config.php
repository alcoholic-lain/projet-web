<?php
// C:\xampp\htdocs\admin\config.php
$host = '127.0.0.1';
$dbname = 'tec_max';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Check if function exists before declaring it
if (!function_exists('validateReclamation')) {
    function validateReclamation($user, $sujet, $description) {
        $errors = [];
        if (empty($user)) $errors[] = "Utilisateur obligatoire";
        if (empty($sujet)) $errors[] = "Sujet obligatoire";
        if (empty($description)) $errors[] = "Description obligatoire";
        return $errors;
    }
}
?>