<?php



const COMS2User_PATH = '/../../../model/COMS/User.php';
const COMS2Conv_PATH = '/../../../model/COMS/Conversation.php';
const COMS2Message_PATH = '/../../../model/COMS/Message.php';



// controller to view
Const COMS2B_PATH = '/../../../view/B/comp/COMS/index.php';
const COMS2F_PATH ='/../../../view/F/comp/COMS/index.php';

const COMS2L_PATH = '/../../../view/L/L_index.php';




require_once __DIR__ . COMS2User_PATH;
require_once __DIR__ . COMS2Conv_PATH;
require_once __DIR__ . COMS2Message_PATH;




function buildUrl($controller, $action, $params = []) {
    $url = "index.php?c=$controller&a=$action";

    // Add session_name if it exists
    if (isset($_SESSION['_session_name_suffix'])) {
        $params['session_name'] = $_SESSION['_session_name_suffix'];
    }

    foreach ($params as $key => $value) {
        $url .= "&$key=" . urlencode($value);
    }

    return $url;
}