<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์
$username = "entcenterl_pj_final";
$password = "New_Film_2024";
$dbname = "entcenterl_finalpro"; // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$username = $_POST['username'];
$password = $_POST['password'];

// ค้นหาผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM medical_staff WHERE username = '$username'";
$result = $conn->query($sql);

// ตรวจสอบว่าเจอผู้ใช้ไหม
if ($result->num_rows > 0) {
    // ผู้ใช้พบ
    $row = $result->fetch_assoc();
    
    // ตรวจสอบรหัสผ่าน
    if (password_verify($password, $row['password'])) {
        // Login สำเร็จ
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        // รหัสผ่านไม่ถูกต้อง
        echo json_encode(['status' => 'error', 'message' => 'Invalid password!']);
    }
} else {
    // ไม่พบผู้ใช้
    echo json_encode(['status' => 'error', 'message' => 'No user found with that username!']);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
