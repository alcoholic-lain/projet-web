<?php
require_once __DIR__ . "/../../controller/components/Innovation/inns_Config.php";

class CommentModel {

    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? config::getConnexion();
    }

    // ✅ AJOUT D’UN COMMENTAIRE (ALIGNÉ AVEC TA TABLE comments)
    public function addComment(int $innovation_id, int $user_id, string $content): int
    {
        $sql = "INSERT INTO comments (innovation_id, user_id, content, created_at)
                VALUES (:innovation_id, :user_id, :content, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':innovation_id' => $innovation_id,
            ':user_id'       => $user_id,
            ':content'       => $content
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ✅ RÉCUPÉRATION DES COMMENTAIRES
    public function getComments(int $innovation_id)
    {
        $sql = "SELECT c.*, u.pseudo
                FROM comments c
                JOIN user u ON u.id = c.user_id
                WHERE c.innovation_id = :id
                ORDER BY c.created_at DESC";

        $req = $this->db->prepare($sql);
        $req->execute([":id" => $innovation_id]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
