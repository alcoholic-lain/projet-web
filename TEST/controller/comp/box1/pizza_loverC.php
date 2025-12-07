<?php
/*
 * made with love
 */

require __DIR__ . "/../../../config/config.php";
require __DIR__ . "/../../../model/box1/pizza_lover.php";



class Pizza_loverC{
    // add pizza_lover

    public function addPizza_lover($pizza_lover){
        $sql = "INSERT INTO tunispace.pizza_lovers 
            (pizza_name, pizza_pass, fav_pizza, bio) 
            VALUES (:pizza_name, :pizza_pass, :fav_pizza, :bio)";

        $db = Database::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'pizza_name' => $pizza_lover->getPizza_name(),
                'pizza_pass' => $pizza_lover->getPizza_pass(),
                'fav_pizza'  => $pizza_lover->getFav_pizza(),
                'bio'        => $pizza_lover->getBio()
            ]);

            // Get the auto-generated ID if needed
            $newId = $db->lastInsertId();
            echo "Pizza lover '{$pizza_lover->getPizza_name()}' added with ID: $newId<br>";

        } catch (PDOException $e) {
            // This will now show the REAL error (e.g., duplicate pizza_name)
            echo 'Error: ' . $e->getMessage();
        }
    }

}