<?php
// เริ่มต้น session
session_start();

// เคลียร์ค่าทุกอย่างใน session
$_SESSION = array();

// ทำลาย session
session_destroy();

// Optional: ลบ cookie ถ้าใช้ cookie ในการเก็บ session id
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ผู้ใช้กลับไปยังหน้า login หรือหน้าแรก
header("Location: ../employee_login.php");
exit;
?>