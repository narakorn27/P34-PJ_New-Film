<?php
// เริ่มต้นการเชื่อมต่อฐานข้อมูล
include('connect_database.php');
session_start();

// ตรวจสอบว่าได้รับข้อมูลจากฟอร์มแล้ว
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $appointment_id = $_POST['appointment_id'];
    $day = $_POST['day'];
    $month_th = $_POST['month']; // ได้เป็น "กุมภาพันธ์"
    $year_th = $_POST['year'];
    $time = $_POST['mdtimepicker'];  // เวลา
    $rescheduling_reason = $_POST['rescheduling_reason'];

    // แปลงปี พ.ศ. เป็น ค.ศ.
    $year = $year_th - 543;

    // แปลงเดือนภาษาไทยเป็นตัวเลข
    $thai_months = [
        "มกราคม" => "01", "กุมภาพันธ์" => "02", "มีนาคม" => "03", "เมษายน" => "04",
        "พฤษภาคม" => "05", "มิถุนายน" => "06", "กรกฎาคม" => "07", "สิงหาคม" => "08",
        "กันยายน" => "09", "ตุลาคม" => "10", "พฤศจิกายน" => "11", "ธันวาคม" => "12"
    ];
    
    $month = isset($thai_months[$month_th]) ? $thai_months[$month_th] : "00"; // แปลงเดือนเป็นตัวเลข

    // แปลงวันและเวลาเป็นรูปแบบที่เหมาะสม
    $appointment_date = "$year-$month-$day $time:00";  // รวมวันและเวลาเข้าด้วยกัน

    // แก้ไขการตรวจสอบการนัดหมายซ้ำ
    $sql_check_existing_appointment = "SELECT * FROM appointments WHERE appointment_date = ?";
    $stmt_check_existing_appointment = $conn->prepare($sql_check_existing_appointment);
    $stmt_check_existing_appointment->bind_param("s", $appointment_date);
    $stmt_check_existing_appointment->execute();
    $result_check_existing_appointment = $stmt_check_existing_appointment->get_result();

    if ($result_check_existing_appointment->num_rows > 0) {
        die(json_encode(["error" => "มีวันและเวลานัดหมายซ้ำ กรุณาเลือกวันและเวลาอื่น"]));
    }

    // ตรวจสอบการนัดหมายของผู้ป่วย
    $patient_id = $_SESSION['id'];  // ค่าผู้ป่วยจาก session
    $sql_check_appointment = "SELECT * FROM appointments WHERE patient_id = ?";
    $stmt_check_appointment = $conn->prepare($sql_check_appointment);
    $stmt_check_appointment->bind_param("s", $patient_id);
    $stmt_check_appointment->execute();
    $result_check_appointment = $stmt_check_appointment->get_result();

    // ถ้าไม่พบการนัดหมายสำหรับผู้ป่วยนี้ในระบบ
    if ($result_check_appointment->num_rows == 0) {
        // สร้าง appointment_id ใหม่
        $appointment_date_object = new DateTime($appointment_date);
        $formatted_date = $appointment_date_object->format('Ymd');

        // ตรวจสอบค่าของ appointment_id สูงสุดที่มีอยู่ในระบบ
        $sql_max_appointment_id = "SELECT MAX(appointment_id) AS max_appointment_id FROM appointments WHERE appointment_id LIKE 'APT-$formatted_date-%'";
        $result_max_appointment_id = $conn->query($sql_max_appointment_id);
        $row_max = $result_max_appointment_id->fetch_assoc();
        $max_appointment_id = $row_max['max_appointment_id'];

        // กรณีที่ไม่มี appointment_id สูงสุด
        if ($max_appointment_id == null) {
            $new_number = '0001';  // กำหนดหมายเลขเริ่มต้นใหม่
        } else {
            // แยกหมายเลขลำดับ (เลขท้าย) ออกจาก appointment_id
            $max_number = substr($max_appointment_id, -4);
            $new_number = str_pad((int)$max_number + 1, 4, '0', STR_PAD_LEFT); // แปลงให้เป็นเลขที่เพิ่มขึ้น
        }

        // สร้าง appointment_id ใหม่
        $appointment_id_new = "APT-" . $formatted_date . "-" . $new_number;

        // เตรียมคำสั่ง INSERT สำหรับการเพิ่มการนัดหมายใหม่
        $sql_insert_appointment = "INSERT INTO appointments (appointment_id, patient_id, appointment_date, treatment_area, doctor_diagnosis, doctor_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_appointment = $conn->prepare($sql_insert_appointment);
        $stmt_insert_appointment->bind_param("ssssss", $appointment_id_new, $patient_id, $appointment_date, $treatmentArea, $doctorDiagnosis, $doctor_id);

        // เพิ่มการนัดหมายใหม่
        if ($stmt_insert_appointment->execute()) {
            // ส่งข้อมูลสำเร็จในรูปแบบ JSON
            echo json_encode([
                "success" => true,
                "message" => "การนัดหมายใหม่ถูกเพิ่มสำเร็จ",
                "appointment_id" => $appointment_id_new
            ]);
            exit;
        } else {
            // ส่งข้อความผิดพลาดในรูปแบบ JSON
            die(json_encode(["error" => "ไม่สามารถเพิ่มข้อมูลการนัดหมายใหม่ได้"]));
        }
    } else {
        // ตรวจสอบสถานะของการนัดหมาย
        $sql_check_status = "SELECT status FROM appointments WHERE appointment_id = ? AND patient_id = ?";
        $stmt_check_status = $conn->prepare($sql_check_status);
        $stmt_check_status->bind_param("ss", $appointment_id, $patient_id);
        $stmt_check_status->execute();
        $result_check_status = $stmt_check_status->get_result();
        $row_status = $result_check_status->fetch_assoc();

        // ถ้าสถานะเป็น 'cancelled' จะไม่สามารถเลื่อนนัดได้
        if ($row_status['status'] == 'cancelled') {
            die(json_encode(["error" => "ไม่สามารถเลื่อนนัดที่ถูกยกเลิกได้"]));
        }

        // หากมีการนัดหมายอยู่แล้ว ให้สร้าง appointment_id ใหม่
        $appointment_date_object = new DateTime($appointment_date);
        $formatted_date = $appointment_date_object->format('Ymd');

        // ตรวจสอบค่าของ appointment_id สูงสุดที่มีอยู่ในระบบ
        $sql_max_appointment_id = "SELECT MAX(appointment_id) AS max_appointment_id FROM appointments WHERE appointment_id LIKE 'APT-$formatted_date-%'";
        $result_max_appointment_id = $conn->query($sql_max_appointment_id);
        $row_max = $result_max_appointment_id->fetch_assoc();
        $max_appointment_id = $row_max['max_appointment_id'];

        // กรณีที่ไม่มี appointment_id สูงสุด
        if ($max_appointment_id == null) {
            $new_number = '0001';  // กำหนดหมายเลขเริ่มต้นใหม่
        } else {
            // แยกหมายเลขลำดับ (เลขท้าย) ออกจาก appointment_id
            $max_number = substr($max_appointment_id, -4);
            $new_number = str_pad((int)$max_number + 1, 4, '0', STR_PAD_LEFT); // แปลงให้เป็นเลขที่เพิ่มขึ้น
        }

        // สร้าง appointment_id ใหม่
        $appointment_id_new = "APT-" . $formatted_date . "-" . $new_number;

        // อัปเดตการนัดหมาย
        $sql_update = "UPDATE appointments SET appointment_id = ?, appointment_date = ?, rescheduling_reason = ?, is_read = 0 WHERE appointment_id = ? AND patient_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssss", $appointment_id_new, $appointment_date, $rescheduling_reason, $appointment_id, $patient_id);

        if ($stmt_update->execute()) {
            // อัปเดตสถานะเป็น 'rescheduled'
            $sql_update_status = "UPDATE appointments SET status = 'rescheduled', is_read = 0 WHERE appointment_id = ? AND patient_id = ?";
            $stmt_update_status = $conn->prepare($sql_update_status);
            $stmt_update_status->bind_param("ss", $appointment_id_new, $patient_id);
            $stmt_update_status->execute();

            // ส่งข้อมูลสำเร็จในรูปแบบ JSON
            echo json_encode([
                "success" => true,
                "message" => "การนัดหมายถูกอัปเดตสำเร็จ",
                "appointment_id" => $appointment_id_new
            ]);
            exit;
        } else {
            // ส่งข้อความผิดพลาดในรูปแบบ JSON
            die(json_encode(["error" => "ไม่สามารถอัปเดตข้อมูลการนัดหมายได้"]));
        }
    }
} else {
    echo json_encode(["error" => "ข้อมูลไม่ครบถ้วน"]);
}
?>