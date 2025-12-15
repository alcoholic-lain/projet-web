<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

// ------- 1. VALIDATION -------
if (!isset($_GET['q']) || strlen(trim($_GET['q'])) < 1) {
    echo json_encode(["success" => false, "reply" => "Dis-moi quelque chose 😊"]);
    exit;
}

$query = trim($_GET['q']);
$qLower = mb_strtolower($query);

// ------- 2. INIT HISTORIQUE -------
if (!isset($_SESSION['ai_history'])) {
    $_SESSION['ai_history'] = [];
}

// Ajouter message utilisateur
$_SESSION['ai_history'][] = ["from" => "user", "msg" => $query];

// ------- 3. APPEL AU MOTEUR DE RECHERCHE EXISTANT -------
$searchUrl = "http://localhost/projet-web/controller/API/client/ai_search.php?q=" . urlencode($query);
$raw = file_get_contents($searchUrl);
$data = json_decode($raw, true);

// ------- 4. LOGIQUE CONVERSATIONNELLE -------
$reply = "";

// Si aucun résultat trouvé
if (empty($data["categories"]) && empty($data["innovations"])) {
    $reply = "😕 Je n’ai rien trouvé pour **{$query}**.\n\n";
    $reply .= "💡 Par contre, je peux te proposer des idées si tu veux : drones, énergie solaire, IA, robotique…";
}
else {
    $reply .= "🔍 Voici ce que j’ai trouvé pour **{$query}** :\n\n";

    // Catégories
    foreach ($data["categories"] as $c) {
        $reply .= "📁 **{$c['nom']}** → {$c['description']}\n";
    }

    // Innovations
    foreach ($data["innovations"] as $i) {
        $reply .= "🚀 **{$i['titre']}** (catégorie : {$i['categorie']})\n";
    }
}

// Petites capacités IA
if (str_contains($qLower, "merci")) {
    $reply .= "\n😊 Avec plaisir ! On continue ?";
}
if (str_contains($qLower, "idee") || str_contains($qLower, "idée")) {
    $reply .= "\n✨ Tu veux des idées d’innovation dans quel domaine ?";
}

// Ajouter message bot à l’historique
$_SESSION['ai_history'][] = ["from" => "bot", "msg" => $reply];

// ------- 5. RENVOYER RÉPONSE -------
echo json_encode([
    "success" => true,
    "reply" => nl2br($reply),
    "history" => $_SESSION['ai_history']
], JSON_UNESCAPED_UNICODE);
