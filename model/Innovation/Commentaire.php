<?php
/**
 * Commentaire Model
 * Table : commentaires
 */

class Commentaire {
    private $conn;
    private $table_name = "commentaires";

    public $id;
    public $innovation_id;
    public $auteur;
    public $contenu;
    public $date_commentaire;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** Récupérer les commentaires d’une innovation */
    public function getByInnovation(int $innovationId): array {
        $sql = "SELECT * FROM {$this->table_name}
                WHERE innovation_id = :iid
                ORDER BY date_commentaire DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Créer un commentaire */
    public function create(): bool {
        $sql = "INSERT INTO {$this->table_name}
                (innovation_id, auteur, contenu, date_commentaire)
                VALUES (:innovation_id, :auteur, :contenu, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":innovation_id", $this->innovation_id, PDO::PARAM_INT);
        $stmt->bindParam(":auteur",        $this->auteur);
        $stmt->bindParam(":contenu",       $this->contenu);

        return $stmt->execute();
    }

    /** Supprimer un commentaire */
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Supprimer tous les commentaires d’une innovation (si besoin) */
    public function deleteByInnovation(int $innovationId): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE innovation_id = :iid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
