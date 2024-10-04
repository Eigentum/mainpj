<?php
session_start();

// check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// load DB conn Setting through config/db.php
require_once '../config/db.php';

// check submitted form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // 카테고리 설정 (GET 파라미터에서 받기, 예: 토론 게시판)
    $category = isset($_GET['category']) ? $_GET['category'] : 'general'; // 기본값은 'general'
    
    // check Validation
    if (empty($title) || empty($content)) {
        $error_message = "제목과 내용을 모두 입력해주세요.";
    } else {
        try {
            // insert to DB
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $user_id, $category]);

            // 특정 카테고리로 리다이렉션
            if ($category === 'discussion') {
                header("Location: discussions.php");
            } else if ($category === 'reviews') {
                header("Location: reviews.php");
            } else {
                header("Location: board.php");  // 기본 게시판으로 리다이렉션
            }
            exit();
        } catch (PDOException $e) {
            $error_message = "게시물 작성 중 오류가 발생했습니다: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 작성</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>게시물 작성</h2>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="write.php?category=<?php echo htmlspecialchars($_GET['category']); ?>" method="post">
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">작성하기</button>
    </form>
</body>
</html>

