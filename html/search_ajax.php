<?php
require_once 'config/db.php';

$search_query = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($search_query)) {
    // 제목에 대한 검색 결과
    $stmt = $pdo->prepare("SELECT title, id FROM posts WHERE title LIKE ? LIMIT 5");
    $stmt->execute(["%$search_query%"]);
    $results = $stmt->fetchAll();

    // 검색 결과 출력
    foreach ($results as $result) {
        echo '<p><a href="board/view.php?id=' . $result['id'] . '">' . htmlspecialchars($result['title']) . '</a></p>';
    }
}
?>

