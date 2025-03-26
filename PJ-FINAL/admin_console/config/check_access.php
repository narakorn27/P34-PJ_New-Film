<?php
session_start();
require_once 'db.php';

function checkAccess($requiredRole) {
    // ตรวจสอบการเข้าสู่ระบบ
    if (!isset($_SESSION['admin_login']) && !isset($_SESSION['doctor_login']) && !isset($_SESSION['nurse_login'])) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: ../employee_login.php');
        exit();
    }

    // ตรวจสอบบทบาทของผู้ใช้
    $user_id = $_SESSION['admin_login'] ?? $_SESSION['doctor_login'] ?? $_SESSION['nurse_login'];
    
    $$conn = $conn->prepare('SELECT urole FROM hospital_staff WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !in_array($user['urole'], $requiredRole)) {
        $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้!';
        header('location: access_denied.php');
        exit();
    }
}
?>