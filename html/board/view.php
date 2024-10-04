<?php
// Load post, uploaded file's info
$post_id = $_GET['id'];

// load post info
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

// load uploaded File's info
$file_stmt = $pdo->prepare("SELECT * FROM uploads WHERE post_id = ?");
$file_stmt->execute([$post_id]);
$file = $file_stmt->fetch();
?>

<!-- print Post content -->
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

<?php if ($file): ?>
    <p>첨부 파일: <a href="download.php?id=<?php echo $file['id']; ?>"><?php echo htmlspecialchars($file['file_name']); ?> 다운로드</a></p>
<?php endif; ?>

<!-- post recommendation Qty & Recommendation Button -->
<p>추천 수: <?php echo $post['like_count']; ?></p>

<?php if (isset($_SESSION['user_id'])): ?>
    <form action="like_post.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <button type="submit">추천하기</button>
    </form>
<?php endif; ?>

<!-- show edit link, if current user is same post writer... -->
    <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
	<p><a href="edit_post.php?id=<?php echo $post_id; ?>">게시물 수정</a></p>
	<p><a href="delete_post.php?id=<php echo $post_id; ?>" onclick="return confirm('이 게시물을 삭제하시겠습니까?');">게시물 삭제</a></p>
    <?php endif; ?>
<?php endif; ?>

<!-- print or insert reply -->
<section>
    <h2>댓글</h2>
    <?php include 'comment.php'; ?>
</section>

