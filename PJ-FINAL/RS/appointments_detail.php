<?php
// เริ่มต้นการเชื่อมต่อฐานข้อมูล
include('./config/connect_database.php');
session_start();

// ตรวจสอบ session
if (!isset($_SESSION['id'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้กลับไปที่หน้า login
    header('Location: splash.php');
    exit;
}

// ดึงข้อมูลจาก session ที่เก็บ id
$id = $_SESSION['id']; // เช่น PT00001

// Query เชื่อมข้อมูลจาก 3 ตาราง
$sql = "
    SELECT 
        a.appointment_id, a.appointment_date, a.doctor_diagnosis,
        pi.id, pi.first_name, pi.last_name, pi.age, pi.hn,
        pi.appointment_type, pi.department, pi.status,  -- เพิ่ม status จาก table patients_info
        a.treatment_area, 
        ms.first_name AS doctor_first_name, ms.last_name AS doctor_last_name
    FROM appointments a
    JOIN patients_info pi ON a.patient_id = pi.id
    JOIN patients_login pl ON pl.id = a.patient_id
    JOIN medical_staff ms ON a.doctor_id = ms.id
    WHERE pl.id = '$id'  -- ใช้ id จาก session
    ORDER BY a.appointment_date ASC
";


// ดำเนินการ Query
$result = mysqli_query($conn, $sql);

// ตรวจสอบผลลัพธ์
if ($result) {
    $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "Error: " . mysqli_error($conn); // ดูรายละเอียดข้อผิดพลาด
}

?>

<?php
include 'mid_string.php'
?>

<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- <link rel="stylesheet" href="assets/css/style_login.css" /> -->
    <link rel="stylesheet" href="assets/css/style_appointment.css" />
    <title>ข้อมูลการนัดหมายทั้งหมด</title>


</head>

<body>
    <div class="head-first">
        <h3>ข้อมูลการนัดหมาย</h3>
    </div>

    <div class="section-content">
        <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="card-header-detail">
                        <div class="circle mx-auto">
                            <?php
                            $date = strtotime(end($appointments)['appointment_date'] ?? '');
                            $day = date('j', $date);
                            $month_names = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
                            $month = $month_names[date('n', $date) - 1] ?? '';
                            ?>
                            <strong><?= htmlspecialchars($day) ?></strong>
                            <span><?= htmlspecialchars($month) ?></span>
                        </div>

                        <div class="text-title">
                            <p>
                                สถานะการตรวจ:
                                <?php
                                $status = $appointment['status'];
                                $status_text = '';
                                $status_class = '';

                                if ($status === 'Pending') {
                                    $status_text = 'รอพบแพทย์';
                                    $status_class = 'text-warning';
                                } elseif ($status === 'Checked') {
                                    $status_text = 'กำลังเข้าตรวจ';
                                    $status_class = 'text-info';
                                } elseif ($status === 'Completed') {
                                    $status_text = 'ตรวจเสร็จเรียบร้อย';
                                    $status_class = 'text-success';
                                } else {
                                    $status_text = 'สถานะไม่ระบุ';
                                    $status_class = 'text-secondary';
                                }
                                ?>
                                <span class="<?= htmlspecialchars($status_class) ?>"><?= htmlspecialchars($status_text) ?></span>
                            </p>
                            <br>

                            <small>
                                <?= htmlspecialchars(date('d/m/', $date) . (date('Y', $date) + 543)) ?> |
                                <?= htmlspecialchars(date('H:i', $date)) ?>-<?= htmlspecialchars(date('H:i', strtotime('+20 minutes', $date))) ?> น.
                            </small>
                            <br>
                            <small>การรักษา : <?= htmlspecialchars($appointment['appointment_type'] === 'Walk-in' ? 'ทั่วไป' : 'ติดตามอาการ') ?> (<?= htmlspecialchars($appointment['treatment_area']) ?>)</small>
                        </div>
                    </div>

                    <div class="status">
                        <a href="edit-appointment.php?appointment_id=<?= urlencode($appointment['appointment_id']) ?>" class="btn btn-link">แจ้งเลื่อนนัดหมาย</a>
                    </div>

                    <div class="divider"></div>
                    <div class="section-con">
                        <p class="section-title">รายละเอียดของอาการ</p>
                        <p><?= htmlspecialchars($appointment['doctor_diagnosis'] ?? 'ไม่มีข้อมูล') ?></p>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <p class="section-title">แผนก</p>
                        <p>ห้องตรวจ<?= htmlspecialchars($appointment['department'] ?? 'ไม่ระบุ') ?></p>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <p class="section-title">ผู้รับบริการ</p>
                        <p><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></p>
                        <p>หมายเลขประจำตัวผู้ป่วย: <?= $appointment['hn'] ?> (<?= $appointment['id'] ?>)</p>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <p class="section-title">แพทย์ผู้นัดหมาย</p>
                        <p>แพทย์: <?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data-container">
                <p class="no-data">ไม่มีข้อมูลการนัดหมาย</p>
            </div>
        <?php endif; ?>
    </div>








    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>


</body>

</html>