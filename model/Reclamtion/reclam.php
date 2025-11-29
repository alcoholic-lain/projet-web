<?php
include_once __DIR__ . '/../../config.php';
class Reclamation {
    private $conn;
    private $table_name = "reclamations";

    public $id;
    public $user;
    public $sujet;
    public $description;
    public $date;
    public $statut;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Ajouter une réclamation
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET user=:user, sujet=:sujet, description=:description, date=:date, statut=:statut";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->user = htmlspecialchars(strip_tags($this->user));
        $this->sujet = htmlspecialchars(strip_tags($this->sujet));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Liaison des paramètres
        $stmt->bindParam(":user", $this->user);
        $stmt->bindParam(":sujet", $this->sujet);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":statut", $this->statut);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ - Lire toutes les réclamations
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ - Lire une réclamation par ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE - Mettre à jour une réclamation
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET user=:user, sujet=:sujet, description=:description, statut=:statut 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->user = htmlspecialchars(strip_tags($this->user));
        $this->sujet = htmlspecialchars(strip_tags($this->sujet));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Liaison des paramètres
        $stmt->bindParam(":user", $this->user);
        $stmt->bindParam(":sujet", $this->sujet);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE - Supprimer une réclamation
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
function validateReclamation($user, $sujet, $description) {
    $errors = [];

    if (empty($sujet)) $errors[] = "Sujet vide";
    if (empty($description)) $errors[] = "Description vide";

    return $errors;
}

?>