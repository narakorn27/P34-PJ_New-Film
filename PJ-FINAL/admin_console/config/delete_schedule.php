<?php
include 'db.php'; // สมมติว่า $pdo ถูกกำหนดใน db.php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM medical_schedule WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(["success" => true, "message" => "ตารางเวรถูกลบแล้ว"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ไม่มีข้อมูลที่ถูกส่งมา"]);
}
?>