<?php
session_start();
require_once '../../../config.php';

class DashboardController {

    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    // --- Historique connexions (7 derniers jours) ---
    public function getConnexionsLast7Days() {
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) as day, COUNT(*) as total
            FROM user_activity
            WHERE action = 'connexion'
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->fillDays($data);
    }

    // --- Historique modifications (7 derniers jours) ---
    public function getModificationsLast7Days() {
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) as day, COUNT(*) as total
            FROM user_activity
            WHERE action = 'modification'
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->fillDays($data);
    }

    // --- Fonction utilitaire pour remplir les 7 derniers jours ---
    private function fillDays($data) {
        $days = [];
        $values = [];
        for($i=6; $i>=0; $i--){
            $day = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            foreach($data as $row){
                if($row['day'] == $day){
                    $values[] = (int)$row['total'];
                    $found = true;
                    break;
                }
            }
            if(!$found) $values[] = 0;
            $days[] = $day;
        }
        return ['labels' => $days, 'values' => $values];
    }
}

// --- Point d'entrée pour JSON ---
$controller = new DashboardController();
header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? '';
if($type === 'connexions') {
    echo json_encode($controller->getConnexionsLast7Days());
} elseif($type === 'modifications') {
    echo json_encode($controller->getModificationsLast7Days());
} else {
    echo json_encode(['error' => 'Type non valide']);
}
?>