<?php

require_once __DIR__ . "/../../../config.php";
require_once __DIR__ . "/../../../model/Innovation/Innovation.php";
require_once __DIR__ . "/../../../model/Innovation/Category.php";

class InnovationController
{
    private PDO $db;

    public function __construct()
    {
        // Utilisation correcte de ta classe Database
        $this->db = (new Database())->getConnection();
    }

    /** Liste complète */
    public function listInnovations(): array
    {
        $sql = "SELECT i.*, c.nom AS categorie_nom
                FROM innovations i
                LEFT JOIN categories c ON i.category_id = c.id
                ORDER BY i.date_creation DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Mes innovations */
    public function listInnovationsByUser($userId): array
    {
        $sql = "SELECT i.*, c.nom AS categorie_nom
                FROM innovations i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.user_id = :user_id";

        $query = $this->db->prepare($sql);
        $query->bindValue(':user_id', $userId);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Par catégorie */
    public function listInnovationsByCategory($catId): array
    {
        $sql = "SELECT i.*, c.nom AS categorie_nom
                FROM innovations i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.category_id = :catId";

        $query = $this->db->prepare($sql);
        $query->bindValue(':catId', $catId);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Récupérer une innovation */
    public function getInnovation(int $id): ?array
    {
        $sql = "SELECT * FROM innovations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([ ':id' => $id ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Ajouter */
    public function addInnovation(Innovation $inn): bool
    {
        $sql = "INSERT INTO innovations
            (titre, category_id, description, date_creation, statut, user_id)
            VALUES (:titre, :category_id, :description, NOW(), :statut, :user_id)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':titre'        => $inn->getTitre(),
            ':category_id'  => $inn->getCategoryId(),
            ':description'  => $inn->getDescription(),
            ':statut'       => $inn->getStatut(),
            ':user_id'      => $inn->getUserId()   // ← IMPORTANT
        ]);
    }

    /** Modifier */
    public function updateInnovation(Innovation $inn): bool
    {
        if ($inn->getId() === null) {
            throw new Exception("ID innovation manquant.");
        }

        $sql = "UPDATE innovations SET
                    titre = :titre,
                    description = :description,
                    category_id = :category_id,
                    statut = :statut
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'          => $inn->getId(),
            ':titre'       => $inn->getTitre(),
            ':description' => $inn->getDescription(),
            ':category_id' => $inn->getCategoryId(),
            ':statut'      => $inn->getStatut()
        ]);
    }

    /** Supprimer */
    public function deleteInnovation(int $id): bool
    {
        $sql = "DELETE FROM innovations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([ ':id' => $id ]);
    }
}
