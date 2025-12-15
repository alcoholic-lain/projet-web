<?php
include __DIR__ . '/../../../config.php';

class ReclamationController {

    private $pdo;

    public function __construct()
    {
        // Connexion via config
        $this->pdo = config::getConnexion();

        if (!$this->pdo) {
            die("Erreur: Connexion à la base de données échouée (PDO null)");
        }
    }

    // &#9989; Lire toutes les réclamations
    public function getAllReclamations() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM reclamations ORDER BY date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    public function listReclamations() {
        return $this->getAllReclamations();
    }

    // &#9989; Lire une réclamation par ID
    public function getReclamationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM reclamations WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    public function getReclamation($id) {
        return $this->getReclamationById($id);
    }

    // &#9989; Créer une réclamation (avec champ urgent)
    public function addReclamation($user, $sujet, $description, $statut = 'en attente', $urgent = 0) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reclamations (user, sujet, description, date, statut, urgent)
                VALUES (?, ?, ?, NOW(), ?, ?)
            ");
            return $stmt->execute([$user, $sujet, $description, $statut, $urgent]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Modifier (avec champ urgent)
    public function updateReclamation($id, $user, $sujet, $description, $statut, $urgent = 0) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE reclamations
                SET user = ?, sujet = ?, description = ?, statut = ?, urgent = ?
                WHERE id = ?
            ");
            return $stmt->execute([$user, $sujet, $description, $statut, $urgent, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Supprimer
    public function deleteReclamation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reclamations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Mettre à jour le statut uniquement
    public function updateStatut($id, $statut) {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
            return $stmt->execute([$statut, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Mettre à jour le statut urgent uniquement
    public function updateUrgent($id, $urgent) {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET urgent = ? WHERE id = ?");
            return $stmt->execute([$urgent, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Récupérer les réclamations urgentes
    public function getUrgentReclamations() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM reclamations WHERE urgent = 1 ORDER BY date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Compter les réclamations urgentes
    public function countUrgentReclamations() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM reclamations WHERE urgent = 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Récupérer les réclamations par statut
    public function getReclamationsByStatus($statut) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM reclamations WHERE statut = ? ORDER BY date DESC");
            $stmt->execute([$statut]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Rechercher des réclamations
    public function searchReclamations($keyword) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM reclamations 
                WHERE sujet LIKE ? OR description LIKE ? OR user LIKE ?
                ORDER BY date DESC
            ");
            $searchTerm = "%$keyword%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // &#9989; Récupérer les statistiques
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total des réclamations
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM reclamations");
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Réclamations urgentes
            $stmt = $this->pdo->query("SELECT COUNT(*) as urgent FROM reclamations WHERE urgent = 1");
            $stats['urgent'] = $stmt->fetch(PDO::FETCH_ASSOC)['urgent'];
            
            // Réclamations par statut
            $stmt = $this->pdo->query("SELECT statut, COUNT(*) as count FROM reclamations GROUP BY statut");
            $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Réclamations du mois
            $stmt = $this->pdo->query("SELECT COUNT(*) as month_count FROM reclamations WHERE MONTH(date) = MONTH(CURRENT_DATE())");
            $stats['this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['month_count'];
            
            return $stats;
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }
}
?>