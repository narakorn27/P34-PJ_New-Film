<?php
session_start();
include('connect_database.php');

// ตรวจสอบ session
if (isset($_SESSION['id'])) {
    $patient_id = $_SESSION['id'];
    if (isset($_POST['additional_details'])) {
        $additional_details = $_POST['additional_details'];

        // SQL Update
        $sql = "UPDATE patients_info SET additional_details = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $additional_details, $patient_id); // เปลี่ยนเป็น 'ss' ถ้า id เป็น string

        if ($stmt->execute()) {
            // ส่งข้อความสำเร็จ
            echo "Update successful";
        } else {
            // ส่งข้อความข้อผิดพลาด
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // ส่งข้อความหากไม่มีข้อมูล
        echo "No additional details received.";
    }
} else {
    // ส่งข้อความหาก session ไม่ถูกตั้ง
    echo "Session not set.";
}

$conn->close();
?>
