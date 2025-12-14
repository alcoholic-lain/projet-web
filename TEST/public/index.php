<?php

if (isset($_GET['session_name']) && preg_match('/^[a-z0-9]+$/i', $_GET['session_name'])) {
    session_name('TUNISPACE_' . $_GET['session_name']);
}

session_start();




// paths
const GOOGLE_ICON_PATH = '../view/LoginC/COMS/img/g_icon.png';

const LOGIN_CSS = '../view/LoginC/COMS/CSS/login.css';

const LOG_CSS = '../view/LoginC/COMS/log.css';



















require_once __DIR__ . '/../controller/LogC.php';
require_once __DIR__ . '/../controller/components/COMS/AdminController.php';
require_once __DIR__ . '/../controller/components/COMS/ChatController.php';


$controllerName = $_GET['c'] ?? 'log';
$action         = $_GET['a'] ?? 'login';

switch ($controllerName) {

    case 'chatC':
        $controller = new ChatController();
        break;
    case 'chatA':
        $controller = new AdminController();
        break;



    case 'log':
    default:
        $controller = new LogC();
        break;
}

if (!method_exists($controller, $action)) {
    $action = 'index';
}

$controller->$action();

