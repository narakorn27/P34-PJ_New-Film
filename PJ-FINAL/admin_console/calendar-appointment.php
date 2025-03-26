<?php
// เชื่อมต่อฐานข้อมูล
include 'mid_string.php';

try {
    // ตรวจสอบบทบาทของผู้ใช้
    $stmt = $conn->prepare('SELECT role FROM medical_staff WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('ไม่พบข้อมูลผู้ใช้');
    }

    // กำหนด SQL ตามบทบาทของผู้ใช้
    if ($user['role'] === 'admin' || $user['role'] === 'nurse') {
        // พยาบาลและแอดมินเห็นปฏิทินทั้งหมด
        $sql = "SELECT 
        a.appointment_id, 
        a.patient_id, 
        DATE_FORMAT(a.appointment_date, '%Y-%m-%dT%H:%i:%s') AS start, 
        DATE_FORMAT(a.appointment_date, '%H:%i') AS time, 
        a.treatment_area, 
        a.doctor_diagnosis,
        p.hn, 
        p.first_name, 
        p.last_name, 
        p.gender, 
        p.age
    FROM appointments a
    JOIN patients_info p ON a.patient_id = p.id";
        $stmt = $conn->prepare($sql);
    } else {
        // หมอเห็นเฉพาะของตัวเอง
        $sql = "SELECT 
        a.appointment_id, 
        a.patient_id, 
        DATE_FORMAT(a.appointment_date, '%Y-%m-%dT%H:%i:%s') AS start, 
        DATE_FORMAT(a.appointment_date, '%H:%i') AS time, 
        a.treatment_area, 
        a.doctor_diagnosis,
        p.hn, 
        p.first_name, 
        p.last_name, 
        p.gender, 
        p.age
    FROM appointments a
    JOIN patients_info p ON a.patient_id = p.id
    WHERE a.doctor_id = :doctor_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $user_id, PDO::PARAM_STR);
    }

    // Execute query
    $stmt->execute();

    $appointments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $appointments[] = [
            'id' => $row['appointment_id'],
            'title' => 'มีตรวจ',
            'time' => $row['time'],
            'start' => $row['start'],
            'patient_id' => $row['patient_id'],
            'description' => $row['doctor_diagnosis'],
            'hn' => $row['hn'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'gender' => $row['gender'],
            'age' => $row['age'],
            'treatment_area' => $row['treatment_area'],
        ];
    }

    // แปลงข้อมูลเป็น JSON
    $appointments_json = json_encode($appointments, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    $appointments_json = json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    $appointments_json = json_encode(['error' => $e->getMessage()]);
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <title>Preclinic - Medical & Hospital - Bootstrap 4 Admin Template</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">



    <style>
        #calendar {
            height: 90vh;
            /* กำหนดความสูงให้พอดี */
            overflow-y: auto;
            /* เปิด Scroll bar ถ้าข้อมูลเยอะ */
        }

        .fc-button.fc-button-active {
            background-color: #007bff !important;
            /* เปลี่ยนสีพื้นหลัง */
            border-color: #007bff !important;
            /* เปลี่ยนสีขอบ */
            color: #fff !important;
            /* เปลี่ยนสีตัวอักษร */
        }

        .fc-button.fc-button-active:hover {
            background-color: #0056b3 !important;
            /* เปลี่ยนสีพื้นหลังเมื่อ hover */
            border-color: #0056b3 !important;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-8 col-4">
                        <h4 class="page-title">ปฏิทินการนัดหมาย</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">รายละเอียดการนัดหมาย</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="modalContent"></div>
                    </div>
                </div>
            </div>
        </div>





    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./assets/js/jquery-3.2.1.min.js"></script>




    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/th.js"></script>





    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var events = <?php echo $appointments_json; ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'th', // ใช้ภาษาไทย
                events: events,
                editable: true,
                eventLimit: true, // เปิดใช้งาน event limit
                dayMaxEvents: 3, // จำกัดให้แสดงแค่ 3 รายการต่อวัน
                eventClick: function(info) {
                    const genderMapping = {
                        male: 'ชาย',
                        female: 'หญิง',
                        other: 'อื่นๆ'
                    };

                    const patientId = info.event.extendedProps.patient_id;
                    const treatmentArea = info.event.extendedProps.treatment_area;
                    const diagnosis = info.event.extendedProps.description;
                    const appointmentDate = info.event.start;
                    const patientHN = info.event.extendedProps.hn;
                    const firstName = info.event.extendedProps.first_name;
                    const lastName = info.event.extendedProps.last_name;
                    const gender = genderMapping[info.event.extendedProps.gender.toLowerCase()] || 'ไม่ระบุ';
                    const age = info.event.extendedProps.age;

                    Swal.fire({
                        title: 'รายละเอียดการนัดหมาย',
                        html: `
                    <p><strong>รหัสผู้ป่วย:</strong> ${patientHN}</p>
                    <p><strong>ชื่อ:</strong> ${firstName} ${lastName}</p>
                    <p><strong>เพศ:</strong> ${gender}</p>
                    <p><strong>อายุ:</strong> ${age}</p>
                    <p><strong>พื้นที่การรักษา:</strong> ${treatmentArea}</p>
                    <p><strong>คำอธิบาย:</strong> ${diagnosis}</p>
                    <p><strong>วันที่และเวลา:</strong> ${appointmentDate.toLocaleString()}</p>
                `,
                        confirmButtonText: 'ปิด',
                    });
                },
                dateClick: function(info) {
                    const genderMapping = {
                        male: 'ชาย',
                        female: 'หญิง',
                        other: 'อื่นๆ'
                    };

                    const selectedDate = info.dateStr;
                    const eventsOnDate = events.filter(event => event.start.startsWith(selectedDate));

                    let modalContent = eventsOnDate.length ?
                        eventsOnDate.map(event => {
                            const gender = genderMapping[event.gender?.toLowerCase()] || 'ไม่ระบุ';
                            return `
                        <p><strong>รหัสผู้ป่วย:</strong> ${event.hn}</p>
                        <p><strong>ชื่อ:</strong> ${event.first_name} ${event.last_name}</p>
                        <p><strong>เพศ:</strong> ${gender}</p>
                        <p><strong>เวลา:</strong> ${event.time}</p>
                        <hr>
                    `;
                        }).join('') :
                        '<p>ไม่มีคิวในวันนี้</p>';

                    Swal.fire({
                        title: `คิววันที่ ${selectedDate}`,
                        html: modalContent,
                        confirmButtonText: 'ปิด',
                    });
                },
                headerToolbar: {
                    left: 'prev,next today', // ปุ่ม "ย้อนกลับ", "ถัดไป", "วันนี้"
                    center: 'title', // ปรับการแสดงชื่อเดือนหรือปี
                    right: 'dayGridMonth,dayGridWeek,dayGridDay', // แสดงมุมมองต่าง ๆ
                },
                eventClassNames: function(info) {
                    if (info.event.extendedProps.type === 'consultation') {
                        return ['event-consultation'];
                    } else if (info.event.extendedProps.type === 'followup') {
                        return ['event-followup'];
                    } else {
                        return ['event-other'];
                    }
                }
            });

            calendar.render();
        });
    </script>




</body>

</html>