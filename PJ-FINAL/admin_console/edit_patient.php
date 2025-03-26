<?php
include 'mid_string.php';

// ตรวจสอบว่ามีการส่งค่า id หรือไม่
if (!isset($_GET['id'])) {
    die("ไม่พบข้อมูลผู้ป่วย");
}

$patient_id = $_GET['id'];

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
$stmt->execute([$patient_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("ไม่พบข้อมูลผู้ป่วย");
}

$patient_info = $result;

// ตรวจสอบว่าผู้ใช้มีรูปโปรไฟล์หรือไม่
if ($patient_info['profile_picture']) {
    $profile_picture = $patient_info['profile_picture'];
    $profile_image = 'data:image/jpeg;base64,' . base64_encode($profile_picture);
} else {
    $profile_image = 'assets/img/user.jpg';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ป่วย</title>
    <link rel="stylesheet" href="styles.css">


    <style>
        .img-pro {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 0.1px solid #686868;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">ข้อมูลผู้ป่วย</h4>
                    </div>
                </div>

                <div class="container mt-4">
                    <div class="card shadow-sm p-4">
                        <!-- ภาพโปรไฟล์ -->
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-4 text-center">
                                <img class="img-pro" src="<?= $profile_image ?>" alt="Profile Picture">
                            </div>
                        </div>
                        <hr>
                        <!-- ข้อมูลผู้ป่วย -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3"><?= htmlspecialchars($patient_info['first_name'] . ' ' . $patient_info['last_name']); ?></h4>
                                <p><strong>หมายเลขประจำตัวผู้ป่วย:</strong> <?= htmlspecialchars($patient_info['hn']); ?></p>
                                <p><strong>น้ำหนัก:</strong> <?= htmlspecialchars($patient_info['weight']); ?> กก. <strong>ส่วนสูง:</strong> <?= htmlspecialchars($patient_info['height']); ?> ซม.</p>
                            </div>
                        </div>
                        <hr>
                        <!-- ข้อมูลโลหิต -->
                        <div>
                            <h5 class="mb-3">ข้อมูลโลหิต</h5>
                            <p><strong>กรุ๊ปเลือด:</strong> <?= htmlspecialchars($patient_info['blood_type']); ?></p>
                            <p><strong>ชีพจร:</strong> <?= htmlspecialchars($patient_info['heart_rate']); ?> ครั้ง/นาที</p>
                            <p><strong>ค่าความดัน:</strong> <?= htmlspecialchars($patient_info['blood_pressure']); ?></p>
                        </div>
                        <hr>
                        <!-- รายละเอียดการรักษา -->
                        <div>
                            <h5 class="mb-3">รายละเอียดการรักษา</h5>
                            <p><?= nl2br(htmlspecialchars($patient_info['doctor_diagnosis'])); ?></p>
                        </div>
                        <hr>
                        <!-- ความคิดเห็นจากแพทย์ -->
                        <div>
                            <h5 class="mb-3">ความคิดเห็นจากแพทย์</h5>
                            <p><?= nl2br(htmlspecialchars($patient_info['additional_details'])); ?></p>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>


</body>

</html>