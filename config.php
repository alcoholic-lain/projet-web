<?php


if (!class_exists('config')) {

    class config
    {
        private static $pdo = null;

        public static function getConnexion()
        {
            if (self::$pdo === null) {
                try {
                    self::$pdo = new PDO(
                        "mysql:host=localhost;dbname=tunispace_database;charset=utf8",
                        "root",
                        ""
                    );

                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                } catch (Exception $e) {
                    die('Erreur DB : ' . $e->getMessage());
                }
            }

            return self::$pdo;
        }
    }
}

/* ✅ OBLIGATOIRE SELON TA DEMANDE :
   appel automatique sécurisé */
if (class_exists('config')) {
    config::getConnexion();
}
