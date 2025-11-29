<?php


class ClientC{
    public function index(): void{
        echo '<style>';
        echo file_get_contents(__DIR__ . '/../view/F/assets/Client.css');
        echo '</style>';




        echo 'this is the client page';
        require __DIR__ . '/../view/F/F_index.php';



        // this is how you include js files

        echo '<script>'.file_get_contents(__DIR__ . '/../view/F/assets/Client.js').' </script>';

    }
}

