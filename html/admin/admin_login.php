<?php
session_start();
require_once '../config/db.php';

// Check Submited form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // Check Admin Account (Hardcoding admin Account info...)
    $correct_admin_username = "admin";
    $correct_admin_password = "adminnimda";

    if ($admin_username === $correct_admin_username && $admin_password === $correct_admin_password) {
        // Login Successful
        $_SESSION['is_admin'] = true;
        header("Location: manage_user.php");
        exit();
    } else {
        $error_message = "잘못된 관리자 계정 정보입니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 로그인</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>관리자 로그인</h2>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="admin_login.php" method="post">
        <label for="admin_username">아이디:</label>
        <input type="text" id="admin_username" name="admin_username" required>

        <label for="admin_password">비밀번호:</label>
        <input type="password" id="admin_password" name="admin_password" required>

        <button type="submit">로그인</button>
    </form>
</body>
</html>

