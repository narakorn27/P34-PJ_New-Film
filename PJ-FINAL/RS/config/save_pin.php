<?php
session_start();
header('Content-Type: application/json');

include 'connect_database.php'; // ใช้ไฟล์เชื่อมฐานข้อมูล

// ตรวจสอบว่า User ล็อกอินอยู่หรือไม่
if (!isset($_SESSION["id"])) {
    echo json_encode(["success" => false, "message" => "คุณยังไม่ได้เข้าสู่ระบบ"]);
    exit();
}

$userId = $_SESSION["id"]; // ใช้ ID จาก Session
$pin = $_POST["pin"] ?? ''; // รับ PIN ที่ส่งมา

// ตรวจสอบค่าที่รับเข้ามา
if (empty($pin)) {
    echo json_encode(["success" => false, "message" => "กรุณากรอก PIN"]);
    exit();
}

// ใช้การ Hash PIN เพื่อความปลอดภัย
$hashedPin = password_hash($pin, PASSWORD_DEFAULT);

// อัปเดต PIN ลงฐานข้อมูล
$stmt = $conn->prepare("UPDATE patients_login SET pin_code = ? WHERE id = ?");
$stmt->bind_param("ss", $hashedPin, $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการบันทึก PIN"]);
}

$stmt->close();
$conn->close();
?>
