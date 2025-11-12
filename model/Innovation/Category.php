<?php
/**
 * Category Model
 * Author: Hichem Challakhi
 */

class Category {
    private $conn;
    private $table_name = "categories";

    // Category properties
    public $id;
    public $nom;
    public $description;
    public $date_creation;

    /**
     * Constructor with DB connection
     * @param $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new category
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nom=:nom, description=:description, date_creation=:date_creation";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->date_creation = htmlspecialchars(strip_tags($this->date_creation));

        // Bind values
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":date_creation", $this->date_creation);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Read all categories
     * @return PDOStatement
     */
    public function read() {
        $query = "SELECT id, nom, description, date_creation 
                  FROM " . $this->table_name . " 
                  ORDER BY date_creation DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read a single category
     * @return bool
     */
    public function readOne() {
        $query = "SELECT id, nom, description, date_creation 
                  FROM " . $this->table_name . " 
                  WHERE id = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom = $row['nom'];
            $this->description = $row['description'];
            $this->date_creation = $row['date_creation'];
            return true;
        }

        return false;
    }

    /**
     * Update a category
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nom=:nom, description=:description 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Delete a category
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
