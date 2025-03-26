<?php
session_start();
header('Content-Type: application/json');

include 'connect_database.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า User ล็อกอินอยู่หรือไม่
if (!isset($_SESSION["id"])) {
    echo json_encode(["success" => false, "message" => "คุณยังไม่ได้เข้าสู่ระบบ"]);
    exit();
}

$userId = $_SESSION["id"]; // ใช้ ID จาก Session
$currentPin = $_POST["currentPin"] ?? ''; // รับ PIN ปัจจุบัน
$newPin = $_POST["newPin"] ?? ''; // รับ PIN ใหม่

// ตรวจสอบค่าที่รับเข้ามา
if (empty($currentPin) || empty($newPin)) {
    echo json_encode(["success" => false, "message" => "กรุณากรอกทั้ง PIN ปัจจุบันและ PIN ใหม่"]);
    exit();
}

// ตรวจสอบว่า PIN ใหม่มีความยาว 6 หลัก
if (strlen($newPin) !== 6) {
    echo json_encode(["success" => false, "message" => "รหัส PIN ใหม่ต้องมีความยาว 6 หลัก"]);
    exit();
}

// ตรวจสอบ PIN ปัจจุบันกับฐานข้อมูล
$stmt = $conn->prepare("SELECT pin_code FROM patients_login WHERE id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// เช็คว่า PIN ปัจจุบันถูกต้องหรือไม่
if (!$row || !password_verify($currentPin, $row['pin_code'])) {
    echo json_encode(["success" => false, "message" => "PIN ปัจจุบันไม่ถูกต้อง"]);
    exit();
}

// Hash PIN ใหม่เพื่อความปลอดภัย
$hashedNewPin = password_hash($newPin, PASSWORD_DEFAULT);

// อัปเดต PIN ใหม่ในฐานข้อมูล
$stmt = $conn->prepare("UPDATE patients_login SET pin_code = ? WHERE id = ?");
$stmt->bind_param("ss", $hashedNewPin, $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "เปลี่ยนรหัส PIN สำเร็จ"]);
} else {
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการบันทึก PIN"]);
}

$stmt->close();
$conn->close();
?>
