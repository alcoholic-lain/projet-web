<?php
// ----------------------
// DB CONNECTION
// ----------------------
$host = "localhost";
$dbname = "tunispace_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB ERROR: " . $e->getMessage());
}

// ----------------------
// FUNCTION: GET ALL POSTS
// ----------------------
function getAllPosts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getPostById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updatePost($id, $title, $keywords, $content, $image) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE articles SET title=?, keywords=?, content=?, image=? WHERE id=?");
    return $stmt->execute([$title, $keywords, $content, $image, $id]);
}


// ----------------------
// HANDLE CREATE POST FORM
// ----------------------
if(isset($_POST['submit'])){
    $title = $_POST['title'] ?? '';
    $keywords = $_POST['keywords'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_POST['image'] ?? '';
    $likes = 0;
    $comments = 0;

    if ($title && $keywords && $content && $image) {
        $stmt = $pdo->prepare(
            "INSERT INTO articles (title, keywords, content, image, likes, comments, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$title, $keywords, $content, $image, $likes, $comments]);
    }

    header("Location: ../view/index.php");
    exit;
}
