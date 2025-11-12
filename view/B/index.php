<?php
// view/B/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../model/config/config.php';
require __DIR__ . '/../../controller/AdminController.php';

$controller = new AdminController($pdo);
$controller->run();