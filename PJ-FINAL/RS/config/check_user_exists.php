<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// รับค่าจากฟอร์ม
$id_card = $_POST['id_card'];
$date_of_birth = $_POST['date_of_birth'];
$account_id = $_POST['account_id'];

// ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
if (empty($id_card) || empty($date_of_birth) || empty($account_id)) {
    echo json_encode(['error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit();
}

// แปลงวันที่เกิดจากรูปแบบ 'YYYY-MM-DD' เป็น 'DDMMYYYY'
$date_of_birth_format = DateTime::createFromFormat('Y-m-d', $date_of_birth);
if ($date_of_birth_format) {
    $new_password = $date_of_birth_format->format('dmY');  // รูปแบบ 'DDMMYYYY'
} else {
    echo json_encode(['error' => 'รูปแบบวันที่ไม่ถูกต้อง']);
    exit();
}

// ตรวจสอบว่าผู้ใช้มีอยู่แล้วหรือไม่ (ค้นหาเฉพาะ id_card)
$query = "SELECT * FROM patients_login WHERE id_card = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_card]);
$user = $stmt->fetch();

if ($user) {
    // ถ้ามีผู้ใช้แล้วให้ส่งข้อมูลว่า "มีผู้ใช้แล้ว"
    echo json_encode(['user_exists' => true]);
} else {
    // กำหนดค่าให้กับตัวแปรเมื่อไม่พบผู้ใช้
    $new_username = $id_card;  // ใช้ id_card เป็น username

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $insertQuery = "INSERT INTO patients_login (id, username, password, id_card, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->execute([$account_id, $new_username, $new_password, $id_card]);

    // ส่งข้อมูลบัญชีใหม่กลับไป
    echo json_encode([
        'new_account' => [
            'username' => $new_username,
            'password' => $new_password
        ]
    ]);
}
?>
