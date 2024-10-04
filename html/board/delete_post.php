<?php
session_start();
require_once '../config/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "로그인이 필요합니다.";
    exit();
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Check post writer
$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "유효하지 않은 게시물입니다.";
    exit();
}

if ($post['user_id'] != $user_id) {
    echo "게시물을 삭제할 권한이 없습니다.";
    exit();
}

// delete post
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

// redirect to post list, after delete post
header("Location: board.php");
exit();
?>

