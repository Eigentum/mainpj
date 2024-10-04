<?php
session_start();
require_once '../config/db.php';

// Check Login_status
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// check uploaded files
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $user_id = $_SESSION['user_id'];

    // Load File_Info
    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

    // Apply File Size limit
    $max_file_size = 10 * 1024 * 1024;
    if ($file_size > $max_file_size) {
        echo "파일 크기가 10MB를 초과합니다.";
        exit();
    }

    // Allowed File Type
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_type), $allowed_types)) {
        echo "허용되지 않는 파일 형식입니다. PDF, 이미지 파일만 업로드 가능합니다.";
        exit();
    }

    // Save File's Path
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);  // 디렉토리 없을 경우 생성
    }

    // create Default name for save file
    $new_file_name = uniqid() . '.' . $file_type;
    $file_path = $upload_dir . $new_file_name;

    // file move to Server
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Save File's info to DB
        $stmt = $pdo->prepare("INSERT INTO uploads (user_id, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $file_name, $file_path]);

        echo "파일이 성공적으로 업로드되었습니다.";
    } else {
        echo "파일 업로드에 실패했습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>파일 업로드</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>파일 업로드</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">파일 선택:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">업로드</button>
    </form>
</body>
</html>

