<?php
session_start();
include 'connect_database.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['id'];  // ดึง patient_id จาก session

// อัปเดตสถานะการอ่านการนัดหมายเป็น 1 (เมื่อผู้ใช้กดกระดิ่ง)
$sql = "UPDATE appointments SET is_read = 1 
        WHERE patient_id = ? AND is_read = 0"; // เฉพาะการนัดหมายที่ยังไม่ได้อ่าน

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Notification marked as read']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update notification status']);
}

$stmt->close();
$conn->close();
?>

