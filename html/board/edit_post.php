<?php
session_start();
require_once '../config/db.php';

// Check the 'login'
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "로그인이 필요합니다.";
        exit();
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $content = trim($_POST['content']);
    $title = trim($_POST['title']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content) || $post_id === 0) {
        echo "모든 필드를 입력해주세요.";
        exit();
    }

    // check post writer
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post['user_id'] != $user_id) {
        echo "게시물을 수정할 권한이 없습니다.";
        exit();
    }

    // edit post title & content
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $content, $post_id]);

// Process File upload 
    if (!empty($_FILES['file']['name'])) {
        $file_name = basename($_FILES['file']['name']);
        $file_tmp = $_FILES['file']['tmp_name'];
        $upload_dir = '../uploads/';
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check Allow File type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
        if (in_array($file_type, $allowed_types)) {
            // File upload
            if (move_uploaded_file($file_tmp, $target_file)) {
                // process delete origin file기존 파일 삭제 처리
                $stmt = $pdo->prepare("SELECT * FROM uploads WHERE post_id = ?");
                $stmt->execute([$post_id]);
                $existing_file = $stmt->fetch();
                if ($existing_file) {
                    unlink($existing_file['file_path']);  // delete origin file
                    $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
                    $stmt->execute([$existing_file['id']]);
                }

                // save new file info
                $stmt = $pdo->prepare("INSERT INTO uploads (post_id, file_name, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$post_id, $file_name, $target_file]);
            } else {
                echo "파일 업로드에 실패했습니다.";
                exit();
            }
        } else {
            echo "허용되지 않는 파일 형식입니다.";
            exit();
        }
    }

    // Redirect post list page after edit post
    header("Location: view.php?id=" . $post_id);
    exit();
} else {
    // load post info (when GET request)
    $post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if (!$post) {
        echo "게시물을 수정할 권한이 없습니다.";
        exit();
    }

    // Load uploaded file info
    $file_stmt = $pdo->prepare("SELECT * FROM uploads WHERE post_id = ?");
    $file_stmt->execute([$post_id]);
    $file = $file_stmt->fetch();

}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 수정</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>게시물 수정</h2>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        
        <label for="content">내용:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        
        <button type="submit">수정 완료</button>
    </form>

<?php if ($file): ?>
        <p>첨부 파일: <?php echo htmlspecialchars($file['file_name']); ?></p>
        <?php if (in_array(strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])): ?>
            <!-- 이미지 미리보기 -->
            <img src="../uploads/<?php echo htmlspecialchars($file['file_name']); ?>" alt="이미지 미리보기" style="max-width: 300px;">
        <?php endif; ?>
        <p><a href="delete_file.php?id=<?php echo $file['id']; ?>" onclick="return confirm('정말로 이 파일을 삭제하시겠습니까?');">파일 삭제</a></p>
    <?php endif; ?>

</body>
</html>

