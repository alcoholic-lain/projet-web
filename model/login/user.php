<?php
class User {
    private ?int $id;
    private ?string $pseudo;
    private ?string $email;
    private ?string $password;
    private ?string $planet;

    // Constructeur
    public function __construct(?int $id, ?string $pseudo, ?string $email, ?string $password, ?string $planet) {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->email = $email;
        $this->password = $password;
        $this->planet = $planet;
    }

    // --- Affichage
    public function show(): void {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Pseudo</th><th>Email</th><th>Planète</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->pseudo}</td>";
        echo "<td>{$this->email}</td>";
        echo "<td>{$this->planet}</td>";
        echo "</tr>";
        echo "</table>";
    }

    // --- Getters et Setters ---
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getPseudo(): ?string { return $this->pseudo; }
    public function setPseudo(?string $pseudo): void { $this->pseudo = $pseudo; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): void { $this->password = $password; }

    public function getPlanet(): ?string { return $this->planet; }
    public function setPlanet(?string $planet): void { $this->planet = $planet; }
}
?>
