<?php
session_start();  // เริ่มต้น session
session_unset();  // ลบข้อมูลใน session
session_destroy(); // ทำลาย session
echo json_encode(['success' => true]);  // ส่งข้อมูลกลับไปที่ JavaScript
?>
