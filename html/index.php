<?php
session_start();

// load DB Setting include config/db.php
require_once 'config/db.php';

// check the connecting user
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>클래식 음악 애호가 커뮤니티</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>클래식 음악 애호가 커뮤니티</h1>
        <nav>
            <ul>
                <li><a href="index.php">홈</a></li>
                <li><a href="board/board.php">게시판</a></li>
                <li><a href="music/composers.php">작곡가 정보</a></li>
                <li><a href="music/recommendations.php">추천 음악</a></li>
                <li><a href="events/event_list.php">공연 일정</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="profile.php">프로필</a></li>
                    <li><a href="logout.php">로그아웃</a></li>
                <?php else: ?>
                    <li><a href="login.php">로그인</a></li>
                    <li><a href="register.php">회원가입</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>최근 게시물</h2>
            <ul>
                <?php
                // load 5 recent list
                $stmt = $pdo->prepare("SELECT title, created_at FROM posts ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $recent_posts = $stmt->fetchAll();

                foreach ($recent_posts as $post):
                ?>
                    <li><?php echo htmlspecialchars($post['title']); ?> - <?php echo $post['created_at']; ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h2>추천 음악</h2>
            <a href="music/recommendations.php">곡 추천</a>
        </section>

        <section>
            <h2>작곡가 정보</h2>
            <p>바로크부터 현대까지 클래식 작곡가들의 생애와 작품을 만나보세요.</p>
            <a href="music/composers.php">작곡가 정보 보기</a>
        </section>

        <section>
            <h2>공연 일정</h2>
            <p>클래식 공연 일정을 확인하고, 가까운 공연을 놓치지 마세요!</p>
            <a href="events/event_list.php">공연 일정 보기</a>
        </section>
    </main>
</body>
</html>

