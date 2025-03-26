<?php
include('connect_database.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // 🔹 ดึงข้อมูลจากฐานข้อมูล (เพิ่ม pin_code)
        $sql = "SELECT id, password, accepted_terms, pin_code FROM patients_login WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode([
                'status' => 'error',
                'message' => 'คำสั่ง SQL ผิดพลาด'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $stored_password, $accepted_terms, $pin_code);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();

            if ($password === $stored_password || password_verify($password, $stored_password)) {
                $_SESSION['id'] = $id;

                // ✅ ถ้ายังไม่ได้กดยอมรับ Terms → ไปหน้า Terms
                if ($accepted_terms == 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'โปรดยอมรับข้อตกลงก่อน!',
                        'redirect' => 'terms.php'
                    ], JSON_UNESCAPED_UNICODE);
                }
                // ✅ ถ้ายังไม่ได้ตั้งค่า PIN → ไปหน้า PIN
                elseif (empty($pin_code)) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'โปรดตั้งค่า PIN ก่อนเข้าใช้งาน!',
                        'redirect' => 'set_pin.php'
                    ], JSON_UNESCAPED_UNICODE);
                }
                // ✅ ถ้าตั้ง PIN แล้ว → ไปหน้า home_page.php
                else {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'เข้าสู่ระบบสำเร็จ!',
                        'redirect' => 'home_page.php'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'รหัสผ่านไม่ถูกต้อง!'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่พบชื่อผู้ใช้ในระบบ!'
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'กรุณากรอกข้อมูลให้ครบ!'
        ], JSON_UNESCAPED_UNICODE);
    }

    $conn->close();
    exit;
}
?>
