<?php
class Article {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Insert a new article
    public function insert($title, $keywords, $content, $image) {
        $stmt = $this->pdo->prepare("
            INSERT INTO articles 
            (title, keywords, content, image, likes, comments, created_at) 
            VALUES (:title, :keywords, :content, :image, 0, 0, NOW())
        ");
        $stmt->execute([
            ':title' => $title,
            ':keywords' => $keywords,
            ':content' => $content,
            ':image' => $image
        ]);
    }

    // Get all articles
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
