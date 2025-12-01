<?php
require_once __DIR__ . '/../db.php';
require_once '../model/Article.php';

$articleModel = new Article($pdo);

$id = $_POST['id'];
$title = $_POST['title'];
$keywords = $_POST['keywords'];
$content = $_POST['content'];

$image = null;
if (!empty($_FILES['image']['name'])) {
    $image = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../assets/" . $image);
}

$articleModel->update($id, $title, $keywords, $content, $image);

header("Location: ../view/admin_dashboard.php");
exit;
