<?php
session_start();
require_once '../config/db.php';

// Check Authority
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 대시보드</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>관리자 대시보드</h2>
    <ul>
        <li><a href="manage_user.php">사용자 관리</a></li>
        <li><a href="manage_posts.php">게시물 관리</a></li>
        <li><a href="../logout.php">로그아웃</a></li>
    </ul>
</body>
</html>

