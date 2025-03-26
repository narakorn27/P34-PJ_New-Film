<?php
include 'mid_string.php'
?>


<?php
// ดึงข้อมูลจาก session ที่เก็บ id
$id = $_SESSION['id']; // เช่น PT00001

// Query เชื่อมข้อมูลจาก 3 ตาราง
$sql = "
    SELECT 
        a.appointment_id, a.appointment_date, 
        pi.id, pi.first_name, pi.last_name, pi.age, pi.hn,
        pi.appointment_type, a.treatment_area, ms.first_name AS doctor_first_name,
        ms.last_name AS doctor_last_name
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


<head>
    <link rel="stylesheet" href="assets/css/styles_home_page.css" />
    <title>หน้าแรก</title>
</head>


<body>

    <main id="main-content">
        <!--=============== HOME ===============-->

        <div class="container-fluid p-0 position-relative">
            <!-- รูปภาพที่ขยายเต็มหน้าจอ -->
            <img src="assets/img/pic-ent.jpg" class="d-block img-fluid w-100" alt="Banner Image" />

            <!-- Section Content -->
            <div class="section-content">
                <!-- Appointment Card -->
                <div class="appointment-card">
                    <div class="header-que">
                        <h1>นัดหมาย</h1>
                        <?php if (!empty($appointments)): ?>
                            <a href="appointments_detail.php" class="view-all-btn">ดูทั้งหมด</a>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <!-- เนื้อหา -->
                        <?php if (!empty($appointments)): // ตรวจสอบว่ามีข้อมูลนัดหมาย 
                        ?>
                            <div class="circle mx-auto">
                                <?php
                                // แปลงวันที่เป็น Timestamp
                                $date = strtotime(end($appointments)['appointment_date'] ?? '');

                                // ดึงวันที่ เดือน และปีเป็นภาษาไทย
                                $day = date('j', $date); // วันที่
                                $month_names = [
                                    "มกราคม",
                                    "กุมภาพันธ์",
                                    "มีนาคม",
                                    "เมษายน",
                                    "พฤษภาคม",
                                    "มิถุนายน",
                                    "กรกฎาคม",
                                    "สิงหาคม",
                                    "กันยายน",
                                    "ตุลาคม",
                                    "พฤศจิกายน",
                                    "ธันวาคม"
                                ];
                                $month = $month_names[date('n', $date) - 1] ?? ''; // เดือน
                                $year = date('Y', $date) + 543; // แปลงเป็นปีพุทธศักราช (พ.ศ.)
                                ?>
                                <strong><?= htmlspecialchars($day) ?></strong>
                                <span style="font-size: 15px;"><?= htmlspecialchars($month) ?></span>
                                <span style="font-size: 15px;"><?= htmlspecialchars($year) ?></span>
                            </div>


                            <div class="details">
                                <h2 class="text-center">การนัดหมายของคุณ</h2>
                                <ul class="list-unstyled">
                                    <?php foreach ($appointments as $appointment): ?>
                                        <li>
                                            <strong>เวลาที่นัดหมาย:</strong>
                                            <?php
                                            $date = strtotime($appointment['appointment_date'] ?? '');
                                            $start_time = date('H:i', $date); // เวลาเริ่มต้น
                                            $end_time = date('H:i', strtotime("+20 minutes", $date)); // เวลาสิ้นสุด (เพิ่ม 20 นาที)
                                            echo htmlspecialchars("$start_time - $end_time น.");
                                            ?>
                                            <br>
                                            <!-- <strong>หมายเลขนัดหมาย:</strong> <?= htmlspecialchars($appointment['appointment_id'] ?? 'ไม่มีข้อมูล') ?> -->
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <p>แพทย์ผู้ดูแล: <?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></p>
                            </div>




                            <div class="queue">
                                <h2>หมายเลขประจำตัวผู้ป่วย</h2>
                                <?php
                                // ดึงค่า HN จาก appointments
                                $hn = end($appointments)['hn'] ?? 'ไม่มีข้อมูล HN';
                                ?>
                                <p class="queue-number"><?= htmlspecialchars($hn) ?></p>
                                <?php if ($hn !== 'ไม่มีข้อมูล HN'): ?>
                                    <p class="queue-text" style="color: #72757A;">ชื่อผู้ป่วย: <?= htmlspecialchars(end($appointments)['first_name'] . ' ' . end($appointments)['last_name']) ?></p>
                                <?php else: ?>
                                    <p>ไม่พบข้อมูล HN ของคุณ</p>
                                <?php endif; ?>
                            </div>

                        <?php else: // หากไม่มีข้อมูล 
                        ?>
                            <div class="no-data-container">
                                <p class="no-data">ยังไม่มีข้อมูลการนัดหมาย</p>
                            </div>
                        <?php endif; ?>

                        <!-- เพิ่มเนื้อหาตามต้องการ -->
                    </div>
                </div>
            </div>
        </div>






    </main>

    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>
</body>

</html>