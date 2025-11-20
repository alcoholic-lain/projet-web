<?php
/**
 * Vote Model
 * Table : votes
 */

class Vote {
    private $conn;
    private $table_name = "votes";

    public $id;
    public $innovation_id;
    public $type_vote;   // 'up' ou 'down'
    public $date_vote;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** Ajouter un vote */
    public function create(): bool {
        $sql = "INSERT INTO {$this->table_name}
                (innovation_id, type_vote, date_vote)
                VALUES (:innovation_id, :type_vote, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":innovation_id", $this->innovation_id, PDO::PARAM_INT);
        $stmt->bindParam(":type_vote",     $this->type_vote);

        return $stmt->execute();
    }

    /** Compter les votes par type (up / down) pour une innovation */
    public function countByInnovation(int $innovationId): array {
        $sql = "SELECT type_vote, COUNT(*) as total
                FROM {$this->table_name}
                WHERE innovation_id = :iid
                GROUP BY type_vote";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = ["up" => 0, "down" => 0];
        foreach ($rows as $row) {
            $type = $row["type_vote"];
            $result[$type] = (int)$row["total"];
        }
        return $result;
    }

    /** Supprimer tous les votes d’une innovation */
    public function deleteByInnovation(int $innovationId): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE innovation_id = :iid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":iid", $innovationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
