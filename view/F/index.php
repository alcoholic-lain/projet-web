<?php
// view/F/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../model/config/config.php';
require __DIR__ . '/../../controller/ChatController.php';

$controller = new ChatController($pdo);
$controller->handle();          // $convs, $conv, $msgs, $members are set here
require __DIR__ . '/index.html'; // <-- HTML view is included