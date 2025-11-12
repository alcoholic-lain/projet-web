<?php

class Category {
    private $id;
    private $name;
    private $description;
    private $icon;
    private $color;
    
    public function __construct($id = null, $name = '', $description = '', $icon = '', $color = '') {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->color = $color;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getIcon() {
        return $this->icon;
    }
    
    public function getColor() {
        return $this->color;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function setIcon($icon) {
        $this->icon = $icon;
    }
    
    public function setColor($color) {
        $this->color = $color;
    }
    
    // Convert to array
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color
        ];
    }
}
