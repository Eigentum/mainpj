<?php
$sidebar_menu = [];

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
        <?php foreach ($sidebar_menu as $menu_name => $menu_link): ?>
            <li><a href="<?php echo $menu_link; ?>"><?php echo $menu_name; ?></a></li>
        <?php endforeach; ?>
    </ul>
</aside>

