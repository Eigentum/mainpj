<?php
session_start();
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "아이디와 비밀번호를 모두 입력해주세요.";
    } else {
        // 사용자 정보 가져오기
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // 사용자가 존재하고, 비밀번호가 맞는지 확인
        if ($user && $password === $user['password']) {
            // 로그인 성공, 세션에 사용자 정보 저장
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error_message = "아이디 또는 비밀번호가 잘못되었습니다.";
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
    <div class="container">
        <main>
            <h2>로그인</h2>

            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <label for="username">아이디:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">비밀번호:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">로그인</button>
            </form>
        </main>
    </div>
</body>
</html>

