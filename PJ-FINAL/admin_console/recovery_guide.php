<?php
include 'mid_string.php';

// ดึง doctor_id จาก session
$doctor_id = $_SESSION['user_login'];

// ใช้ prepared statement กับ SQL ที่ถูกต้อง
$query = "
SELECT p.first_name, p.last_name, a.appointment_date, a.patient_request, 
       a.doctor_response, a.appointment_id, a.treatment_area, a.doctor_diagnosis
FROM patients_info p
JOIN appointments a ON p.id = a.patient_id
WHERE a.doctor_id = :doctor_id AND a.patient_request IS NOT NULL AND a.patient_request != ''";


$stmt = $conn->prepare($query);
$stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>ให้ความรู้หลังการรักษา</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>

<body>
    <div class="main-wrapper">

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">ขอคำแนะนำหลังการรักษา</h4>
                    </div>
                </div>

                <div id="patient-list">
                    <?php if ($results): ?>
                        <?php foreach ($results as $row): ?>
                            <div class='card mb-3'>
                                <div class='card-body'>
                                    <h5 class='card-title'>ชื่อผู้ป่วย : <?= $row['first_name'] . " " . $row['last_name'] ?></h5>
                            
                                    <p><strong>การรักษา :</strong> <?= $row['treatment_area'] ?></p>
                                    <p><strong>การวินิจฉัยของหมอ :</strong></p>
                                    <textarea class="form-control" rows="3" readonly><?= $row['doctor_diagnosis'] ?></textarea>

                                    <p><strong>คนไข้ขอคำแนะนำหลังการรักษา :</strong></p>
                                    <textarea class='form-control' rows='3' readonly><?= $row['patient_request'] ?></textarea>

                                    <p><strong>คำแนะนำจากแพทย์ล่าสุด :</strong></p>
                                    <textarea class="form-control" rows="3" readonly><?= $row['doctor_response'] ?></textarea>

                                    <form class='doctor-response-form mt-3'>
                                        <input type='hidden' name='appointment_id' value='<?= $row['appointment_id'] ?>'>
                                        <label><strong>ให้คำแนะนำหลังการรักษากับคนไข้:</strong></label>
                                        <textarea class='form-control' rows='3' name='doctor_response'></textarea>
                                        <button type='submit' class='btn btn-primary mt-2'>บันทึกคำตอบ</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class='alert alert-info'>ไม่มีคำขอคำแนะนำจากผู้ป่วย</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>



    <script>
        $(document).ready(function() {
            $(".doctor-response-form").on("submit", function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: "./config/db-update_recovery.php",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.status === "success") {
                            Swal.fire({
                                title: "บันทึกสำเร็จ!",
                                text: "คำตอบจากหมอถูกบันทึกแล้ว",
                                icon: "success",
                                confirmButtonText: "ตกลง"
                            }).then(() => {
                                location.reload(); // รีเฟรชหน้าเพื่อแสดงข้อมูลที่อัปเดต
                            });
                        } else {
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: res.message,
                                icon: "error",
                                confirmButtonText: "ตกลง"
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: "ผิดพลาด!",
                            text: "เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์",
                            icon: "error",
                            confirmButtonText: "ตกลง"
                        });
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>



</body>

</html>