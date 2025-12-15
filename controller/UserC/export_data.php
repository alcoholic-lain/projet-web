<?php
session_start();
require_once '../../config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$db = config::getConnexion();

// Récupérer uniquement pseudo, email, avatar et statut
$stmt = $db->prepare("SELECT pseudo, email, avatar, statut FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Utilisateur introuvable']);
    exit;
}

// Forcer le téléchargement JSON
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="mes_donnees.json"');
echo json_encode($user, JSON_PRETTY_PRINT);
?>