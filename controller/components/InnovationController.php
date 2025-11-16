<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Préflight CORS (utile si tu utilises PUT/DELETE en fetch)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../../model/config/data-base.php";
require_once __DIR__ . "/../../model/Innovation/Innovation.php";

$db = (new Database())->getConnection();
$innovationModel = new Innovation($db);

/**
 * Helper pour réponse JSON
 */
function json_response($payload, int $code = 200) {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

/* ============================================================
   1️⃣ GET – plusieurs cas :
      - ?category_id=...  → innovations d’une catégorie
      - ?id=...           → une innovation
      - (rien)            → liste complète (admin)
============================================================ */
if ($method === "GET") {

    // Cas 1 : innovations par catégorie (front visiteur / liste catégorie)
    if (isset($_GET["category_id"])) {
        $categoryId = (int) $_GET["category_id"];

        try {
            // ⚠️ On renvoie TOUTES les innovations de la catégorie
            // (le filtrage "Validée seulement" peut se faire en JS côté visiteur)
            $records = $innovationModel->getByCategory($categoryId, false);

            json_response([
                "success" => true,
                "records" => $records
            ]);
        } catch (Exception $e) {
            json_response([
                "success" => false,
                "message" => "Erreur lors du chargement des innovations par catégorie",
                "error"   => $e->getMessage()
            ], 500);
        }
    }

    // Cas 2 : une innovation par ID
    if (isset($_GET["id"])) {
        $id = (int) $_GET["id"];

        try {
            $record = $innovationModel->getById($id);

            if (!$record) {
                json_response([
                    "success" => false,
                    "message" => "Innovation introuvable"
                ], 404);
            }

            // Ici on renvoie directement l’objet (comme tu le faisais déjà)
            echo json_encode($record);
            exit;

        } catch (Exception $e) {
            json_response([
                "success" => false,
                "message" => "Erreur lors du chargement de l’innovation",
                "error"   => $e->getMessage()
            ], 500);
        }
    }

    // Cas 3 : liste complète (admin)
    try {
        $records = $innovationModel->getAll(false);

        json_response([
            "success" => true,
            "records" => $records
        ]);

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur lors du chargement des innovations",
            "error"   => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   2️⃣ POST – Ajouter une innovation
============================================================ */
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data["titre"]) || empty($data["description"]) || empty($data["category_id"])) {
        json_response([
            "success" => false,
            "message" => "Champs obligatoires manquants"
        ], 400);
    }

    try {
        $innovationModel->titre       = $data["titre"];
        $innovationModel->description = $data["description"];
        $innovationModel->category_id = (int) $data["category_id"];
        $innovationModel->statut      = $data["statut"] ?? "En attente";

        $ok = $innovationModel->create();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Innovation créée avec succès"
            ], 201);
        } else {
            json_response([
                "success" => false,
                "message" => "Échec de la création de l’innovation"
            ], 500);
        }

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur lors de la création",
            "error"   => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   3️⃣ PUT – Mettre à jour :
        - soit seulement le statut (Valider / Rejeter)
        - soit tout (titre, description, category, statut)
============================================================ */
if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = (int) ($data["id"] ?? 0);
    if ($id <= 0) {
        json_response([
            "success" => false,
            "message" => "ID invalide"
        ], 400);
    }

    try {
        // Cas 1 : update uniquement du statut (validation / rejet)
        if (isset($data["statut"]) && !isset($data["titre"]) && !isset($data["description"]) && !isset($data["category_id"])) {

            $innovationModel->id = $id;
            $ok = $innovationModel->updateStatut($data["statut"]);

            if ($ok) {
                json_response([
                    "success" => true,
                    "message" => "Statut mis à jour"
                ]);
            } else {
                json_response([
                    "success" => false,
                    "message" => "Échec de la mise à jour du statut"
                ], 500);
            }
        }

        // Cas 2 : update complet (édition admin)
        $innovationModel->id          = $id;
        $innovationModel->titre       = $data["titre"]        ?? "";
        $innovationModel->description = $data["description"]  ?? "";
        $innovationModel->category_id = (int) ($data["category_id"] ?? 0);
        $innovationModel->statut      = $data["statut"]       ?? "En attente";

        $ok = $innovationModel->update();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Innovation mise à jour"
            ]);
        } else {
            json_response([
                "success" => false,
                "message" => "Échec de la mise à jour de l’innovation"
            ], 500);
        }

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur lors de la mise à jour",
            "error"   => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   4️⃣ DELETE – Supprimer une innovation
============================================================ */
if ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id   = (int) ($data["id"] ?? 0);

    if ($id <= 0) {
        json_response([
            "success" => false,
            "message" => "ID invalide pour suppression"
        ], 400);
    }

    try {
        $innovationModel->id = $id;
        $ok = $innovationModel->delete();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Innovation supprimée"
            ]);
        } else {
            json_response([
                "success" => false,
                "message" => "Échec de la suppression"
            ], 500);
        }

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur lors de la suppression",
            "error"   => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   5️⃣ Méthode non supportée
============================================================ */
json_response([
    "success" => false,
    "message" => "Méthode non supportée"
], 405);
