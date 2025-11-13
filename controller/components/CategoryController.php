<?php
require_once __DIR__ . '/../../model/config/data-base.php';
require_once __DIR__ . '/../../model/Innovation/Category.php';

header('Content-Type: application/json; charset=utf-8');

// Initialize DB & model
$database = new Database();
$db = $database->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}
$category = new Category($db);

$method = $_SERVER['REQUEST_METHOD'];

// Helper to read JSON body
function getJsonBody() {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return null;
    }
    return $data;
}

try {
    if ($method === 'GET') {
        // Single category ?id=
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                exit;
            }
            $category->id = $id;
            if ($category->readOne()) {
                http_response_code(200);
                echo json_encode([
                    'id' => $category->id,
                    'nom' => $category->nom,
                    'description' => $category->description,
                    'date_creation' => $category->date_creation
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
            }
        } else {
            // List all categories
            $stmt = $category->readAll();
            $records = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $records[] = [
                    'id' => (int)$row['id'],
                    'nom' => $row['nom'],
                    'description' => $row['description'],
                    'date_creation' => $row['date_creation']
                ];
            }
            http_response_code(200);
            echo json_encode(['success' => true, 'records' => $records]);
        }
    } elseif ($method === 'POST') {
        $data = getJsonBody();
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Corps JSON invalide']);
            exit;
        }
        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        if ($nom === '' || $description === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
            exit;
        }
        $category->nom = $nom;
        $category->description = $description;
        if ($category->create()) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'id' => $category->id,
                'nom' => $category->nom,
                'description' => $category->description,
                'date_creation' => $category->date_creation
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
        }
    } elseif ($method === 'PUT') {
        $data = getJsonBody();
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Corps JSON invalide']);
            exit;
        }
        $id = (int)($data['id'] ?? 0);
        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        if ($id <= 0 || $nom === '' || $description === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }
        $category->id = $id;
        $category->nom = $nom;
        $category->description = $description;
        if ($category->update()) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    } elseif ($method === 'DELETE') {
        $data = getJsonBody();
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Corps JSON invalide']);
            exit;
        }
        $id = (int)($data['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }
        $category->id = $id;
        if ($category->delete()) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
