<?php
session_start();
require_once 'db.php'; // ใช้ $conn จาก db.php

if (!isset($_POST['searchBy']) || !isset($_POST['searchPatientId'])) {
    die(json_encode(["error" => "Missing search parameters"]));
}

$searchBy = $_POST['searchBy'];
$searchPatientId = $_POST['searchPatientId'];

$allowedSearchBy = ['hn', 'first_name', 'last_name', 'id_card', 'full_name'];
if (!in_array($searchBy, $allowedSearchBy)) {
    die(json_encode(["error" => "Invalid search criteria"]));
}

try {
    if ($searchBy == 'full_name') {
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
                a.doctor_id
            FROM patients_info p 
            LEFT JOIN appointments a ON p.id = a.patient_id 
            WHERE p.first_name LIKE ? AND p.last_name LIKE ?";
            $stmt = $conn->prepare($sql); // ใช้ $conn แทน $pdo
            $stmt->execute(["%$first_name%", "%$last_name%"]);
        } else {
            $sql = "SELECT 
                p.id, p.hn, p.first_name, p.last_name, p.status, p.urgency, p.additional_details, 
                COALESCE(p.appointment_type, '') AS appointment_type, 
                COALESCE(a.doctor_diagnosis, 'ไม่ระบุ') AS doctor_diagnosis, 
                COALESCE(a.treatment_area, '') AS treatment_area,
                COALESCE(a.appointment_date, '') AS appointment_date,
                a.doctor_id
            FROM patients_info p 
            LEFT JOIN appointments a ON p.id = a.patient_id 
            WHERE p.first_name LIKE ? OR p.hn LIKE ?";
            $stmt = $conn->prepare($sql); // ใช้ $conn แทน $pdo
            $stmt->execute(["%$searchPatientId%", "%$searchPatientId%"]);
        }
    } else {
        $sql = "SELECT 
                p.id, p.hn, p.first_name, p.last_name, p.status, p.urgency, p.additional_details, 
                COALESCE(p.appointment_type, '') AS appointment_type, 
                COALESCE(a.doctor_diagnosis, 'ไม่ระบุ') AS doctor_diagnosis, 
                COALESCE(a.treatment_area, '') AS treatment_area,
                COALESCE(a.appointment_date, '') AS appointment_date,
                a.doctor_id
            FROM patients_info p 
            LEFT JOIN appointments a ON p.id = a.patient_id 
            WHERE p.$searchBy = ? OR p.hn LIKE ?";
        $stmt = $conn->prepare($sql); // ใช้ $conn แทน $pdo
        $stmt->execute([$searchPatientId, "%$searchPatientId%"]);
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($row['doctor_id']) {
            $doctor_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM medical_staff WHERE id = ?";
            $doctor_stmt = $conn->prepare($doctor_sql); // ใช้ $conn แทน $pdo
            $doctor_stmt->execute([$row['doctor_id']]);
            $doctor_row = $doctor_stmt->fetch(PDO::FETCH_ASSOC);
            $row['doctor_name'] = $doctor_row ? $doctor_row['full_name'] : 'ไม่พบข้อมูลหมอ';
        } else {
            $row['doctor_name'] = 'ยังไม่เลือกหมอ';
        }

        $doctor_list_sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM medical_staff WHERE role = 'doctor'";
        $doctor_list_stmt = $conn->query($doctor_list_sql); // ใช้ $conn แทน $pdo
        $doctors = $doctor_list_stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'patient' => $row,
            'doctors' => $doctors
        ]);
    } else {
        echo json_encode(["message" => "ไม่พบข้อมูลการนัดหมาย"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
