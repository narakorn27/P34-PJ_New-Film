<?php

$servername = "localhost";
$username = "entcenterl_pj_final";
$password = "New_Film_2024";
$dbname = "entcenterl_finalpro"; // ชื่อฐานข้อมูลของคุณ


// เชื่อมต่อฐานข้อมูล
$conn = @new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // ตั้งค่ารหัสอักขระเป็น utf8mb4
    if (!$conn->set_charset("utf8mb4")) {
        die("Error loading character set utf8mb4: " . $conn->error);
    }
    // echo "เชื่อมต่อฐานข้อมูลสำเร็จพร้อมตั้งค่ารหัสอักขระ!";
}
?>