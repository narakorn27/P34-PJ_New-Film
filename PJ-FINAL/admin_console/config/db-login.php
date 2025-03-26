<?php
session_start();
require_once 'db.php';

if (isset($_POST['login_go'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้';
        header("location: ../employee_login.php");
        exit();
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'ชื่อผู้ใช้ต้องเป็นภาษาอังกฤษและตัวเลขเท่านั้น';
        header("location: ../employee_login.php");
        exit();
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: ../employee_login.php");
        exit();
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM medical_staff WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // ตรวจสอบรหัสผ่าน
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_login'] = $row['id']; // เซ็ตค่า session
                    $_SESSION['role'] = $row['role']; // เซ็ตค่า role
                    header("location: ../index.php");
                } else {
                    $_SESSION['error'] = 'รหัสผ่านผิด';
                    header("location: ../employee_login.php");
                }
            } else {
                $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
                header("location: ../employee_login.php");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            header("location: ../employee_login.php");
        }
    }
}
?>
