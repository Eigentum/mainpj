<?php 
include '../includes/header.php';
require_once '../config/db.php';

// 토론 주제 리스트 불러오기
$stmt = $pdo->prepare("SELECT * FROM posts WHERE category = 'discussion' ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>토론 게시판</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
<?php include '../includes/sidebar.php'; ?>
<main>
	<section>
    <h2>토론 게시판</h2>
    <a href="write.php?category=discussion">글쓰기</a>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <a href="view.php?id=<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
                - <?php echo $post['created_at']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
	</section>
</main>
</div>
</body>
</html>

