<?php
// เริ่มต้นการเชื่อมต่อฐานข้อมูล
include('./config/connect_database.php');
session_start();

// ตรวจสอบว่า $appointment_id ถูกส่งมาหรือไม่
if (!isset($_GET['appointment_id'])) {
    die("ไม่พบ appointment_id");
}

$appointment_id = $_GET['appointment_id'];

// ตรวจสอบว่า session มีข้อมูลผู้ใช้หรือไม่
if (!isset($_SESSION['id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

$patient_id = $_SESSION['id']; // เอาค่า patient_id จาก session

// SQL Query
$sql = "
    SELECT 
        a.appointment_date,  
        pi.first_name, 
        pi.last_name
    FROM appointments a
    JOIN patients_info pi ON a.patient_id = pi.id
    WHERE a.appointment_id = '$appointment_id' AND a.patient_id = '$patient_id'
";

// รัน Query
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Fetch ข้อมูล
$data = mysqli_fetch_assoc($result);
if (!$data) {
    die("ไม่พบข้อมูลการนัดหมายนี้");
}

// Debug: แสดงข้อมูลที่ดึงมาได้
error_log("Fetched Data: " . print_r($data, true));
?>

<?php
include 'mid_string.php'
?>

<?php
setlocale(LC_TIME, 'th_TH.UTF-8', 'th_TH', 'Thai');
$thaiMonth = strftime('%B', strtotime($data['appointment_date']));

// แปลง encoding ให้แน่ใจว่าเป็น UTF-8
$thaiMonth = iconv('TIS-620', 'UTF-8', $thaiMonth);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลการนัดหมาย</title>
    <link rel="stylesheet" href="assets/css/style_edit-appointment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
</head>

<body>
    <div class="container-app">
        <div class="head-first">
            <h2>แจ้งเลื่อนวันนัดหมาย</h2>
        </div>

        <div class="content-form">
            <div class="titile-header">
                <h3>ระบุวันนัดหมาย ที่สะดวก</h3>
            </div>
            <form method="POST" action="./config/update_new_appointment.php">
                <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>"> <!-- ส่งค่า appointment_id ไปในฟอร์ม -->

                <!-- Button to trigger calendar popup -->
                <div class="row mb-3">
                    <button id="selectDateButton" class="btn btn-primary" type="button">เลือกวัน</button>
                </div>

                <!-- Date Row -->
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="day" class="form-label">วัน</label>
                        <input type="text" id="day" name="day" class="form-control" value="<?= date('d', strtotime($data['appointment_date'])) ?>" readonly>
                    </div>

                    <div class="col-4">
                        <label for="month" class="form-label">เดือน</label>
                        <input type="text" id="month" name="month" class="form-control" value="<?= $thaiMonth ?>" readonly>
                    </div>

                    <div class="col-4">
                        <label for="year" class="form-label">ปี (พ.ศ.)</label>
                        <input type="text" id="year" name="year" class="form-control" value="<?= date('Y', strtotime($data['appointment_date'])) + 543 ?>" readonly>
                    </div>
                </div>

                <!-- Flatpickr Datepicker -->
                <input type="text" id="datepicker" style="display: none;">

                <!-- Time Slot -->
                <div class="mb-3">
                    <label for="mdtimepicker" class="form-label">เลือกเวลา</label>
                    <input id="mdtimepicker" name="mdtimepicker" class="form-control" value="<?= htmlspecialchars(date('H:i', strtotime($data['appointment_date']))) ?>">
                </div>

                <!-- Reason for Rescheduling -->
                <div class="mb-3">
                    <label for="rescheduling_reason" class="form-label">สาเหตุในการเลื่อนนัด: </label>
                    <textarea id="rescheduling_reason" name="rescheduling_reason" class="form-control" placeholder="กรุณาระบุเหตุผล : หากท่านไม่สามารถมานัดหมายได้ กรุณาแจ้งให้ทราบล่วงหน้าเพื่อให้สามารถเปิดช่องทางให้กับคนไข้ท่านอื่น ๆ" rows="4"></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5" style="margin-bottom: 2rem;">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Map เดือนภาษาไทยเป็นตัวเลข
            const thaiMonths = {
                "มกราคม": "01",
                "กุมภาพันธ์": "02",
                "มีนาคม": "03",
                "เมษายน": "04",
                "พฤษภาคม": "05",
                "มิถุนายน": "06",
                "กรกฎาคม": "07",
                "สิงหาคม": "08",
                "กันยายน": "09",
                "ตุลาคม": "10",
                "พฤศจิกายน": "11",
                "ธันวาคม": "12"
            };

            // ตั้งค่า flatpickr สำหรับการเลือกวัน
            flatpickr("#datepicker", {
                minDate: "today", // ไม่ให้เลือกวันที่ก่อนวันนี้
                disableMobile: true, // ปิดการใช้งาน Mobile UI
                locale: {
                    firstDayOfWeek: 0, // วันเริ่มต้นของสัปดาห์ (อาทิตย์)
                    weekdays: {
                        shorthand: ["อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"], // ชื่อวันในสัปดาห์
                        longhand: ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุกร์", "เสาร์"]
                    },
                    months: {
                        shorthand: ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."], // ชื่อเดือนย่อ
                        longhand: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"] // ชื่อเดือนเต็ม
                    }
                },
                dateFormat: "d F Y", // แสดงวันที่เป็น "01 มกราคม 2568"
                onChange: function(selectedDates, dateStr, instance) {
                    const selectedDate = new Date(selectedDates[0]);
                    const day = selectedDate.getDate().toString().padStart(2, '0');

                    // แปลงเดือนให้เป็นภาษาไทย
                    const month = selectedDate.toLocaleString('th-TH', {
                        month: 'long'
                    });

                    // แปลงปี ค.ศ. เป็น พ.ศ.
                    const year = selectedDate.getFullYear() + 543; // เพิ่มปี พ.ศ.

                    // ยัดค่าลงในช่อง
                    $("#day").val(day);
                    $("#month").val(month); // ใช้เดือนภาษาไทย
                    $("#year").val(year); // ใช้ปี พ.ศ.
                },
                // เมื่อคลิกเลือกปีให้แปลงปีให้เป็น พ.ศ. แสดงในรูปแบบที่เลือก
                onYearChange: function(selectedDates, dateStr, instance) {
                    // เมื่อปีถูกเลือกจากปฏิทิน
                    const year = selectedDates[0].getFullYear() + 543; // แปลงปี ค.ศ. เป็น พ.ศ.
                    instance.setDate(new Date(selectedDates[0].setFullYear(year)));
                }
            });

            // เมื่อกดปุ่มให้แสดงปฏิทิน
            $("#selectDateButton").click(function() {
                $("#datepicker").click(); // เปิด flatpickr
            });

            // ตั้งค่า Flatpickr สำหรับเลือกเวลา
            $("#mdtimepicker").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
                onChange: function(selectedDates, dateStr, instance) {
                    let day = $("#day").val();
                    let month = $("#month").val();
                    let year = $("#year").val();

                    // ตรวจสอบว่ามีวันที่แล้วหรือยัง
                    if (!day || !month || !year) {
                        alert("กรุณาเลือกวันที่ก่อนเลือกเวลา!");
                        return;
                    }

                    // แปลงเดือนเป็นตัวเลขเฉพาะตอนบันทึก
                    const monthNumber = thaiMonths[month] || "00";

                    // รวมวันที่และเวลา
                    const appointmentDate = `${year}-${monthNumber}-${day} ${dateStr}:00`;
                    console.log("วันเวลาที่เลือก:", appointmentDate);
                }
            });
        });
    </script>



    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

            let formData = new FormData(this); // รับข้อมูลจากฟอร์ม

            fetch('./config/update_new_appointment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // รับข้อมูลจาก server
                .then(data => {
                    console.log(data); // ตรวจสอบข้อมูลที่ได้รับจาก server

                    if (data.success) { // ตรวจสอบ success จาก PHP
                        // แสดงผลลัพธ์ด้วย SweetAlert2 สำหรับข้อความสำเร็จ
                        Swal.fire({
                            title: 'สำเร็จ',
                            text: data.message, // ข้อความจาก PHP
                            icon: 'success'
                        }).then(() => {
                            // กรณีต้องการ redirect หลังจากแสดง SweetAlert
                            window.location.href = "appointments_detail.php?appointment_id=" + data.appointment_id;
                        });
                    } else if (data.error) { // หากพบข้อผิดพลาดจาก PHP
                        // แสดงผลลัพธ์ด้วย SweetAlert2 สำหรับข้อความข้อผิดพลาด
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด',
                            text: data.error, // ข้อความจาก PHP
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        icon: 'error'
                    });
                });
        });
    </script>




</body>

</html>