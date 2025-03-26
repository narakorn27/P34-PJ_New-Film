<?php
require 'db.php'; // เชื่อมต่อฐานข้อมูล

header("Content-Type: application/json"); // บอกให้ browser รู้ว่าเราส่ง JSON
error_reporting(0); // ปิด Error Output (ถ้าไม่อยากให้แสดง Error ใน JSON)
ini_set('display_errors', 0); // ปิด Error Output

if (isset($_GET['id_card'])) {
    $id_card = $_GET['id_card'];

    $stmt = $conn->prepare("SELECT * FROM patients_info WHERE id_card = :id_card");
    $stmt->bindParam(':id_card', $id_card);
    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        echo json_encode(["status" => "found"] + $patient);
    } else {
        echo json_encode(["status" => "not_found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing id_card parameter"]);
}
?>
