<?php 
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "로그인이 필요합니다.";
        exit();
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $comment = trim($_POST['comment']);
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : NULL;  // 부모 댓글 ID
    $user_id = $_SESSION['user_id'];

    if (empty($comment) || $post_id === 0) {
        echo "유효한 댓글과 게시물 ID가 필요합니다.";
        exit();
    }

    // 댓글 또는 답글 추가
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment, parent_comment_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $comment, $parent_comment_id]);

    // 게시물에 대한 댓글 및 답글 목록 가져오기
    $stmt = $pdo->prepare("SELECT comments.*, users.username 
                           FROM comments 
                           JOIN users ON comments.user_id = users.id 
                           WHERE comments.post_id = ? ORDER BY comments.created_at ASC");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll();

    // 댓글 및 답글 출력
    foreach ($comments as $comment) {
        // print normal reply(
        if ($comment['parent_comment_id'] == NULL) {
            echo "<li><strong>" . htmlspecialchars($comment['username']) . "</strong> (" . $comment['created_at'] . "):<br>" . nl2br(htmlspecialchars($comment['comment'])) . "</li>";
            
            // print reply about origin reply
            foreach ($comments as $reply) {
                if ($reply['parent_comment_id'] == $comment['id']) {
                    echo "<ul><li><strong>" . htmlspecialchars($reply['username']) . "</strong> (" . $reply['created_at'] . "):<br>" . nl2br(htmlspecialchars($reply['comment'])) . "</li></ul>";
                }
            }

            // 답글 폼 출력
            echo '<form action="comment.php" method="post">
                    <input type="hidden" name="post_id" value="' . $post_id . '">
                    <input type="hidden" name="parent_comment_id" value="' . $comment['id'] . '">
                    <textarea name="comment" placeholder="답글 입력"></textarea>
                    <button type="submit">답글 달기</button>
                  </form>';
        }
    }
}

