<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost"; 
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
$first_name = $_POST['firstname'];
$last_name = $_POST['lastname'];
$username = $_POST['username'];
$password = $_POST['password'];
$c_password = $_POST['c_password'];

// ตรวจสอบรหัสผ่านให้ตรงกัน
if ($password != $c_password) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match!']);
    exit();
}

// แฮชรหัสผ่าน
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// สร้างรหัสบุคลากร (ID) อัตโนมัติ
$sql = "SELECT id FROM medical_staff WHERE id LIKE 'ADM%' ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latest_id = $row['id'];
    $number = (int) substr($latest_id, 3);
    $new_number = $number + 1;
    $new_id = 'ADM' . str_pad($new_number, 5, '0', STR_PAD_LEFT);
} else {
    $new_id = 'ADM00001';
}

// กำหนดบทบาทเป็น admin
$role = 'admin';
$status = 'active';
$created_at = date("Y-m-d H:i:s");

// สร้างคำสั่ง SQL เพื่อบันทึกข้อมูล
$sql = "INSERT INTO medical_staff (id, first_name, last_name, username, password, role, status, created_at) 
        VALUES ('$new_id', '$first_name', '$last_name', '$username', '$hashed_password', '$role', '$status', '$created_at')";

// ตรวจสอบว่าเกิดข้อผิดพลาดจากการคิวรีหรือไม่
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Registration Successful. You have successfully registered as an Admin!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'There was an issue with the registration. Error: ' . $conn->error]);
}

$conn->close();

?>
