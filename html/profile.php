<?php
session_start();
require_once 'config/db.php';

// Check the User
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Load User's info
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Update User's info, When submit form.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Update Email
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $user_id]);
        $success_message = "이메일이 성공적으로 업데이트되었습니다.";
    }

    // Update Password 
    if (!empty($password) && $password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $success_message = "비밀번호가 성공적으로 변경되었습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>프로필 관리</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>프로필 관리</h2>

    <?php if (isset($success_message)): ?>
        <p><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form action="profile.php" method="post">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>

        <label for="email">이메일:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="password">새 비밀번호:</label>
        <input type="password" id="password" name="password">

        <label for="confirm_password">비밀번호 확인:</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <button type="submit">정보 업데이트</button>
    </form>

    <a href="logout.php">로그아웃</a>
</body>
</html>

