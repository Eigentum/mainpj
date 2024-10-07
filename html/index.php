<?php
include 'includes/header.php';
require_once 'config/db.php';

// 최근 게시물 가져오기
$stmt_recent_posts = $pdo->prepare("SELECT id, title FROM posts ORDER BY created_at DESC LIMIT 5");
$stmt_recent_posts->execute();
$recent_posts = $stmt_recent_posts->fetchAll(PDO::FETCH_ASSOC);

// 공지사항 가져오기 (category = 'notice'로 구분)
$stmt_notice_posts = $pdo->prepare("SELECT id, title FROM posts WHERE category = 'notice' ORDER BY created_at DESC LIMIT 5");
$stmt_notice_posts->execute();
$notice_posts = $stmt_notice_posts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>홈페이지</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?> <!-- 사이드바 포함 -->

        <main>
            <h2>커뮤니티에 오신 것을 환영합니다!</h2>
            <p>이곳은 클래식 음악 애호가들을 위한 커뮤니티입니다.</p>
            <p>게시판에서 자유롭게 의견을 나누고, 작곡가 정보를 탐색하며, 추천 음악을 들어보세요.</p>

            <!-- 최근 게시물 섹션 -->
	    <section>
		<br>
                <h3>최근 게시물</h3>
                <ul>
                    <?php if (!empty($recent_posts)): ?>
                        <?php foreach ($recent_posts as $post): ?>
                            <li><a href="board/view.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>최근 게시물이 없습니다.</li>
                    <?php endif; ?>
                </ul>
            </section>

            <!-- 공지사항 섹션 -->
            <section>
                <h3>공지사항</h3>
                <ul>
                    <?php if (!empty($notice_posts)): ?>
                        <?php foreach ($notice_posts as $post): ?>
                            <li><a href="board/view.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>공지사항이 없습니다.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </main>
    </div>
</body>
</html>

