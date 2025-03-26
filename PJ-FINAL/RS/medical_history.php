<?php
include 'mid_string.php';

// ดึง id จาก session
$id = $_SESSION['id'];

// Query ข้อมูลจากฐานข้อมูล
$sql = "
    SELECT 
        pi.first_name, pi.last_name, pi.hn, pi.weight, pi.height,
        pi.blood_type, pi.heart_rate, pi.blood_pressure, 
        pi.additional_details, a.doctor_diagnosis, pl.profile_picture
    FROM patients_info pi
    LEFT JOIN appointments a ON a.patient_id = pi.id 
    LEFT JOIN patients_login pl ON pl.id = pi.id
    WHERE pi.id = ?
";



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient_info = $result->fetch_assoc();

// ตรวจสอบว่าผู้ใช้มีรูปโปรไฟล์หรือไม่
if ($patient_info['profile_picture']) {
    // ถ้ามีรูปโปรไฟล์ แปลงข้อมูล BLOB เป็น base64
    $profile_picture = $patient_info['profile_picture'];
    $profile_image = 'data:image/jpeg;base64,' . base64_encode($profile_picture);  // แปลง BLOB เป็น base64
} else {
    // ถ้าไม่มีรูปโปรไฟล์ ใช้ภาพเริ่มต้น
    $profile_image = 'assets/img/user.jpg';
}

?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style_medical_history.css">
    <title>ประวัติการรักษา</title>




</head>

<body>
    <main>
        <div class="container-app">
            <!-- ส่วนหัว -->
            <div class="head-first">
                <h3>ประวัติสุขภาพ</h3>
            </div>



            <div class="section-content">
                <div class="appointment-card">

                    <!-- ข้อมูลผู้ป่วย -->


                    <div class="patient-details">
                        <div>
                            <img class="img-pro" src="<?= $profile_image ?>" alt="Profile Picture" onclick="previewImageClick()">
                        </div>
                        <!-- พรีวิวรูปภาพ -->
                        <!-- <div id="imagePreviewModal" class="image-preview-modal" style="display: none;">
                            <img id="modalImage" src="" alt="Image Preview">
                            <button class="close-btn" onclick="closeModal()">Close</button>
                        </div> -->




                        <div class="text-detail">
                            <p><strong>ชื่อ - นามสกุล :</strong><br> <?= htmlspecialchars($patient_info['first_name'] . ' ' . $patient_info['last_name']); ?></p>
                            <p><strong>หมายเลขประจำตัวผู้ป่วย : </strong><br>
                            <p><?= htmlspecialchars($patient_info['hn']); ?></p>
                            </p>
                            <p><strong>น้ำหนัก</strong> <?= htmlspecialchars($patient_info['weight']); ?> กก. <strong>ส่วนสูง</strong> <?= htmlspecialchars($patient_info['height']); ?> ซม.</p>
                        </div>
                    </div>
                    <div class="divider"></div>

                    <!-- โลหิต -->
                    <div class="text-detail2">
                        <h4>โลหิต</h4>
                        <p>กรุ๊ปเลือด: <?= htmlspecialchars($patient_info['blood_type']); ?></p>

                        <?php
                        // ค่าชีพจร
                        $heart_rate = (int)$patient_info['heart_rate'];
                        if ($heart_rate >= 60 && $heart_rate <= 100) {
                            $heart_rate_status = "ปกติ";
                        } elseif ($heart_rate < 60) {
                            $heart_rate_status = "ต่ำกว่าปกติ";
                        } else {
                            $heart_rate_status = "สูงกว่าปกติ";
                        }
                        ?>

                        <p>ชีพจร: <?= htmlspecialchars($heart_rate); ?> ครั้ง/นาที (<?= $heart_rate_status; ?>)</p>

                        <?php
                        // ค่าความดันโลหิต
                        [$systolic, $diastolic] = explode('/', $patient_info['blood_pressure']);
                        $systolic = (int)$systolic;
                        $diastolic = (int)$diastolic;

                        if ($systolic >= 90 && $systolic <= 120 && $diastolic >= 60 && $diastolic <= 80) {
                            $blood_pressure_status = "ปกติ";
                        } elseif ($systolic > 120 || $diastolic > 80) {
                            $blood_pressure_status = "สูงกว่าปกติ";
                        } elseif ($systolic < 90 || $diastolic < 60) {
                            $blood_pressure_status = "ต่ำกว่าปกติ";
                        } else {
                            $blood_pressure_status = "ไม่สามารถระบุสถานะได้";
                        }
                        ?>

                        <p>ค่าความดัน: <?= htmlspecialchars($patient_info['blood_pressure']); ?> (<?= $blood_pressure_status; ?>)</p>
                    </div>


                    <div class="divider"></div>

                    <!-- รายละเอียดการรักษา -->
                    <div class="text-detail2">
                        <h4>รายละเอียดการรักษา</h4>
                        <?php if (!empty($patient_info['doctor_diagnosis'])): ?>
                            <p>อาการ: <?= nl2br(htmlspecialchars($patient_info['doctor_diagnosis'])); ?></p>
                        <?php else: ?>
                            <p>ยังไม่ได้บันทึกการรักษา</p>
                        <?php endif; ?>
                    </div>

                    <div class="divider"></div>


                    <!-- ความคิดเห็นจากแพทย์ -->
                    <div class="text-detail2">
                        <h4>ความคิดเห็นจากแพทย์</h4>
                        <p><?= nl2br(htmlspecialchars($patient_info['additional_details'])); ?></p>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        // ฟังก์ชันพรีวิวภาพเมื่อคลิกที่รูป
        function previewImageClick() {
            const img = document.querySelector(".img-pro");
            const modal = document.getElementById("imagePreviewModal");
            const modalImg = document.getElementById("modalImage");

            modal.style.display = "flex"; // ใช้ 'flex' แทน 'block' เพื่อให้สามารถใช้การจัดแนวแบบ flex ได้
            modalImg.src = img.src;
        }

        // ฟังก์ชันปิดพรีวิว
        function closeModal() {
            const modal = document.getElementById("imagePreviewModal");
            modal.style.display = "none";
        }
    </script>



</body>

</html>