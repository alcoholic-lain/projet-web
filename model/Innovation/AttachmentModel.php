<?php
require_once __DIR__ . "/../../controller/components/Innovation/inns_Config.php";

class AttachmentModel
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? config::getConnexion();
    }

    public function addAttachment($comment_id, $file_path)
    {
        $sql = "INSERT INTO attachments (comment_id, file_path) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$comment_id, $file_path]);
    }

    public function getAttachments($comment_id)
    {
        $sql = "SELECT * FROM attachments WHERE comment_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$comment_id]);
        return $stmt->fetchAll();
    }
}
