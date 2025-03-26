<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงข้อมูลจากฟอร์ม
    $id_card = $_POST['id_card'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $appointment_type = $_POST['appointment_type'] ?? '';
    $weight = $_POST['weight'] ?? 0;
    $height = $_POST['height'] ?? 0;
    $bmi = $_POST['bmi'] ?? 0;
    $blood_type = $_POST['blood_type'] ?? '';
    $temperature = $_POST['temperature'] ?? 0;
    $heart_rate = $_POST['heart_rate'] ?? 0;
    $blood_pressure = $_POST['blood_pressure'] ?? '';
    $symptom_days = $_POST['symptom_days'] ?? 0;
    $smoking = $_POST['smoking'] ?? '';
    $drinking = $_POST['alcohol'] ?? '';
    $drug_allergy = $_POST['drug_allergy'] ?? '';
    $allergy_details = $_POST['allergy_details'] ?? '';
    $additional_details = $_POST['additional_details'] ?? '';
    $department = $_POST['department'] ?? '';
    $status = $_POST['status'] ?? '';
    $urgency = $_POST['urgency'] ?? '';

    // ข้อมูลบุคคลที่สามารถติดต่อได้
    $emer_contact_name = $_POST['emer_contact_name'] ?? '';
    $emer_contact_phone = $_POST['emer_contact_phone'] ?? '';

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($id_card) || empty($first_name) || empty($last_name) || empty($date_of_birth)) {
        $response['message'] = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
        echo json_encode($response);
        exit;
    }

    try {
        // ตั้งค่าโซนเวลา
        date_default_timezone_set('Asia/Bangkok');
        $conn->beginTransaction();

        // ตรวจสอบว่ามีข้อมูล id_card นี้แล้วหรือไม่
        $stmt = $conn->prepare("SELECT id, hn FROM patients_info WHERE id_card = :id_card");
        $stmt->execute([':id_card' => $id_card]);
        $existingPatient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingPatient) {
            // ถ้ามีข้อมูล → อัปเดต
            $stmt = $conn->prepare("UPDATE patients_info SET
                first_name = :first_name,
                last_name = :last_name,
                phone_number = :phone_number,
                date_of_birth = :date_of_birth,
                age = :age,
                gender = :gender,
                appointment_type = :appointment_type,
                weight = :weight,
                height = :height,
                bmi = :bmi,
                temperature = :temperature,
                heart_rate = :heart_rate,
                blood_pressure = :blood_pressure,
                symptom_days = :symptom_days,
                smoking = :smoking,
                alcohol = :alcohol,
                drug_allergy = :drug_allergy,
                allergy_details = :allergy_details,
                additional_details = :additional_details,
                department = :department,
                status = :status,
                urgency = :urgency,
                blood_type = :blood_type,
                emer_contact_name = :emer_contact_name,
                emer_contact_phone = :emer_contact_phone,
                updated_at = NOW()
                WHERE id_card = :id_card");

            $stmt->execute([
                ':id_card' => $id_card,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':phone_number' => $phone_number,
                ':date_of_birth' => $date_of_birth,
                ':age' => $age,
                ':gender' => $gender,
                ':appointment_type' => $appointment_type,
                ':weight' => $weight,
                ':height' => $height,
                ':bmi' => $bmi,
                ':blood_type' => $blood_type,
                ':temperature' => $temperature,
                ':heart_rate' => $heart_rate,
                ':blood_pressure' => $blood_pressure,
                ':symptom_days' => $symptom_days,
                ':smoking' => $smoking,
                ':alcohol' => $drinking,
                ':drug_allergy' => $drug_allergy,
                ':allergy_details' => $allergy_details,
                ':additional_details' => $additional_details,
                ':department' => $department,
                ':status' => $status,
                ':urgency' => $urgency,
                ':emer_contact_name' => $emer_contact_name,
                ':emer_contact_phone' => $emer_contact_phone
            ]);

            $response['success'] = true;
            $response['message'] = 'อัปเดตข้อมูลเรียบร้อยแล้ว';
            $response['action'] = 'update'; // บอกว่าเป็นการอัปเดต
        } else {
            // ถ้ายังไม่มีข้อมูล → เพิ่มใหม่
            $current_date = date('Y-m-d');
            $date_code = date('dmY');

            // คำนวณลำดับ HN
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM patients_info WHERE DATE(created_at) = :current_date");
            $stmt->execute([':current_date' => $current_date]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_hn_number = str_pad($row['count'] + 1, 4, '0', STR_PAD_LEFT);
            $hn = "HN-$date_code-$new_hn_number";

            // สร้าง ID ใหม่
            $stmt = $conn->query("SELECT MAX(id) AS last_id FROM patients_info");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_number = $row['last_id']
                ? str_pad((int)substr($row['last_id'], 2) + 1, 5, '0', STR_PAD_LEFT)
                : '00001';
            $id = 'PT' . $new_number;

            // เพิ่มข้อมูลใหม่
            $stmt = $conn->prepare("INSERT INTO patients_info (id_card, first_name, last_name, phone_number, date_of_birth, age, gender, appointment_type, weight, height, bmi, temperature, heart_rate, blood_pressure, symptom_days, smoking, alcohol, drug_allergy, allergy_details, additional_details, department, status, urgency, hn, id, blood_type, emer_contact_name, emer_contact_phone, created_at) 
            VALUES (:id_card, :first_name, :last_name, :phone_number, :date_of_birth, :age, :gender, :appointment_type, :weight, :height, :bmi, :temperature, :heart_rate, :blood_pressure, :symptom_days, :smoking, :alcohol, :drug_allergy, :allergy_details, :additional_details, :department, :status, :urgency, :hn, :id, :blood_type, :emer_contact_name, :emer_contact_phone, NOW())");

            $stmt->execute([
                ':id_card' => $id_card,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':phone_number' => $phone_number,
                ':date_of_birth' => $date_of_birth,
                ':age' => $age,
                ':gender' => $gender,
                ':appointment_type' => $appointment_type,
                ':weight' => $weight,
                ':height' => $height,
                ':bmi' => $bmi,
                ':blood_type' => $blood_type,
                ':temperature' => $temperature,
                ':heart_rate' => $heart_rate,
                ':blood_pressure' => $blood_pressure,
                ':symptom_days' => $symptom_days,
                ':smoking' => $smoking,
                ':alcohol' => $drinking,
                ':drug_allergy' => $drug_allergy,
                ':allergy_details' => $allergy_details,
                ':additional_details' => $additional_details,
                ':department' => $department,
                ':status' => $status,
                ':urgency' => $urgency,
                ':hn' => $hn,
                ':id' => $id,
                ':emer_contact_name' => $emer_contact_name,
                ':emer_contact_phone' => $emer_contact_phone
            ]);


            $response['success'] = true;
            $response['message'] = 'เพิ่มข้อมูลใหม่สำเร็จ';
            $response['hn'] = $hn;
            $response['patientID'] = $id;
            $response['action'] = 'insert';
        }

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit;
