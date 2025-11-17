<?php
// C:\xampp\htdocs\admin\controller\ReclamationController.php
include   __DIR__ . '/../config.php';
class ReclamationController {
    private $pdo;

    public function __construct() {
        // Inclure la configuration
        $config_path = __DIR__ . '/../config.php';
        if (!file_exists($config_path)) {
            die("Fichier config.php introuvable à: " . $config_path);
        }
        
        include_once $config_path;
        global $pdo;
        $this->pdo = $pdo;
        
        if (!$this->pdo) {
            die("Erreur: Connexion à la base de données échouée");
        }
    }

    // Lire toutes les réclamations
    public function getAllReclamations() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM reclamations ORDER BY date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // Alias pour compatibilité avec vos autres fichiers
    public function listReclamations() {
        return $this->getAllReclamations();
    }

    // Lire une réclamation par ID
    public function getReclamationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM reclamations WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // Alias pour compatibilité
    public function getReclamation($id) {
        return $this->getReclamationById($id);
    }

    // Créer une nouvelle réclamation
    public function createReclamation($user, $sujet, $description, $statut = 'en attente') {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO reclamations (user, sujet, description, date, statut) VALUES (?, ?, ?, NOW(), ?)");
            return $stmt->execute([$user, $sujet, $description, $statut]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // Alias pour compatibilité
    public function addReclamation($user, $sujet, $description, $statut = 'en attente') {
        return $this->createReclamation($user, $sujet, $description, $statut);
    }

    // Mettre à jour une réclamation
    public function updateReclamation($id, $user, $sujet, $description, $statut) {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET user = ?, sujet = ?, description = ?, statut = ? WHERE id = ?");
            return $stmt->execute([$user, $sujet, $description, $statut, $id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // Supprimer une réclamation
    public function deleteReclamation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reclamations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            die("Erreur SQL: " . $e->getMessage());
        }
    }

    // Mettre à jour seulement le statut
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