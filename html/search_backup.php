<?php
require_once 'config/db.php';

// Get the parameters from GET request
$search_query = isset($_GET['q']) ? $_GET['q'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$search_in = isset($_GET['search_in']) ? $_GET['search_in'] : 'all';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at_desc';
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

// Pagination settings
$limit = 10; // Results per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit;

if (!empty($search_query)) {
    // Base SQL query
    $sql = "SELECT DISTINCT p.* FROM posts p 
            LEFT JOIN comments c ON p.id = c.post_id 
            LEFT JOIN post_tags pt ON p.id = pt.post_id
            WHERE ";

    // Search scope (title, content, comments)
    $params = [];
    if ($search_in == 'title') {
        $sql .= "p.title LIKE ?";
        $params[] = "%$search_query%";
    } elseif ($search_in == 'content') {
        $sql .= "p.content LIKE ?";
        $params[] = "%$search_query%";
    } elseif ($search_in == 'comments') {
        $sql .= "c.comment LIKE ?";
        $params[] = "%$search_query%";
    } else {
        $sql .= "(p.title LIKE ? OR p.content LIKE ? OR c.comment LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }

    // Filter by author
    if (!empty($author)) {
        $sql .= " AND p.user_id = ?";
        $params[] = $author;
    }

    // Filter by date range
    if (!empty($start_date)) {
        $sql .= " AND p.created_at >= ?";
        $params[] = $start_date;
    }
    if (!empty($end_date)) {
        $sql .= " AND p.created_at <= ?";
        $params[] = $end_date;
    }

    // Filter by tag
    if (!empty($tag)) {
        $sql .= " AND pt.tag_id = ?";
        $params[] = $tag;
    }

    // Sorting
    if ($sort_by == 'created_at_asc') {
        $sql .= " ORDER BY p.created_at ASC";
    } elseif ($sort_by == 'title_asc') {
        $sql .= " ORDER BY p.title ASC";
    } else {
        $sql .= " ORDER BY p.created_at DESC";
    }

    // Add pagination
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    // Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Count the total results for pagination
    $count_sql = "SELECT COUNT(DISTINCT p.id) FROM posts p 
                  LEFT JOIN comments c ON p.id = c.post_id 
                  LEFT JOIN post_tags pt ON p.id = pt.post_id
                  WHERE (p.title LIKE ? OR p.content LIKE ? OR c.comment LIKE ?)";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute(array_slice($params, 0, 3)); // Search keyword only
    $total_results = $count_stmt->fetchColumn();

    // Comment preview query
    $comments_stmt = $pdo->prepare("SELECT comment FROM comments WHERE post_id = ? AND comment LIKE ? LIMIT 3");
}

// Highlight keyword function
function highlight_keyword($text, $keyword) {
    return preg_replace('/('.preg_quote($keyword, '/').')/i', '<mark>$1</mark>', $text);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 검색</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>게시물 검색</h2>
    <form action="search.php" method="get">
        <!-- 검색어 입력 -->
        <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="검색어 입력" required>

        <!-- 작성자 필터링 -->
        <label for="author">작성자:</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>">

        <!-- 날짜 필터링 -->
        <label for="start_date">시작 날짜:</label>
        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        <label for="end_date">종료 날짜:</label>
        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">

        <!-- 검색 범위 선택 -->
        <label for="search_in">검색 범위:</label>
        <select name="search_in">
            <option value="all" <?php if ($search_in == 'all') echo 'selected'; ?>>제목+내용+댓글</option>
            <option value="title" <?php if ($search_in == 'title') echo 'selected'; ?>>제목</option>
            <option value="content" <?php if ($search_in == 'content') echo 'selected'; ?>>내용</option>
            <option value="comments" <?php if ($search_in == 'comments') echo 'selected'; ?>>댓글</option>
        </select>

        <!-- 정렬 선택 -->
        <label for="sort_by">정렬 기준:</label>
        <select name="sort_by">
            <option value="created_at_desc" <?php if ($sort_by == 'created_at_desc') echo 'selected'; ?>>최신순</option>
            <option value="created_at_asc" <?php if ($sort_by == 'created_at_asc') echo 'selected'; ?>>오래된순</option>
            <option value="title_asc" <?php if ($sort_by == 'title_asc') echo 'selected'; ?>>제목순</option>
        </select>

        <!-- 태그 필터링 -->
        <label for="tag">태그:</label>
        <input type="text" name="tag" value="<?php echo htmlspecialchars($tag); ?>">

        <button type="submit">검색</button>
    </form>

    <?php if (!empty($search_query)): ?>
        <h3>검색 결과</h3>
        <p>총 <?php echo $total_results; ?>개의 검색 결과</p>
        <?php if (!empty($results)): ?>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li>
                        <a href="board/view.php?id=<?php echo $result['id']; ?>">
                            <?php echo highlight_keyword(htmlspecialchars($result['title']), $search_query); ?>
                        </a>
                        - <?php echo date('Y-m-d H:i', strtotime($result['created_at'])); ?>
                        
                        <!-- 댓글 미리보기 -->
                        <?php 
                        $comments_stmt->execute([$result['id'], "%$search_query%"]);
                        $comments = $comments_stmt->fetchAll();
                        if ($comments): ?>
                            <ul>
                                <?php foreach ($comments as $comment): ?>
                                    <li><?php echo highlight_keyword(htmlspecialchars($comment['comment']), $search_query); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <!-- 페이지네이션 -->
            <?php if ($total_results > $limit): ?>
                <nav>
                    <ul>
                        <?php
                        $total_pages = ceil($total_results / $limit);
                        for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="search.php?q=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>&author=<?php echo urlencode($author); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&search_in=<?php echo urlencode($search_in); ?>&sort_by=<?php echo urlencode($sort_by); ?>&tag=<?php echo urlencode($tag); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <p>검색 결과가 없습니다.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

