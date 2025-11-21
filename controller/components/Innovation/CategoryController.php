<?php

require_once __DIR__ . "/../../../config.php";
require_once __DIR__ . "/../../../model/Innovation/Category.php";

class CategoryController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /** Récupérer toutes les catégories */
    public function listCategories(): array
    {
        $sql = "SELECT * FROM categories ORDER BY date_creation DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Récupérer une catégorie par ID */
    public function getCategory(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Ajouter une catégorie */
    public function addCategory(Category $cat): bool
    {
        $sql = "INSERT INTO categories (nom, description, date_creation)
                VALUES (:nom, :description, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nom'         => $cat->getNom(),
            ':description' => $cat->getDescription()
        ]);
    }

    /** Modifier une catégorie */
    public function updateCategory(Category $cat): bool
    {
        if ($cat->getId() === null) {
            throw new Exception("ID catégorie manquant.");
        }

        $sql = "UPDATE categories
                SET nom = :nom,
                    description = :description
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'          => $cat->getId(),
            ':nom'         => $cat->getNom(),
            ':description' => $cat->getDescription()
        ]);
    }

    /** Supprimer une catégorie */
    public function deleteCategory(int $id): bool
    {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
