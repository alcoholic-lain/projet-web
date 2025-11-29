<?php

class Innovation
{
    private ?int $id;
    private string $titre;
    private string $description;
    private int $category_id;
    private ?string $date_creation;
    private string $statut;
    private int $user_id;   // <-- important
    private ?string $file;

    public function __construct(
        ?int $id,
        string $titre,
        string $description,
        int $category_id,
        int $user_id,            // <-- ajouté ici
        string $statut,
        ?string $date_creation = null,
        ?string $file = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->user_id = $user_id;       // <-- IMPORTANT
        $this->statut = $statut;
        $this->date_creation = $date_creation;
        $this->file = $file;

    }
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function getCategoryId(): int { return $this->category_id; }
    public function getDateCreation(): ?string { return $this->date_creation; }
    public function getStatut(): string { return $this->statut; }
    public function getUserId(): int { return $this->user_id; }  // <-- ajouté
    public function getFile(): ?string { return $this->file; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setCategoryId(int $category_id): void { $this->category_id = $category_id; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setUserId(int $user_id): void { $this->user_id = $user_id; } // <-- ajouté
    public function setFile(?string $file): void { $this->file = $file; }

}
