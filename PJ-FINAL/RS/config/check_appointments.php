<?php
session_start();
include 'connect_database.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['id'];  // ดึง patient_id จาก session

// ดึงข้อมูลการนัดหมายที่ยังไม่ได้อ่าน
$sql = "SELECT appointment_id, appointment_date, updated_at, is_read
        FROM appointments 
        WHERE patient_id = ? 
        AND is_read = 0
        ORDER BY updated_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// เช็คว่ามีการอัปเดตใหม่หรือไม่
$appointments = [];
$unreadCount = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = [
            'appointment_id' => $row['appointment_id'],
            'appointment_date' => $row['appointment_date'],
            'updated_at' => $row['updated_at']
        ];
    }
}

// เช็คจำนวนการแจ้งเตือนที่ยังไม่ได้อ่าน
$unreadCountQuery = "SELECT COUNT(*) as unread_count FROM appointments WHERE patient_id = ? AND is_read = 0";
$unreadCountStmt = $conn->prepare($unreadCountQuery);
$unreadCountStmt->bind_param("s", $user_id);
$unreadCountStmt->execute();
$unreadCountResult = $unreadCountStmt->get_result();
$unreadCountRow = $unreadCountResult->fetch_assoc();
$unreadCount = $unreadCountRow['unread_count']; // ทำให้ sure อ่านค่าได้ถูกต้อง

// ส่งข้อมูลกลับ
echo json_encode([
    'status' => 'success',
    'appointments' => $appointments,
    'unread_count' => $unreadCount // ส่งคืนจำนวนการแจ้งเตือนที่ยังไม่ได้อ่าน
]);

$stmt->close();
$conn->close();
?>
