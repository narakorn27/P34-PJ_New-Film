<?php
include 'mid_string.php';

$filter = $_GET['filter'] ?? 'all'; // ดึงค่ากรองจาก GET (default: all)
$dateCondition = "";

// เงื่อนไขกรองตามช่วงเวลา
if ($filter === 'day') {
    $dateCondition = "AND DATE(a.appointment_date) = CURDATE()";
} elseif ($filter === 'week') {
    $dateCondition = "AND YEARWEEK(a.appointment_date, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $dateCondition = "AND MONTH(a.appointment_date) = MONTH(CURDATE()) AND YEAR(a.appointment_date) = YEAR(CURDATE())";
}

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_login'] ?? null;

$idCondition = ($role === 'admin') ? "" : "AND a.doctor_id = :id";

$sql = "SELECT 
            a.appointment_id,
            p.first_name AS patient_first_name, 
            p.last_name AS patient_last_name, 
            p.age,
            m.first_name AS doctor_first_name,
            m.last_name AS doctor_last_name,
            COALESCE(NULLIF(a.rescheduling_reason, ''), 'ยังไม่มีการเลื่อนนัด') AS rescheduling_reason,
            COALESCE(DATE_FORMAT(a.appointment_date, '%Y-%m-%d %H:%i:%s'), 'ยังไม่มีการเลื่อนนัด') AS appointment_date,
            a.status
        FROM appointments a
        JOIN patients_info p ON a.patient_id = p.id
        JOIN medical_staff m ON a.doctor_id = m.id
        WHERE 1=1 $idCondition $dateCondition";

$stmt = $conn->prepare($sql);
if ($role !== 'admin' && $user_id !== null) {
    $stmt->bindParam(':id', $user_id, PDO::PARAM_STR);
}
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


$currentDate = date('Y-m-d'); // วันปัจจุบัน

// ตรวจสอบสถานะของการนัดหมาย หากยังเป็น 'confirmed' และถึงวันนัดหมายแล้ว
foreach ($appointments as $appointment) {
    $appointmentDate = date('Y-m-d', strtotime($appointment['appointment_date']));

    // ถ้าสถานะเป็น 'confirmed' และถึงวันนัดหมายแล้ว
    if ($appointment['status'] === 'confirmed' && $appointmentDate < $currentDate) {
        // เปลี่ยนสถานะเป็น 'cancelled'
        $updateStatus = "UPDATE appointments SET status = 'cancelled' WHERE appointment_id = ?";
        $stmt = $conn->prepare($updateStatus);
        $stmt->execute([$appointment['appointment_id']]);
    }
}

?>




<head>
    <title>ตารางการนัดหมาย</title>

    <link rel="stylesheet" type="text/css" href="./css/style_index.css">


    <style>
        /* ปรับสไตล์ตารางให้ดูดีขึ้น */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        /* สีของเหตุผลการเลื่อนนัด */
        .reschedule-reason {
            color: red;
            font-weight: bold;
        }

        .status-blue {
            background-color: #007bff;
            /* ฟ้า 🔵 */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .status-yellow {
            background-color: #ffc107;
            /* เหลือง 🟡 */
            color: black;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .status-red {
            background-color: #dc3545;
            /* แดง ❌ */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .filter-buttons button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
            margin-bottom: 1rem;
        }

        .filter-buttons .all {
            background-color: #009efb;
            color: white;
        }

        .filter-buttons .day {
            background-color: rgb(4, 118, 31);
            color: white;
        }

        .filter-buttons .week {
            background-color: rgb(255, 197, 7);
            color: white;
        }

        .filter-buttons .month {
            background-color: #007bff;
            color: white;
        }

        .filter-buttons .active {
            border: 2px solid black;
        }

        .status-button {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            text-align: center;
            display: inline-block;
            margin: 5px;
        }

        .status-button.confirmed {
            background-color: #28a745;
            /* สีเขียว */
        }

        .status-button.rescheduled {
            background-color: #fd7e14;
            /* สีส้ม */
        }

        .status-button.cancelled {
            background-color: #dc3545;
            /* สีแดง */
        }

        .status-button.finished {
            background-color: #28a745;
            /* สีเขียว */
            color: white;
        }

        .status-button.default {
            background-color: #6c757d;
            /* สีเทา */
        }
    </style>


</head>

<body>

    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-4 col-3">
                    <h4 class="page-title" data-lang-en="Appointments" data-lang-th="การนัดหมาย">การนัดหมาย</h4>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <div class="filter-buttons">
                            <button onclick="filterAppointments('all')" class="all <?php echo ($filter === 'all') ? 'active' : ''; ?>">ทั้งหมด</button>
                            <button onclick="filterAppointments('day')" class="day <?php echo ($filter === 'day') ? 'active' : ''; ?>">วัน</button>
                            <button onclick="filterAppointments('week')" class="week <?php echo ($filter === 'week') ? 'active' : ''; ?>">อาทิตย์</button>
                            <button onclick="filterAppointments('month')" class="month <?php echo ($filter === 'month') ? 'active' : ''; ?>">เดือน</button>
                        </div>

                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>หมายเลขการนัดหมาย</th>
                                    <th>ชื่อคนไข้</th>
                                    <th>อายุ</th>
                                    <th>ชื่อหมอที่ดูแลรับผิดชอบ</th>
                                    <th>เหตุผลในการจัดตารางใหม่</th>
                                    <th>วัน & เวลา การนัดหมาย</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['age']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></td>

                                        <!-- แสดงเหตุผลการเลื่อนนัดเป็นสีแดง -->
                                        <td>
                                            <span class="<?php echo ($appointment['rescheduling_reason'] !== 'ยังไม่มีการเลื่อนนัด') ? 'reschedule-reason' : ''; ?>">
                                                <?php echo htmlspecialchars($appointment['rescheduling_reason']); ?>
                                            </span>
                                        </td>

                                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>

                                        <td>
                                            <?php
                                            $status = $appointment['status'];
                                            $appointmentDate = date('Y-m-d', strtotime($appointment['appointment_date'])); // วันที่นัดหมาย
                                            $currentDate = date('Y-m-d'); // วันปัจจุบัน

                                            // ตรวจสอบว่าถึงวันนัดหมายแล้วและยังคงเป็น 'confirmed'
                                            if ($status === 'confirmed' && $appointmentDate < $currentDate) {
                                                echo '<span class="status-button cancelled">ยกเลิกนัด</span>';
                                            } else {
                                                switch ($status) {
                                                    case 'confirmed':
                                                        echo '<span class="status-button confirmed">รอเข้านัดหมาย</span>';
                                                        break;
                                                    case 'rescheduled':
                                                        echo '<span class="status-button rescheduled">ขอเลื่อนนัด</span>';
                                                        break;
                                                    case 'cancelled':
                                                        echo '<span class="status-button cancelled">ยกเลิกนัด</span>';
                                                        break;
                                                    case 'finished':
                                                        echo '<span class="status-button finished">เสร็จสิ้น</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="status-button default">สถานะไม่ชัดเจน</span>';
                                                        break;
                                                }
                                            }
                                            ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                                <!-- กรณีไม่มีนัดหมาย -->
                                <?php if (empty($appointments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">ไม่พบวันนัดหมาย</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>



    <div class="sidebar-overlay" data-reff=""></div>



    <script>
        function filterAppointments(filter) {
            window.location.href = '?filter=' + filter;
        }
    </script>


    <script>
        $(document).ready(function() {
            $('.status-dropdown').change(function() {
                var appointmentId = $(this).data('id'); // ดึง appointment_id
                var newStatus = $(this).val(); // ดึงค่าที่เลือก

                $.ajax({
                    url: 'config/update_appointment_status.php', // ไฟล์อัปเดต
                    type: 'POST',
                    data: {
                        id: appointmentId,
                        status: newStatus
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตสถานะสำเร็จ!',
                            text: 'สถานะของการนัดหมายถูกเปลี่ยนแล้ว',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถเปลี่ยนสถานะได้ กรุณาลองใหม่อีกครั้ง',
                        });
                    }
                });
            });
        });
    </script>

    <script>
        $('.status-dropdown').change(function() {
            var selectedOption = $(this).val();
            var parentTd = $(this).closest('td');

            parentTd.removeClass('status-green status-yellow status-red');

            if (selectedOption === 'confirmed') {
                parentTd.addClass('status-blue'); // รอเข้านัดหมาย
            } else if (selectedOption === 'rescheduled') {
                parentTd.addClass('status-yellow'); // ขอเลื่อนนัด
            } else if (selectedOption === 'cancelled') {
                parentTd.addClass('status-red'); // ยกเลิกนัด
            }
        });
    </script>





</body>