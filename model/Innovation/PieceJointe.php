<?php
/**
 * Pièce Jointe Model
 * Table : pieces_jointes
 */

class PieceJointe {
    private $conn;
    private $table_name = "pieces_jointes";

    public $id;
    public $innovation_id;
    public $chemin_fichier;
    public $type;
    public $date_upload;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** Récupérer les pièces jointes d’une innovation */
    public function getByInnovation(int $innovationId): array {
        $sql = "SELECT * FROM {$this->table_name}
                WHERE innovation_id = :iid
                ORDER BY date_upload DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Ajouter une pièce jointe */
    public function create(): bool {
        $sql = "INSERT INTO {$this->table_name}
                (innovation_id, chemin_fichier, type, date_upload)
                VALUES (:innovation_id, :chemin_fichier, :type, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":innovation_id",  $this->innovation_id, PDO::PARAM_INT);
        $stmt->bindParam(":chemin_fichier", $this->chemin_fichier);
        $stmt->bindParam(":type",           $this->type);

        return $stmt->execute();
    }

    /** Supprimer une pièce jointe */
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Supprimer toutes les pièces jointes d’une innovation */
    public function deleteByInnovation(int $innovationId): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE innovation_id = :iid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
