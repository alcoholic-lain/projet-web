<?php
// public/index.php

session_start();

require_once __DIR__ . '/../controller/ChatController.php';
require_once __DIR__ . '/../controller/AdminController.php';

$controllerName = $_GET['c'] ?? 'chat';
$action         = $_GET['a'] ?? 'index';

switch ($controllerName) {
    case 'admin':
        $controller = new AdminController();
        break;
    case 'chat':
    default:
        $controller = new ChatController();
        break;
}

if (!method_exists($controller, $action)) {
    $action = 'index';
}

$controller->$action();
