<?php

require_once __DIR__ . "/inns_Config.php";

class InnovationController
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();

    }


    /**
     * Lister TOUTES les innovations (pour la page générale)
     */
    public function listInnovations(): array
    {
        $sql = "SELECT 
                i.*, 
                u.pseudo AS utilisateur,
                c.nom AS categorie_nom
            FROM innovations i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN user u ON i.user_id = u.id
            ORDER BY i.date_creation DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lister les innovations d'une catégorie donnée
     */
    public function listInnovationsByCategory(int $category_id): array
    {
        $sql = "SELECT i.*, c.nom AS categorie_nom
        FROM innovations i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE i.category_id = :category_id
        ORDER BY i.date_creation DESC";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Lister les innovations d'un utilisateur donné
     */
    public function listInnovationsByUser(int $user_id): array
    {
        $sql = "SELECT i.*, c.nom AS categorie_nom
        FROM innovations i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE i.user_id = :user_id
        ORDER BY i.date_creation DESC";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getInnovation(int $id): ?array
    {
        $sql = "SELECT * FROM innovations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function addInnovation($innovation)
    {
        $sql = "INSERT INTO innovations 
    (user_id, titre, description, category_id, statut, date_creation, file)
    VALUES 
    (:user_id, :titre, :description, :category_id, :statut, NOW(), :file)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':user_id'     => $innovation->getUserId(),
            ':titre'       => $innovation->getTitre(),
            ':description' => $innovation->getDescription(),
            ':category_id' => $innovation->getCategoryId(),
            ':statut'      => $innovation->getStatut(),
            ':file'        => $innovation->getFile()   // ✅ LIGNE CRITIQUE
        ]);
    }


    public function updateInnovation(Innovation $i): bool
    {
        if ($i->getId() === null) {
            throw new Exception("ID innovation manquant.");
        }

        $sql = "UPDATE innovations
                SET titre = :titre,
                    description = :description,
                    category_id = :category_id,
                    statut = :statut
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'          => $i->getId(),
            ':titre'       => $i->getTitre(),
            ':description' => $i->getDescription(),
            ':category_id' => $i->getCategoryId(),
            ':statut'      => $i->getStatut()
        ]);
    }

    public function deleteInnovation(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT file FROM innovations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $file = $stmt->fetchColumn();

        if ($file) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . "/projet-web/" . ltrim($file, '/');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $sql = "DELETE FROM innovations WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

}
