<?php
session_start();
require_once 'config/db.php';

// Check User
if (!isset($_SESSION['user_id'])) {
    echo "로그인이 필요합니다.";
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ($post_id === 0) {
    echo "유효한 게시물 ID가 필요합니다.";
    exit();
}

// Check recommand already Done? 
$stmt = $pdo->prepare("SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);
$like = $stmt->fetch();

if ($like) {
    // recommand already Done!
    echo "이미 이 게시물을 추천했습니다.";
} else {
    // recommandation up
    $stmt = $pdo->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $post_id]);

    // increase recommandation number
    $stmt = $pdo->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
    $stmt->execute([$post_id]);

    echo "추천이 완료되었습니다.";
}
?>

