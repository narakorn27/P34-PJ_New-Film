<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['work_day'], $_POST['category'])) {
    $id = $_POST['id'];
    $work_day = $_POST['work_day'];
    $category = $_POST['category'];

    try {
        // ตรวจสอบว่าแพทย์และวันทำงานซ้ำในฐานข้อมูลหรือไม่
        $checkQuery = "SELECT COUNT(*) FROM medical_schedule WHERE doctor_id = (SELECT doctor_id FROM medical_schedule WHERE id = ?) AND work_day = ?";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bindParam(1, $id, PDO::PARAM_INT);
        $stmtCheck->bindParam(2, $work_day, PDO::PARAM_STR);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        // ถ้ามีข้อมูลซ้ำ
        if ($count > 0) {
            echo json_encode(["success" => false, "message" => "ข้อมูลแพทย์และวันทำงานนี้มีอยู่ในระบบแล้ว"]);
            exit;  // หยุดการทำงานถ้ามีข้อมูลซ้ำ
        }

        // อัปเดตข้อมูลถ้าไม่พบการซ้ำ
        $updateQuery = "UPDATE medical_schedule SET work_day = ?, category = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bindParam(1, $work_day, PDO::PARAM_STR);
        $stmtUpdate->bindParam(2, $category, PDO::PARAM_STR);
        $stmtUpdate->bindParam(3, $id, PDO::PARAM_INT);
        $result = $stmtUpdate->execute();

        if ($result) {
            echo json_encode(["success" => true, "message" => "บันทึกการเปลี่ยนแปลงแล้ว"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถอัปเดตข้อมูลได้"]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ไม่มีข้อมูลที่ถูกส่งมา"]);
}
?>
