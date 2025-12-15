<?php
define('SMTP_USER', 'challakhihichem1@gmail.com');
define('SMTP_PASS', 'tyqxsbdamxbusljt');



//controllers
const ChatC_PATH ='/../controller/comp/COMS/ChatController.php';
const AdminC_PATH ='/../controller/comp/COMS/AdminController.php';












// DB settings
const DB_HOST = 'localhost';
const DB_NAME = 'tunispace_database';
const DB_USER = 'root';
const DB_PASS = ''; // your password here


class COMS_config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "tunispace_database";

            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );

            } catch (Exception $e) {
                die("Erreur: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

COMS_config::getConnexion();
