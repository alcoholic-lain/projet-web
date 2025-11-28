<?php
require __DIR__ . "/../config/config.php";
require __DIR__ . "/../model/box1/pizza_lover.php";


class AdminC {
    public function addPizza_lover(Pizza_lover $pizza_lover):void{
         $sql = "INSERT INTO tunispace.pizza_lovers 
                (pizza_name, pizza_pass, fav_pizza, bio) 
                VALUES (:pizza_name, :pizza_pass, :fav_pizza, :bio)";
        $db = config::getConnexion();
        try{
            $query = $db->prepare($sql);
            $query->execute([

                'pizza_name' => $pizza_lover->getPizza_name(),
                'pizza_pass' => $pizza_lover->getPizza_pass(),
                'fav_pizza' => $pizza_lover->getFav_pizza(),
                'bio' => $pizza_lover->getBio()
            ]);
            echo "the pizza lover " . $pizza_lover->getPizza_name() . " is added <br>";
        } catch (Exception $e) {
            echo 'pizza lover error ' . $e->getMessage();
        }
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?c=logC&a=index');
            exit;
        }
    }

    public function index(): void{
        $this->requireLogin();

        echo'<style>'. file_get_contents(__DIR__ . '/../view/B/assets/Admin.css') .'</style>';





        echo 'this is the admin page';
        require_once __DIR__ . '/../view/B/B_index.php';





        // this is how you include js files
        echo '<script>'.file_get_contents(__DIR__ . '/../view/B/assets/Admin.js') . ' </script>';


    }


}
