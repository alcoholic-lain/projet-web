<?php
require_once __DIR__ . "/../../../config.php";
require_once __DIR__ . "/../../../model/Innovation/CommentModel.php";
require_once __DIR__ . "/../../../model/Innovation/AttachmentModel.php";

class CommentController {

    private $db;
    private $commentModel;
    private $attachmentModel;

    public function __construct() {
        $this->db = config::getConnexion();


        $this->commentModel    = new CommentModel($this->db);
        $this->attachmentModel = new AttachmentModel($this->db);
    }

    public function addComment($innovation_id, $user_id, $content, $files) {

        $comment_id = $this->commentModel->addComment(
            $innovation_id,
            $user_id,
            $content
        );

        // ✅ SÉCURITÉ : s'il n'y a pas de fichiers → on sort proprement
        if (!$files || empty($files['tmp_name']) || !is_array($files['tmp_name'])) {
            return $comment_id;
        }

        // ✅ Upload sécurisé
        foreach ($files['tmp_name'] as $i => $tmp) {
            if (!empty($tmp)) {

                $name = time() . "_" . basename($files["name"][$i]);
                $path = "uploads/" . $name;

                move_uploaded_file(
                    $tmp,
                    __DIR__ . "/../../../uploads/" . $name
                );

                $this->attachmentModel->addAttachment(
                    $comment_id,
                    $path
                );
            }
        }

        return $comment_id;
    }


    public function getComments($innovation_id) {
        return $this->commentModel->getComments($innovation_id);
    }
}
