<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sidebar_menu = [];

// 로그인 여부 확인
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';

// 게시판 관련 페이지
if (strpos($_SERVER['SCRIPT_NAME'], '/board') !== false) {
    $sidebar_menu = [
        '전체 게시판' => '/board/board.php',
        '토론 게시판' => '/board/discussions.php',
        '리뷰 게시판' => '/board/reviews.php'
    ];
}

// 음악 관련 페이지
if (strpos($_SERVER['SCRIPT_NAME'], '/music') !== false) {
    $sidebar_menu = [
        '작곡가 정보' => '/music/composers.php',
        '추천 음악' => '/music/recommendations.php',
    ];
}
?>

<aside class="sidebar">
    <ul>
        <?php if ($is_logged_in): ?>
            <li class="welcome-box">
                안녕하세요,<br><strong><?php echo $username; ?></strong> 님!
            </li>
            <li><a href="/profile.php">프로필</a></li>
            <li><a href="/logout.php">로그아웃</a></li>
        <?php else: ?>
            <li>
                <form action="/login.php" method="post">
                    <label for="username">아이디:</label>
                    <input type="text" id="username" name="username" required><br>
                    <label for="password">비밀번호:</label>
                    <input type="password" id="password" name="password" required><br>
                    <button type="submit">로그인</button> <a href="/register.php">회원가입</a>
                </form>
            </li>
        <?php endif; ?>

	<hr>

        <!-- 하위 메뉴 출력 -->
        <?php foreach ($sidebar_menu as $menu_name => $menu_link): ?>
            <li><a href="<?php echo $menu_link; ?>"><?php echo $menu_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
</aside>

