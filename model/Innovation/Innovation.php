<?php
/**
 * Innovation Model (DB-backed)
 * Table : innovations
 */

class Innovation {
    private $conn;
    private $table_name = "innovations";

    public $id;
    public $titre;
    public $description;
    public $category_id;   // ⚠️ correspond à ta BDD actuelle
    public $date_creation;
    public $statut;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** Hydratation rapide si besoin */
    public function fromArray(array $data) {
        $this->id           = $data["id"]           ?? $this->id;
        $this->titre        = $data["titre"]        ?? $this->titre;
        $this->description  = $data["description"]  ?? $this->description;
        $this->category_id  = $data["category_id"]  ?? $this->category_id;
        $this->date_creation= $data["date_creation"]?? $this->date_creation;
        $this->statut       = $data["statut"]       ?? $this->statut;
    }

    /** Récupérer toutes les innovations (option : seulement Validées) */
    public function getAll(bool $onlyValidated = false): array {
        $sql = "SELECT * FROM {$this->table_name}";
        if ($onlyValidated) {
            $sql .= " WHERE statut = 'Validée'";
        }
        $sql .= " ORDER BY date_creation DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Récupérer une innovation par ID */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Récupérer les innovations d’une catégorie (option : seulement Validées) */
    public function getByCategory(int $categoryId, bool $onlyValidated = false): array {
        $sql = "SELECT * FROM {$this->table_name} WHERE category_id = :cid";
        if ($onlyValidated) {
            $sql .= " AND statut = 'Validée'";
        }
        $sql .= " ORDER BY date_creation DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":cid", $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Créer une innovation */
    public function create(): bool {
        $sql = "INSERT INTO {$this->table_name}
                (titre, description, category_id, date_creation, statut)
                VALUES (:titre, :description, :category_id, NOW(), :statut)";
        $stmt = $this->conn->prepare($sql);

        // Valeur par défaut si statut vide
        $statut = $this->statut ?: "En attente";

        $stmt->bindParam(":titre",        $this->titre);
        $stmt->bindParam(":description",  $this->description);
        $stmt->bindParam(":category_id",  $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(":statut",       $statut);

        return $stmt->execute();
    }

    /** Mettre à jour une innovation (titre, desc, catégorie, statut) */
    public function update(): bool {
        $sql = "UPDATE {$this->table_name}
                SET titre = :titre,
                    description = :description,
                    category_id = :category_id,
                    statut = :statut
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":titre",        $this->titre);
        $stmt->bindParam(":description",  $this->description);
        $stmt->bindParam(":category_id",  $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(":statut",       $this->statut);
        $stmt->bindParam(":id",           $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Mettre à jour uniquement le statut (Valider / Rejeter) */
    public function updateStatut(string $statut): bool {
        $sql = "UPDATE {$this->table_name}
                SET statut = :statut
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":statut", $statut);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Supprimer une innovation */
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
