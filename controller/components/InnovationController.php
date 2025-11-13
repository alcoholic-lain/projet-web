<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../../model/config/data-base.php"; // ← IMPORTANT
require_once "../../model/Innovation/Innovation.php";

$db = (new Database())->getConnection();

/* ============================================================
   1️⃣ GET innovations by category
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["category_id"])) {

    $categoryId = intval($_GET["category_id"]);

    $query = "SELECT * FROM innovations WHERE category_id = :cid ORDER BY date_creation DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":cid", $categoryId, PDO::PARAM_INT);
    $stmt->execute();

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "records" => $records
    ]);
    exit;
}

/* ============================================================
   2️⃣ GET one innovation by ID
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {

    $id = intval($_GET["id"]);

    $query = "SELECT * FROM innovations WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($record ?: ["success" => false, "message" => "Not found"]);
    exit;
}

/* ============================================================
   3️⃣ GET all innovations (admin)
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $query = "SELECT * FROM innovations ORDER BY date_creation DESC";
    $stmt = $db->query($query);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "records" => $records
    ]);
    exit;
}

/* ============================================================
   4️⃣ ADD innovation (POST)
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO innovations (titre, description, category_id) 
              VALUES (:titre, :description, :cid)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":titre", $data["titre"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":cid", $data["category_id"]);

    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Innovation created"]);
    exit;
}

/* ============================================================
   5️⃣ UPDATE innovation (PUT)
============================================================ */
/* ============================================================
   5️⃣ UPDATE innovation (PUT)
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id = intval($data["id"] ?? 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID invalide"]);
        exit;
    }

    // Cas 1 : mise à jour du statut uniquement (validation / rejet)
    if (isset($data["statut"]) && !isset($data["titre"]) && !isset($data["description"])) {
        $query = "UPDATE innovations SET statut = :statut WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":statut", $data["statut"]);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Statut mis à jour"]);
        exit;
    }

    // Cas 2 : mise à jour complète (titre, description, category_id, statut éventuel)
    $query = "UPDATE innovations
              SET titre = :titre, description = :description, category_id = :cid
              " . (isset($data["statut"]) ? ", statut = :statut " : "") . "
              WHERE id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":titre", $data["titre"]);
    $stmt->bindParam(":description", $data["description"]);
    $stmt->bindParam(":cid", $data["category_id"]);
    if (isset($data["statut"])) {
        $stmt->bindParam(":statut", $data["statut"]);
    }
    $stmt->bindParam(":id", $id);

    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Innovation updated"]);
    exit;
}


/* ============================================================
   6️⃣ DELETE innovation (DELETE)
============================================================ */
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    $data = json_decode(file_get_contents("php://input"), true);

    $query = "DELETE FROM innovations WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $data["id"]);

    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Innovation deleted"]);
    exit;
}

?>
