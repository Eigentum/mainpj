<?php
session_start();
require_once '../config/db.php';

// Check Authority
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check Post list
$stmt = $pdo->prepare("SELECT posts.id, posts.title, posts.created_at, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll();

// Process delete post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post_id'])) {
    $delete_post_id = intval($_POST['delete_post_id']);

    // delete post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$delete_post_id]);

    header("Location: manage_posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 관리</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>게시물 관리</h2>
    <table>
        <thead>
            <tr>
                <th>게시물 ID</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
                <th>삭제</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo $post['id']; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['username']); ?></td>
                    <td><?php echo $post['created_at']; ?></td>
                    <td>
                        <form action="manage_posts.php" method="post" onsubmit="return confirm('정말로 이 게시물을 삭제하시겠습니까?');">
                            <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit">삭제</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

