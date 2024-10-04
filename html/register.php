<?php
// load DB conn.setting, include config/db.php
require_once 'config/db.php';

// Check to Submitting Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check the Basical Validation(empty field, correct pw)
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "모든 필드를 입력해주세요.";
    } elseif ($password !== $confirm_password) {
        $error_message = "비밀번호가 일치하지 않습니다.";
    } else {
        // Password Hashing
	    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
	
	// Password unHashing(Vulnerability) delete this later...
	    $hashed_password = $password;



        try {
            // Check email addr. 
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $email_count = $stmt->fetchColumn();

            if ($email_count > 0) {
                $error_message = "이미 등록된 이메일입니다.";
            } else {
                // Save UserInfo to DB
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);

                // if Success, redirect to Login page
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "회원가입 중 오류가 발생했습니다: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>회원가입</h2>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="register.php" method="post">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">이메일:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">비밀번호 확인:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">회원가입</button>
    </form>
</body>
</html>
