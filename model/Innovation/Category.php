<?php
/**
 * Category Model (DB-backed)
 * Table: categories
 */
class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $nom;
    public $description;
    public $date_creation;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all categories
    public function readAll() {
        $query = "SELECT id, nom, description, date_creation FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single category by id
    public function readOne() {
        $query = "SELECT id, nom, description, date_creation FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->nom = $row['nom'];
            $this->description = $row['description'];
            $this->date_creation = $row['date_creation'];
            return true;
        }
        return false;
    }

    // Create category
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nom, description, date_creation)
                  VALUES (:nom, :description, NOW())";
        $stmt = $this->conn->prepare($query);
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            // fetch created row date_creation
            $this->readOne();
            return true;
        }
        return false;
    }

    // Update category
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom = :nom, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete category
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
