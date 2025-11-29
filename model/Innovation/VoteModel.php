<?php
require_once __DIR__ . '/../../config.php';

class VoteModel {

    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? config::getConnexion();
    }

    // Récupérer le vote de l'utilisateur
    public function getUserVote($innovation_id, $user_id) {
        $sql = "SELECT vote_type FROM votes WHERE innovation_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$innovation_id, $user_id]);
        return $stmt->fetch();
    }

    // Ajouter ou modifier un vote
    public function setVote($innovation_id, $user_id, $vote_type) {

        $existing = $this->getUserVote($innovation_id, $user_id);

        if ($existing) {
            if ($existing["vote_type"] === $vote_type) {
                // supprimer vote
                $sql = "DELETE FROM votes WHERE innovation_id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$innovation_id, $user_id]);
            } else {
                // modifier vote
                $sql = "UPDATE votes SET vote_type = ? 
                        WHERE innovation_id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$vote_type, $innovation_id, $user_id]);
            }
        }

        // sinon → insertion
        $sql = "INSERT INTO votes (innovation_id, user_id, vote_type) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$innovation_id, $user_id, $vote_type]);
    }

    // Stats votes
    public function getStats($innovation_id) {
        $sql = "
            SELECT 
                SUM(vote_type = 'up')   AS upvotes,
                SUM(vote_type = 'down') AS downvotes
            FROM votes
            WHERE innovation_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$innovation_id]);
        return $stmt->fetch();
    }
}
