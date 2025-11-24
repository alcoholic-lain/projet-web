<?php
/**
 * Database Configuration
 * Author: Hichem Challakhi
 */

if (!class_exists('Database')) {
    class Database
    {
        private $host = "localhost";
        private $db_name = "tunispace_database";
        private $username = "root";
        private $password = "";
        private $conn;

        /**
         * Get database connection
         * @return PDO|null
         */
        public function getConnection()
        {
            $this->conn = null;

            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("set names utf8");
            } catch (PDOException $exception) {
                die("❌ ERREUR PDO : " . $exception->getMessage());            }

            return $this->conn;
        }
    }
}
?>
