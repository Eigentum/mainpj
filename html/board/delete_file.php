<?php
session_start();
require_once '../config/db.php';

// check login
if (!isset($_SESSION['user_id'])) {
    echo "로그인이 필요합니다.";
    exit();
}

$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// load file info
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    echo "파일을 찾을 수 없습니다.";
    exit();
}

// check the User, whether Current User and Post's writer are same or not
$post_stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$post_stmt->execute([$file['post_id']]);
$post = $post_stmt->fetch();

if ($post['user_id'] != $user_id) {
    echo "파일을 삭제할 권한이 없습니다.";
    exit();
}

// Delete File
unlink($file['file_path']);  // Delete file from Server
$stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
$stmt->execute([$file_id]);

// redirect post edit page
header("Location: edit_post.php?id=" . $file['post_id']);
exit();
?>

