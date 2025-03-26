<?php
// เริ่มต้น session
session_start();
include('./config/connect_database.php');

// ดึงข้อมูลจาก session ที่เก็บ id
$id = $_SESSION['id']; // เช่น PT00001

// ตรวจสอบว่า session มี id หรือไม่
$patient_request = "ไม่มีข้อมูลที่ขอคำแนะนำล่าสุด";
$doctor_response = "ไม่มีคำแนะนำจากบุคลากรทางการแพทย์";
if (isset($_SESSION['id'])) {
    $patient_id = $_SESSION['id'];

    // ดึงข้อมูล patient_request และ doctor_response จากฐานข้อมูล
    $sql = "
    SELECT a.patient_request, a.doctor_response 
    FROM appointments a 
    WHERE a.patient_id = ? 
    ORDER BY a.appointment_date DESC LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $patient_id); // เปลี่ยนเป็น 's' ถ้า patient_id เป็น string
    $stmt->execute();
    $stmt->bind_result($patient_request, $doctor_response);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>


<?php
include 'mid_string.php'
?>


<head>
    <link rel="stylesheet" href="assets/css/styles_recovery_guide.css" />
    <title>ขอคำแนะนำหลังการรักษา</title>
</head>

<body>
    <main>

        <div class="head-first">
            <h3>สอบถามหลังการรักษา</h3>
        </div>

        <div class="section-content">
            <form id="updateDetailsForm">
                <div class="appointment-card">
                    <p style="font-size: 20px; font-weight: bold;">ขอคำแนะนำหลังการรักษาเพิ่มเติม</p>
                    <div class="grey-card">
                        <textarea id="inputText" name="patient_request" class="form-control" rows="5" placeholder="กรอกข้อมูลที่ต้องการสอบถามทีมแพทย์พยาบาล"></textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" id="saveButton" class="btn btn-primary">
                            <i class="fas fa-save"></i> บันทึก
                        </button>
                    </div>
                </div>
            </form>






            <!-- การ์ดใหม่ -->
            <div class="new-appointment-card">
                <p style="font-size: 20px; font-weight: bold;">คำแนะนำการดูแลตัวเองจากทีมแพทย์พยาบาล</p>
                <div class="grey-card">
                    <p id="inputText" class="form-control" rows="6" readonly>
                        <strong>คำขอคำแนะนำล่าสุด:</strong>
                        <?php echo htmlspecialchars($patient_request); ?>
                    </p>
                    <p id="inputText" class="form-control" rows="6" readonly>
                        <strong>คำแนะนำจากทีมแพทย์พยาบาล:</strong>
                        <?php echo htmlspecialchars($doctor_response); ?>
                    </p>
                </div>
            </div>



        </div>


    </main>

    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // เมื่อคลิกปุ่มบันทึก
        document.getElementById('saveButton').addEventListener('click', function() {
            const formElement = document.getElementById('updateDetailsForm');

            // สร้าง FormData จากฟอร์ม
            const formData = new FormData(formElement);

            // ตรวจสอบว่า formData มีข้อมูล patient_request หรือไม่
            if (!formData.has('patient_request')) {
                console.error('Patient request is missing!');
                return;
            }

            // ส่งข้อมูลไปยัง PHP
            fetch('./config/save_recovery.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text()) // รับข้อความจาก PHP
                .then(result => {
                    console.log('Result from server:', result); // แสดงข้อความจาก PHP

                    // ถ้าผลลัพธ์เป็น "Update successful"
                    if (result.trim() === "Update successful") {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: 'ข้อมูลของคุณถูกบันทึกเรียบร้อยแล้ว!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // ถ้าผลลัพธ์ไม่ใช่ "Update successful"
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: result, // แสดงข้อความจาก PHP
                            showConfirmButton: true
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่!',
                        showConfirmButton: true
                    });
                    console.error('Error:', error);
                });
        });
    </script>



</body>