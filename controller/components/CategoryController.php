<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// CORS preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "../../model/config/data-base.php";
require_once "../../model/Innovation/Category.php";

$db = (new Database())->getConnection();
$categoryModel = new Category($db);

/**
 * Helper JSON
 */
function json_response($data, int $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

/* ============================================================
   1️⃣ GET
============================================================ */
if ($method === "GET") {

    // GET ONE
    if (isset($_GET["id"])) {
        $id = (int) $_GET["id"];

        try {
            $cat = $categoryModel->getById($id);

            if (!$cat) {
                json_response([
                    "success" => false,
                    "message" => "Catégorie introuvable"
                ], 404);
            }

            json_response($cat);
        } catch (Exception $e) {
            json_response([
                "success" => false,
                "message" => "Erreur serveur",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // GET ALL
    try {
        $records = $categoryModel->getAll();

        json_response([
            "success" => true,
            "records" => $records
        ]);

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur",
            "error" => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   2️⃣ POST – Ajouter une catégorie
============================================================ */
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data["nom"]) || empty($data["description"])) {
        json_response([
            "success" => false,
            "message" => "Champs obligatoires manquants"
        ], 400);
    }

    try {
        $categoryModel->nom         = $data["nom"];
        $categoryModel->description = $data["description"];

        $ok = $categoryModel->create();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Catégorie créée avec succès"
            ], 201);
        } else {
            json_response([
                "success" => false,
                "message" => "Erreur lors de la création"
            ], 500);
        }

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur",
            "error" => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   3️⃣ PUT – Modifier une catégorie
============================================================ */
if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id   = (int) ($data["id"] ?? 0);

    if ($id <= 0) {
        json_response([
            "success" => false,
            "message" => "ID invalide"
        ], 400);
    }

    try {
        $categoryModel->id          = $id;
        $categoryModel->nom         = $data["nom"]         ?? "";
        $categoryModel->description = $data["description"] ?? "";

        $ok = $categoryModel->update();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Catégorie mise à jour"
            ]);
        } else {
            json_response([
                "success" => false,
                "message" => "Échec de la mise à jour"
            ], 500);
        }

    } catch (Exception $e) {
        json_response([
            "success" => false,
            "message" => "Erreur serveur",
            "error" => $e->getMessage()
        ], 500);
    }
}

/* ============================================================
   4️⃣ DELETE – Supprimer une catégorie
============================================================ */
if ($method === "DELETE") {

    $data = json_decode(file_get_contents("php://input"), true);
    $id   = (int) ($data["id"] ?? 0);

    if ($id <= 0) {
        json_response([
            "success" => false,
            "message" => "ID invalide"
        ], 400);
    }

    try {
        $categoryModel->id = $id;

        $ok = $categoryModel->delete();

        if ($ok) {
            json_response([
                "success" => true,
                "message" => "Catégorie supprimée"
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
            "message" => "Erreur serveur",
            "error" => $e->getMessage()
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
