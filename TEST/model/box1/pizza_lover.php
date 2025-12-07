<?php

/*
 * made with love
 */
class Pizza_lover{
    private ?int  $id = null;
    private ?string $pizza_name = null;
    private ?string $pizza_pass = null;
    private ?string $created_at = null;
    private ?string $fav_pizza = null;
    private ?string $bio = null;

    //constructor
    public function __construct(
        ?int    $id         = null,
        ?string $pizza_name = null,
        ?string $pizza_pass = null,
        ?string $fav_pizza  = null,

        ?string $bio        = null
    ) {
        $this->id         = $id;
        $this->pizza_name = $pizza_name;
        $this->pizza_pass = $pizza_pass;
        $this->fav_pizza  = $fav_pizza;
        $this->bio        = $bio;
    }

    //getters

    public function getId(): ?int {
        return $this->id;
    }
    public function getPizza_name(): ?string {
        return $this->pizza_name;
    }
    public function getPizza_pass(): ?string  {
        return $this->pizza_pass;
    }
    public function getCreated_at(): ?string  {
        return $this->created_at;
    }
    public function getFav_pizza(): ?string {
        return $this->fav_pizza;
    }
    public function getBio(): ?string  {
        return $this->bio;
    }




    // setters
    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setPizza_name(?string  $pizza_name): self {
        $this->pizza_name = $pizza_name;
        return $this;
    }

    public function setPizza_pass(?string  $pizza_pass): self {
        $this->pizza_pass = $pizza_pass;
        return $this;
    }

    public function setCreated_at(?string  $created_at): self {
        $this->created_at = $created_at;
        return $this;
    }

    public function setFav_pizza(?string  $fav_pizza): self {
        $this->fav_pizza = $fav_pizza;
        return $this;
    }

    public function setBio(?string  $bio): self {
        $this->bio = $bio;
        return $this;
    }
}

$pizza_lover = new Pizza_lover(1,'e','');