<?php

session_start();
require_once 'db.php';

header("Content-Type: application/json");


// ปิด Emulated Prepares ป้องกัน SQL Injection
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// ฟังก์ชันสำหรับสร้าง ID ตาม role (เวอร์ชันที่ปรับแล้ว)
function generateId($role, $pdo)
{
    // กำหนด Prefix ตาม role
    $prefix = $role === 'doctor' ? 'DT' : ($role === 'nurse' ? 'NR' : 'ADM');

    // ดึงข้อมูล id ทั้งหมดจากฐานข้อมูลที่ตรงกับ prefix
    $stmt = $pdo->prepare("SELECT id FROM medical_staff WHERE id LIKE CONCAT(:prefix, '%') ORDER BY id ASC");
    $stmt->execute([':prefix' => $prefix]);
    $existingIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // สร้าง array ของหมายเลข ID ที่มีอยู่
    $idNumbers = [];
    foreach ($existingIds as $id) {
        $idNumbers[] = (int)substr($id, strlen($prefix));
    }

    // หาช่องว่างของหมายเลข ID
    sort($idNumbers);
    $newNumber = 1;

    foreach ($idNumbers as $num) {
        if ($num != $newNumber) {
            // ถ้าหมายเลขขาดหายไป (เช่น DT00002)
            break;
        }
        $newNumber++;
    }

    // สร้างหมายเลข ID ใหม่และคืนค่า
    return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
}


// รับข้อมูลจาก POST
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$date_of_birth = $_POST['date_of_birth'] ?? '';
$gender = $_POST['gender'] ?? '';
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$district = trim($_POST['district'] ?? '');
$sub_district = trim($_POST['sub_district'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$role = $_POST['role'] ?? '';
$status = $_POST['status'] ?? 'active';
$nationality = trim($_POST['nationality'] ?? 'thai');

// ตรวจสอบค่าให้ไม่เป็นค่าว่าง
if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($date_of_birth) || empty($gender) || empty($role)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

// ตรวจสอบอีเมล
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// ตรวจสอบ username และ email ซ้ำ
$checkSql = "SELECT username, email FROM medical_staff WHERE username = :username OR email = :email";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->execute([':username' => $username, ':email' => $email]);
$existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    http_response_code(409);
    echo json_encode([
        'status' => $existing['username'] === $username ? 'duplicate_username' : 'duplicate_email',
        'message' => $existing['username'] === $username ? 'Username already exists.' : 'Email already exists.'
    ]);
    exit;
}

// ตรวจสอบรหัสผ่าน
if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long.']);
    exit;
}

$password_hashed = password_hash($password, PASSWORD_BCRYPT);
$id = generateId($role, $conn);

// ตรวจสอบการอัปโหลดรูปภาพ
$avatar_blob = null;




// เพิ่มข้อมูลลงฐานข้อมูล
try {
    $sql = "INSERT INTO medical_staff 
            (id, first_name, last_name, username, email, password, date_of_birth, gender, address, city, district, sub_district, postal_code, phone_number, avatar, role, status, nationality, created_at) 
            VALUES 
            (:id, :first_name, :last_name, :username, :email, :password, :date_of_birth, :gender, :address, :city, :district, :sub_district, :postal_code, :phone_number, :avatar, :role, :status, :nationality, NOW())";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password_hashed);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':sub_district', $sub_district);
    $stmt->bindParam(':postal_code', $postal_code);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':avatar', $avatar_blob, PDO::PARAM_LOB);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':nationality', $nationality);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Medical staff added successfully', 'id' => $id]);
    } else {
        throw new Exception('Failed to insert data.');
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;

exit;
