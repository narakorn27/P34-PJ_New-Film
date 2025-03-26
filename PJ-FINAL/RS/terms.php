<?php
session_start();
include './config/connect_database.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // ถ้ายังไม่ได้ล็อกอิน ให้กลับไปหน้า Login
    exit();
}

$user_id = $_SESSION['id']; // ใช้ค่า ID จาก Session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // อัปเดตเฉพาะผู้ใช้ที่ล็อกอินอยู่
    $sql = "UPDATE patients_login SET accepted_terms = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id); // `id` เป็น string (ใช้ "s")

    if ($stmt->execute()) {
        header("Location: set_pin.php"); // ไปตั้งค่า PIN ต่อ
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อตกลงและเงื่อนไข</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            max-width: 600px;
            margin: auto;
        }

        /* Navbar สีฟ้า */
        .navbar {
            background-color: #1655b9;
            /* สีฟ้า */
            height: 30px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1000;
        }

        /* Footer สีฟ้า */
        .footer {
            background-color: #1655b9;
            /* สีฟ้า */
            color: white;
            text-align: center;
            padding: 15px 20px;
            width: 100%;
            position: fixed;
            bottom: -1px;
            left: 0;
            z-index: 1000;
            font-size: 0.9rem;
        }

    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="navbar"></div>
            <div class="container">
                <div class="card shadow-lg p-4">
                    <h2 class="text-center text-primary">ข้อตกลงและเงื่อนไขการใช้บริการ</h2>
                    <p class="mt-3">เราให้ความสำคัญกับความเป็นส่วนตัวและความปลอดภัยของข้อมูลของคุณ ก่อนดำเนินการต่อ โปรดอ่านและยอมรับข้อตกลงต่อไปนี้:</p>
                    <ul>
                        <li><strong>ข้อมูลที่เราขอเข้าถึง:</strong> ชื่อ, อีเมล, ตำแหน่งที่ตั้ง และข้อมูลที่เกี่ยวข้องกับการใช้งานระบบ</li>
                        <li><strong>วัตถุประสงค์ในการใช้งาน:</strong> เพื่อปรับปรุงประสบการณ์การใช้งาน, ตรวจสอบสิทธิ์การเข้าถึง และให้บริการที่เหมาะสมกับคุณ</li>
                        <li><strong>การรักษาความปลอดภัย:</strong> ข้อมูลของคุณจะถูกเก็บรักษาอย่างปลอดภัยตามมาตรฐานความปลอดภัย และจะไม่ถูกเปิดเผยโดยไม่ได้รับอนุญาต</li>
                        <li><strong>สิทธิของผู้ใช้:</strong> คุณสามารถปฏิเสธการให้ความยินยอมได้ และสามารถเปลี่ยนแปลงหรือลบข้อมูลของคุณได้ตลอดเวลา</li>
                        <li><strong>นโยบายความเป็นส่วนตัว:</strong> อ่านรายละเอียดเพิ่มเติมเกี่ยวกับการจัดเก็บและใช้งานข้อมูลของคุณได้ที่ <a href="#">นโยบายความเป็นส่วนตัวของเรา</a></li>
                    </ul>
                    <form method="post" class="text-center mt-3">
                        <button type="submit" class="btn btn-success w-100">ยอมรับเงื่อนไข</button>
                    </form>
                </div>
            </div>
    <div class="footer"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>