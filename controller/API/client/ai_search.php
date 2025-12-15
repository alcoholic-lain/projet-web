<?php
require_once __DIR__ . "/../../components/Innovation/inns_Config.php";

header("Content-Type: application/json; charset=utf-8");

// Vérification entrée
if (!isset($_GET['q']) || strlen(trim($_GET['q'])) < 2) {
    echo json_encode([
        "success" => false,
        "message" => "Requête trop courte"
    ]);
    exit;
}

$query = strtolower(trim($_GET['q']));

// Connexion DB (utilise TA classe config, pas Database)
$db = config::getConnexion();

/* === Recherche Catégories === */
$sqlCat = $db->prepare("
    SELECT id, nom, description, date_creation
    FROM categories
    WHERE LOWER(nom) LIKE :q
       OR LOWER(description) LIKE :q
    LIMIT 20
");
$sqlCat->execute(["q" => "%$query%"]);
$categories = $sqlCat->fetchAll();

/* === Recherche Innovations === */
$sqlInn = $db->prepare("
    SELECT i.id, i.titre, i.description, i.date_creation,
           c.nom AS categorie, c.id AS categorie_id
    FROM innovations i
    LEFT JOIN categories c ON c.id = i.category_id
    WHERE (LOWER(i.titre) LIKE :q
        OR LOWER(i.description) LIKE :q
        OR LOWER(c.nom) LIKE :q)
      AND i.statut = 'Validée'
    ORDER BY i.date_creation DESC
    LIMIT 30
");

$sqlInn->execute(["q" => "%$query%"]);
$innovations = $sqlInn->fetchAll();

echo json_encode([
    "success" => true,
    "query" => $query,
    "categories" => $categories,
    "innovations" => $innovations
]);