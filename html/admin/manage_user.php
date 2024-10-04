<?php
session_start();
require_once '../config/db.php';

// Check Authority(Admin)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// print Users List
$stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();

// Request delete User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    
    // delete user process
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_user_id]);

    header("Location: manage_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 관리</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>사용자 관리</h2>
    <table>
        <thead>
            <tr>
                <th>아이디</th>
                <th>사용자 이름</th>
                <th>이메일</th>
                <th>가입일</th>
                <th>삭제</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <form action="manage_user.php" method="post" onsubmit="return confirm('정말로 이 사용자를 삭제하시겠습니까?');">
                            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit">삭제</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

