<?php

class Category
{
    private ?int $id;
    private string $nom;
    private ?string $description;
    private ?string $date_creation;

    public function __construct(
        ?int $id,
        string $nom,
        ?string $description,
        ?string $date_creation
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->date_creation = $date_creation;
    }

    // --- GETTERS ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDateCreation(): ?string
    {
        return $this->date_creation;
    }

    // --- SETTERS ---
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setDateCreation(?string $date_creation): void
    {
        $this->date_creation = $date_creation;
    }
}
