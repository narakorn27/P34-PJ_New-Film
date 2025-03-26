<?php 
session_start();
require_once 'db.php'; // เชื่อมต่อฐานข้อมูล

// รับค่า 'id' จาก POST
$staff_id = $_POST['id'] ?? ''; 
if (empty($staff_id)) {
    die("Error: Staff ID is missing.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $district = $_POST['district'] ?? '';
    $sub_district = $_POST['sub_district'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $user_role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $avatar = null;

    // ตรวจสอบรหัสผ่าน
    if (!empty($password) && $password !== $confirm_password) {
        die("Error: Passwords do not match!");
    }
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_BCRYPT);
    }

    // ตรวจสอบรูปภาพ
    if (isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
    }

    // แปลงวันที่
    if (!empty($date_of_birth)) {
        $date = DateTime::createFromFormat('d/m/Y', $date_of_birth);
        if ($date) {
            $date_of_birth = $date->format('Y-m-d');
        }
    }

    try {
        // ตรวจสอบว่ามีการเปลี่ยน role หรือไม่
        $stmt = $conn->prepare("SELECT role FROM medical_staff WHERE id = :staff_id");
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->execute();
        $current_role = $stmt->fetchColumn();

        $new_staff_id = $staff_id; // กำหนดค่าเริ่มต้นเป็นค่าเดิม
        if ($current_role !== $user_role) {
            // ถ้ามีการเปลี่ยน role -> อัปเดต id ตาม prefix
            $prefix = '';
            if ($user_role === 'admin') $prefix = 'ADM';
            elseif ($user_role === 'doctor') $prefix = 'DT';
            elseif ($user_role === 'nurse') $prefix = 'NS';
            elseif ($user_role === 'receptionist') $prefix = 'RC';

            if (!empty($prefix)) {
                $new_staff_id = generateNewID($prefix, $conn);
            }
        }

        // คำสั่ง SQL สำหรับอัปเดตข้อมูล
        $sql = "UPDATE medical_staff SET 
                    id = :new_staff_id,
                    first_name = :first_name, 
                    last_name = :last_name, 
                    username = :username, 
                    email = :email, 
                    date_of_birth = :date_of_birth,
                    gender = :gender, 
                    address = :address, 
                    city = :city, 
                    district = :district, 
                    sub_district = :sub_district, 
                    postal_code = :postal_code, 
                    phone_number = :phone_number,
                    role = :user_role,
                    status = :status"; 

        if (!empty($password)) $sql .= ", password = :password";
        if ($avatar !== null) $sql .= ", avatar = :avatar";
        $sql .= " WHERE id = :staff_id";

        $stmt = $conn->prepare($sql);

        // ผูกค่าพารามิเตอร์
        $stmt->bindParam(':new_staff_id', $new_staff_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':sub_district', $sub_district);
        $stmt->bindParam(':postal_code', $postal_code);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':user_role', $user_role);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':staff_id', $staff_id);

        if (!empty($password)) $stmt->bindParam(':password', $password);
        if ($avatar !== null) $stmt->bindParam(':avatar', $avatar, PDO::PARAM_LOB);

        // ดำเนินการอัปเดต
        if ($stmt->execute()) {
            header("Location: ../edit-doctor.php?id=$new_staff_id&success=1");
            exit();
        } else {
            header("Location: ../edit-doctor.php?id=$staff_id&error=1");
            exit();
        }        
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


// ฟังก์ชันสำหรับการสร้าง ID ใหม่ที่ไม่ข้ามลำดับ
function generateNewID($prefix, $conn) {
    // ดึงรายการ id ที่มี prefix ตรงกัน และเรียงตามตัวเลข
    $stmt = $conn->prepare("SELECT id FROM medical_staff WHERE id LIKE :prefix ORDER BY id ASC");
    $stmt->bindValue(':prefix', $prefix . '%');
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // หาเลขลำดับที่ว่าง
    $next_number = 1;
    foreach ($ids as $id) {
        $number = intval(substr($id, 3)); // ตัด prefix ออกแล้วแปลงเป็นตัวเลข
        if ($number == $next_number) {
            $next_number++;
        } else {
            break; // ถ้าพบช่องว่างให้หยุดและใช้เลขนี้
        }
    }

    // สร้าง ID ใหม่ในรูปแบบ PREFIX + เลข 5 หลัก (เช่น DT00002)
    return sprintf("%s%05d", $prefix, $next_number);
}
?>
