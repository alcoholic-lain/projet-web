<?php
/**
 * Category Model (DB-backed)
 * Table : categories
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

    /** Récupérer toutes les catégories */
    public function getAll(): array {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Récupérer une catégorie par ID */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Créer une catégorie */
    public function create(): bool {
        $sql = "INSERT INTO {$this->table_name}
                (nom, description, date_creation)
                VALUES (:nom, :description, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nom",         $this->nom);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    /** Mettre à jour une catégorie */
    public function update(): bool {
        $sql = "UPDATE {$this->table_name}
                SET nom = :nom,
                    description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nom",         $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id",          $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Supprimer une catégorie */
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
