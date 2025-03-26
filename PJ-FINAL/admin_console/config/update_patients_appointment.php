<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Connection failed: " . $e->getMessage()])); 
}

// รับค่าจากฟอร์ม
$urgency = $_POST['urgency'] ?? '';
$medicalHistory = $_POST['additional_details'] ?? '';
$hn = $_POST['hn'] ?? '';
$appointmentDate = $_POST['appointment_date'] ?? '';
$doctorDiagnosis = $_POST['doctorDiagnosis'] ?? '';
$treatmentArea = $_POST['treatment_area'] ?? '';
$doctor_id = $_POST['doctor_id'] ?? '';
$appointment_type_edit = $_POST['appointment_type_edit'] ?? '';

// รับค่าจากฟอร์ม
$status = $_POST['status'] ?? 'Pending'; // กำหนดค่าเริ่มต้นเป็น Pending

// ตรวจสอบว่าเป็นการตรวจเสร็จหรือไม่ (จากสถานะที่ส่งมาจากฟอร์ม)
if ($status === 'Completed') {
    $status = 'Checked'; // ถ้าสถานะเป็น 'Completed' เปลี่ยนเป็น 'Checked'
}



// ตรวจสอบว่า hn มีอยู่ใน patients_info หรือไม่
$sql_check_patient = "SELECT id FROM patients_info WHERE hn = ?";
$stmt_check_patient = $conn->prepare($sql_check_patient);
$stmt_check_patient->execute([$hn]);
$patient = $stmt_check_patient->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die(json_encode(["error" => "ไม่พบผู้ป่วยที่มี HN นี้ในระบบ"]));
}
$patient_id = $patient['id'];

// หากมีค่า appointment_type_edit ให้ทำการอัปเดตข้อมูลนี้
if ($appointment_type_edit) {
    $sql_update_patient_info = "UPDATE patients_info SET appointment_type = ? WHERE hn = ?";
    $stmt_update_patient_info = $conn->prepare($sql_update_patient_info);
    $stmt_update_patient_info->execute([$appointment_type_edit, $hn]);
}

// อัปเดตข้อมูลผู้ป่วยอื่น ๆ
$sql_update_patient = "UPDATE patients_info SET status = ?, urgency = ?, additional_details = ? WHERE hn = ?";
$stmt_update_patient = $conn->prepare($sql_update_patient);
$stmt_update_patient->execute([$status, $urgency, $medicalHistory, $hn]);

// ตรวจสอบการนัดหมายในวันและเวลาที่เลือก
$sql_check_existing_appointment = "SELECT COUNT(*) FROM appointments WHERE appointment_date = ?";
$stmt_check_existing_appointment = $conn->prepare($sql_check_existing_appointment);
$stmt_check_existing_appointment->execute([$appointmentDate]);

if ($stmt_check_existing_appointment->fetchColumn() > 0) {
    die(json_encode(["error" => "มีวันและเวลานัดหมายซ้ำ กรุณาเลือกวันและเวลาอื่น"]));
}

// ตรวจสอบการนัดหมายของผู้ป่วย
$sql_check_appointment = "SELECT * FROM appointments WHERE patient_id = ?";
$stmt_check_appointment = $conn->prepare($sql_check_appointment);
$stmt_check_appointment->execute([$patient_id]);
$existing_appointment = $stmt_check_appointment->fetch(PDO::FETCH_ASSOC);

// สร้าง appointment_id ใหม่
$formatted_date = (new DateTime($appointmentDate))->format('Ymd');
$sql_count_appointments = "SELECT COUNT(*) FROM appointments WHERE appointment_date LIKE ?";
$stmt_count_appointments = $conn->prepare($sql_count_appointments);
$stmt_count_appointments->execute(["$formatted_date%"]);
$count = $stmt_count_appointments->fetchColumn() + 1;
$appointment_id_new = "APT-" . $formatted_date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);


// ตรวจสอบว่ามี appointment_id นี้อยู่แล้วหรือไม่
$sql_check_existing_appointment = "SELECT COUNT(*) FROM appointments WHERE appointment_id = ?";
$stmt_check_existing_appointment = $conn->prepare($sql_check_existing_appointment);
$stmt_check_existing_appointment->execute([$appointment_id_new]);

// ถ้ามีการใช้แล้ว เพิ่มจำนวน appointment_id ใหม่
while ($stmt_check_existing_appointment->fetchColumn() > 0) {
    $count++;  // เพิ่มค่าตัวเลข
    $appointment_id_new = "APT-" . $formatted_date . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    $stmt_check_existing_appointment->execute([$appointment_id_new]);  // ตรวจสอบใหม่
}

if (!$existing_appointment) {
    // เพิ่มการนัดหมายใหม่
    $sql_insert_appointment = "INSERT INTO appointments (appointment_id, patient_id, appointment_date, treatment_area, doctor_diagnosis, doctor_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt_insert_appointment = $conn->prepare($sql_insert_appointment);
    $stmt_insert_appointment->execute([$appointment_id_new, $patient_id, $appointmentDate, $treatmentArea, $doctorDiagnosis, $doctor_id, $status]);
} else {
    // อัปเดตการนัดหมาย
    $sql_update_appointment = "UPDATE appointments SET appointment_date = ?, appointment_id = ?, doctor_diagnosis = ?, treatment_area = ?, doctor_id = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE patient_id = ?";
    $stmt_update_appointment = $conn->prepare($sql_update_appointment);
    $stmt_update_appointment->execute([$appointmentDate, $appointment_id_new, $doctorDiagnosis, $treatmentArea, $doctor_id, $status, $patient_id]);
}

echo json_encode(["message" => "ข้อมูลได้รับการอัปเดตแล้ว"]);
?>