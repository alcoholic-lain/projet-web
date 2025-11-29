<?php


// paths
const GOOGLE_ICON_PATH = '../view/L/assets/img/g_icon.png';

const LOGIN_CSS = '../view/L/assets/CSS/login.css';

const LOG_CSS = '../view/L/assets/log.css';










session_start();








require_once __DIR__ . '/../controller/AdminC.php';
require_once __DIR__ . '/../controller/ClientC.php';
require_once __DIR__ . '/../controller/LogC.php';
require_once __DIR__ . '/../controller/comp/COMS/AdminController.php';
require_once __DIR__ . '/../controller/comp/COMS/ChatController.php';


$controllerName = $_GET['c'] ?? 'log';
$action         = $_GET['a'] ?? 'index';

switch ($controllerName) {

    case 'chatC':
        $controller = new ChatController();
        break;
    case 'chatA':
        $controller = new AdminController();
        break;

    case 'AdminC':
        $controller = new AdminC();
        break;
    case 'ClientC':
        $controller = new ClientC();
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

