<?php
require_once 'config/db.php';

// Get FileID to 'GET' Parameter 
$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($file_id === 0) {
    die("유효한 파일 ID가 필요합니다.");
}

// Load File's Info
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    die("파일을 찾을 수 없습니다.");
}

// Set File's Path
$file_path = $file['file_path'];
$file_name = $file['file_name'];

// Check exist file
if (file_exists($file_path)) {
    // Set file Download Header
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    readfile($file_path);
    exit();
} else {
    die("파일을 찾을 수 없습니다.");
}
?>

