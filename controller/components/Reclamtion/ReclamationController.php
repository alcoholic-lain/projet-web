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

    // ✅ Lire toutes les réclamations
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

    // ✅ Lire une réclamation par ID
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

    // ✅ Créer une réclamation
    public function addReclamation($user, $sujet, $description, $statut = 'en attente') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reclamations (user, sujet, description, date, statut)
                VALUES (?, ?, ?, NOW(), ?)
            ");
            return $stmt->execute([$user, $sujet, $description, $statut]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // ✅ Modifier
    public function updateReclamation($id, $user, $sujet, $description, $statut) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE reclamations
                SET user = ?, sujet = ?, description = ?, statut = ?
                WHERE id = ?
            ");
            return $stmt->execute([$user, $sujet, $description, $statut, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // ✅ Supprimer
    public function deleteReclamation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reclamations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // ✅ Mettre à jour le statut uniquement
    public function updateStatut($id, $statut) {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
            return $stmt->execute([$statut, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }
}
?>
