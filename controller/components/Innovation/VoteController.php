<?php
require_once __DIR__ . "/inns_Config.php";

class VoteController {

    private $voteModel;

    public function __construct() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->voteModel = new VoteModel();
    }

    public function vote() {

        header("Content-Type: application/json");

        // ✅ Vérification paramètres
        if (!isset($_POST["innovation_id"], $_POST["vote_type"])) {
            echo json_encode([
                "success" => false,
                "error" => "missing_params"
            ]);
            return;
        }

        // ✅ Vérification connexion utilisateur
        if (!isset($_SESSION["user_id"])) {
            echo json_encode([
                "success" => false,
                "error" => "not_logged"
            ]);
            return;
        }

        $innovation_id = (int) $_POST["innovation_id"];
        $vote_type     = $_POST["vote_type"];
        $user_id       = (int) $_SESSION["user_id"];

        // ✅ Enregistrement du vote
        $this->voteModel->setVote($innovation_id, $user_id, $vote_type);

        // ✅ Récupération stats
        $stats = $this->voteModel->getStats($innovation_id);
        $currentVote = $this->voteModel->getUserVote($innovation_id, $user_id);

        // ✅ RÉPONSE JSON OBLIGATOIRE
        echo json_encode([
            "success"  => true,
            "score"    => ($stats["upvotes"] ?? 0) - ($stats["downvotes"] ?? 0),
            "userVote" => $currentVote["vote_type"] ?? null
        ]);
    }
}
