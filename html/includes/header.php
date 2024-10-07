<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<header class="site-header">
    <h1>커뮤니티 사이트</h1>
    <nav class="main-nav">
        <ul>
            <li><a href="../index.php">홈</a></li>
            <li><a href="../board/board.php">게시판</a></li>
            <li><a href="../music/composers.php">작곡가 정보</a></li>
            <li><a href="../music/recommendations.php">추천 음악</a></li>
            <li><a href="../events/event_list.php">공연 일정</a></li>
        </ul>
    </nav>
</header>

