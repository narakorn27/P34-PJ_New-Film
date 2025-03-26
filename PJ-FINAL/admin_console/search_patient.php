<?php 
session_start();
require_once './config/db.php';

if (!isset($_POST['searchBy']) || !isset($_POST['searchPatientId'])) {
    die(json_encode(["error" => "Missing search parameters"]));
}

$searchBy = $_POST['searchBy'];
$searchPatientId = $_POST['searchPatientId'];

$allowedSearchBy = ['hn', 'first_name', 'last_name', 'id_card', 'full_name', 'date_of_birth'];
if (!in_array($searchBy, $allowedSearchBy)) {
    die(json_encode(["error" => "Invalid search criteria"]));
}

try {
    // Prepare query for searching patient
    if ($searchBy == 'full_name') {
        // แยก first_name และ last_name จาก full_name
        $nameParts = explode(" ", $searchPatientId);
        $first_name = $nameParts[0];
        $last_name = isset($nameParts[1]) ? $nameParts[1] : "";

        if (!empty($last_name)) {
            $sql = "SELECT 
                    p.id, p.hn, p.first_name, p.last_name, p.status, p.urgency, p.additional_details, 
                    COALESCE(p.appointment_type, '') AS appointment_type, 
                    COALESCE(a.doctor_diagnosis, 'ไม่ระบุ') AS doctor_diagnosis, 
                    COALESCE(a.treatment_area, '') AS treatment_area,
                    COALESCE(a.appointment_date, '') AS appointment_date,
                    p.id_card, p.date_of_birth,  -- เพิ่ม id_card และ date_of_birth
                    a.doctor_id
                FROM patients_info p 
                LEFT JOIN appointments a ON p.id = a.patient_id 
                WHERE p.first_name LIKE :first_name AND p.last_name LIKE :last_name";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        } else {
            $sql = "SELECT 
                    p.id, p.hn, p.first_name, p.last_name, p.status, p.urgency, p.additional_details, 
                    COALESCE(p.appointment_type, '') AS appointment_type, 
                    COALESCE(a.doctor_diagnosis, 'ไม่ระบุ') AS doctor_diagnosis, 
                    COALESCE(a.treatment_area, '') AS treatment_area,
                    COALESCE(a.appointment_date, '') AS appointment_date,
                    p.id_card, p.date_of_birth,  -- เพิ่ม id_card และ date_of_birth
                    a.doctor_id
                FROM patients_info p 
                LEFT JOIN appointments a ON p.id = a.patient_id 
                WHERE p.$searchBy = :searchPatientId OR p.hn LIKE :searchPatientId OR p.id_card LIKE :searchPatientId OR p.date_of_birth LIKE :searchPatientId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':searchPatientId', $searchPatientId, PDO::PARAM_STR);
        }
    } else {
        if ($searchBy == 'date_of_birth') {
            // เปลี่ยนรูปแบบ date ให้เหมาะสม เช่น yyyy-mm-dd
            $date = DateTime::createFromFormat('d/m/Y', $searchPatientId);
            if ($date) {
                $searchPatientId = $date->format('Y-m-d');
            }
        }

        // คำสั่ง SQL สำหรับค้นหาตาม searchBy ที่เลือก
        $sql = "SELECT 
                    p.id, p.hn, p.first_name, p.last_name, p.status, p.urgency, p.additional_details, 
                    COALESCE(p.appointment_type, '') AS appointment_type, 
                    COALESCE(a.doctor_diagnosis, 'ไม่ระบุ') AS doctor_diagnosis, 
                    COALESCE(a.treatment_area, '') AS treatment_area,
                    COALESCE(a.appointment_date, '') AS appointment_date,
                    p.id_card, p.date_of_birth,  -- เพิ่ม id_card และ date_of_birth
                    a.doctor_id
                FROM patients_info p 
                LEFT JOIN appointments a ON p.id = a.patient_id 
                WHERE p.$searchBy = :searchPatientId OR p.hn LIKE :searchPatientId OR p.id_card LIKE :searchPatientId OR p.date_of_birth LIKE :searchPatientId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':searchPatientId', $searchPatientId, PDO::PARAM_STR);
    }

    // Execute the query
    if (!$stmt->execute()) {
        die(json_encode(["error" => "Execute failed: " . $stmt->errorInfo()]));
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // ตรวจสอบว่า doctor_id มีค่าหรือไม่
        if ($result['doctor_id']) {
            // ดึงข้อมูลชื่อหมอจาก medical_staff ถ้ามี doctor_id
            $doctor_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM medical_staff WHERE id = :doctor_id";
            $doctor_stmt = $conn->prepare($doctor_sql);
            $doctor_stmt->bindParam(':doctor_id', $result['doctor_id'], PDO::PARAM_INT);
            $doctor_stmt->execute();
            $doctor_result = $doctor_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doctor_result) {
                $result['doctor_name'] = $doctor_result['full_name']; // แสดงชื่อหมอ
            } else {
                $result['doctor_name'] = 'ไม่พบข้อมูลหมอ';
            }
            
            $doctor_stmt->closeCursor();
        } else {
            $result['doctor_name'] = 'ยังไม่เลือกหมอ';
        }

        // ดึงรายชื่อหมอทั้งหมดจาก medical_staff ที่มี role = 'doctor'
        $doctor_list_sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM medical_staff WHERE role = 'doctor'";
        $doctor_list_stmt = $conn->query($doctor_list_sql);
        $doctors = $doctor_list_stmt->fetchAll(PDO::FETCH_ASSOC);

        // ส่งข้อมูล JSON
        echo json_encode([
            'patient' => $result,
            'doctors' => $doctors
        ]);
    } else {
        echo json_encode(["message" => "ไม่พบข้อมูลการนัดหมาย"]);
    }
} catch (PDOException $e) {
    die(json_encode(["error" => "Database error: " . $e->getMessage()]));
}

$conn = null;
?>
