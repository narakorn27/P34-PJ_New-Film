<?php
session_start();
include 'connect_database.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $sub_district = $_POST['sub_district'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];

    // ตรวจสอบการอัปโหลดไฟล์รูปภาพ
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        
        // อ่านไฟล์เป็น BLOB
        $imageData = file_get_contents($fileTmpPath);

        // อัปเดตลงฐานข้อมูล
        $sql = "UPDATE patients_login SET profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("bs", $imageData, $user_id);
        $stmt->send_long_data(0, $imageData); // ส่งข้อมูลไฟล์ขนาดใหญ่ (BLOB)

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูป!']);
            $stmt->close();
            exit(); // หยุดการทำงานหากเกิดข้อผิดพลาด
        }
        $stmt->close();
    }

    // อัปเดตข้อมูลส่วนตัว
    $sql = "UPDATE patients_info SET first_name = ?, last_name = ?, phone_number = ?, address = ?, district = ?, sub_district = ?, city = ?, postal_code = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $first_name, $last_name, $phone_number, $address, $district, $sub_district, $city, $postal_code, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'ข้อมูลถูกอัปเดตสำเร็จ!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล!']);
    }
    $stmt->close();
    $conn->close();
}
?>
