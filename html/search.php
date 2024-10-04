<?php
require_once 'config/db.php';
// Redis 연결 (여기서 Redis는 설치 및 설정된 상태여야 합니다)
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Get the keyword and other filters
$search_query = isset($_GET['q']) ? $_GET['q'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at_desc';
$search_in = isset($_GET['search_in']) ? $_GET['search_in'] : 'all';
$tag = isset($_GET['tag']) ? $_GET['tag'] : ''; 

// set pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 캐시 키 생성 (검색어와 페이지 번호를 조합)
$cache_key = "search_" . md5($search_query . "_page_" . $page);

// 캐시된 결과 확인
$cached_results = $redis->get($cache_key);

if ($cached_results) {
    // 캐시된 결과 사용
    $results = json_decode($cached_results, true);
    $is_cached = true;
} else {
    // 캐시가 없을 경우 데이터베이스 쿼리 실행
    $is_cached = false;
    if (!empty($search_query)) {
        // Full-Text 검색 쿼리
        $sql = "SELECT DISTINCT p.* FROM posts p 
                LEFT JOIN comments c ON p.id = c.post_id 
                LEFT JOIN post_tags pt ON p.id = pt.post_id
                LEFT JOIN tags t ON pt.tag_id = t.id
                WHERE MATCH(p.title, p.content) AGAINST(? IN BOOLEAN MODE)";

        $params = [$search_query];

        // 필터 추가
        if (!empty($tag)) {
            $sql .= " AND t.name = ?";
            $params[] = $tag;
        }
        if (!empty($author)) {
            $sql .= " AND p.user_id = ?";
            $params[] = $author;
        }
        if (!empty($start_date)) {
            $sql .= " AND p.created_at >= ?";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $sql .= " AND p.created_at <= ?";
            $params[] = $end_date;
        }

        // 정렬 추가
        if ($sort_by == 'created_at_asc') {
            $sql .= " ORDER BY p.created_at ASC";
        } elseif ($sort_by == 'title_asc') {
            $sql .= " ORDER BY p.title ASC";
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }

        // 페이지네이션 추가
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // 결과를 캐싱 (1시간 동안 유지)
        $redis->set($cache_key, json_encode($results), 3600); // 캐시 1시간 유지
    }
}

// 검색 로그 기록
if (!empty($search_query)) {
    $log_stmt = $pdo->prepare("INSERT INTO search_logs (query, user_id, search_time) VALUES (?, ?, NOW())");
    $log_stmt->execute([$search_query, $_SESSION['user_id'] ?? null]);
}

// Highlight function
function highlight_keyword($text, $keyword) {
    $pattern = '/(.{0,50})(' . preg_quote($keyword, '/') . ')(.{0,50})/i';
    return preg_replace($pattern, '<span class="ellipsis">...$1</span><mark>$2</mark><span class="ellipsis">$3...</span>', $text);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 검색</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        function searchPosts() {
            var query = document.getElementById('search_input').value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "search_ajax.php?q=" + encodeURIComponent(query), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('search_results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <h2>게시물 검색</h2>
    <form action="search.php" method="get">
        <input type="text" id="search_input" name="q" placeholder="검색어 입력" oninput="searchPosts()" required>
        <div id="search_results"></div>

        <!-- 필터링 및 정렬 UI... -->
        
        <button type="submit">검색</button>
    </form>

    <?php if (!empty($search_query)): ?>
        <h3>검색 결과 (<?php echo $is_cached ? "캐시된 결과" : "새 쿼리 결과"; ?>)</h3>
        <?php if (!empty($results)): ?>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li>
                        <a href="board/view.php?id=<?php echo $result['id']; ?>">
                            <?php echo highlight_keyword(htmlspecialchars($result['title']), $search_query); ?>
                        </a>
                        - <?php echo date('Y-m-d H:i', strtotime($result['created_at'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- 페이지네이션 처리 -->
        <?php else: ?>
            <p>검색 결과가 없습니다.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

