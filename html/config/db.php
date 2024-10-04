<?php
// connect DB settings
$host = "localhost";
$dbname = "mainpj_db"; 
$username = "root";         
$password = "1234";             

try {
    // connect MySQL through PDO Object
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO Error mode to except mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 연결 성공 시 메시지 (개발 중에만 사용, 배포 시에는 주석 처리 또는 삭제)
    // echo "데이터베이스 연결 성공!";
} catch (PDOException $e) {
    // 연결 실패 시 오류 메시지 출력
    die("데이터베이스 연결 실패: " . $e->getMessage());
}
?>

