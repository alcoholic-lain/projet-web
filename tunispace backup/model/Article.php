<?php

namespace model;
class Article
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE
    public function insert($title, $keywords, $content, $image)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO articles (title, keywords, content, image, likes, comments, created_at)
            VALUES (:title, :keywords, :content, :image, 0, 0, NOW())
        ");
        $stmt->execute([
            ':title' => $title,
            ':keywords' => $keywords,
            ':content' => $content,
            ':image' => $image
        ]);
    }

    // READ ALL
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ BY ID
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update($id, $title, $keywords, $content, $image = null)
    {
        if ($image) {
            $stmt = $this->pdo->prepare("
                UPDATE articles SET title = :title, keywords = :keywords, content = :content, image = :image
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $title,
                ':keywords' => $keywords,
                ':content' => $content,
                ':image' => $image,
                ':id' => $id
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                UPDATE articles SET title = :title, keywords = :keywords, content = :content
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $title,
                ':keywords' => $keywords,
                ':content' => $content,
                ':id' => $id
            ]);
        }
    }

    // DELETE
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
