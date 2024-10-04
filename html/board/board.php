<?php
include '../includes/header.php';

// Load DB conn Setting through config/db.php
require_once '../config/db.php';

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판 목록</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> 
</head>
<body>
<div class="container">
 <?php include '../includes/sidebar.php'; ?>
    <main>
        <section>
            <h2>게시물 목록</h2>
            <a href="write.php">글쓰기</a>
            <ul>
                <?php
                // Load Post List
                $stmt = $pdo->prepare("SELECT id, title, created_at FROM posts ORDER BY created_at DESC");
                $stmt->execute();
                $posts = $stmt->fetchAll();

                foreach ($posts as $post):
                    // Check if the post has attached files
                    $file_stmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE post_id = ?");
                    $file_stmt->execute([$post['id']]);
                    $has_file = $file_stmt->fetchColumn() > 0;
                ?>
                    <li>
                        <a href="view.php?id=<?php echo $post['id']; ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                            <!-- 파일 첨부 아이콘 표시 -->
                            <?php if ($has_file): ?>
                                <img src="../assets/images/file_icon.png" alt="파일 첨부" style="width:16px;height:16px;">
                            <?php endif; ?>
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

