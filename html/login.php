<?php
session_start();

// load DB conn Setting, include config/db.php
require_once 'config/db.php';

// Check Submitting Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check Basical Validation 
    if (empty($email) || empty($password)) {
        $error_message = "이메일과 비밀번호를 입력해주세요.";
    } else {
        try {
            // Check user through Email addr
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Check User's existing, correcting PW
            if ($user && password_verify($password, $user['password'])) {
                // save UserInfo in Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // if login Success, redirect index page
                header("Location: index.php");
                exit();
            } else {
                $error_message = "이메일 또는 비밀번호가 일치하지 않습니다.";
            }
        } catch (PDOException $e) {
            $error_message = "로그인 중 오류가 발생했습니다: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>로그인</h2>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="login.php" method="post">
        <label for="email">이메일:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">로그인</button>
    </form>
</body>
</html>

