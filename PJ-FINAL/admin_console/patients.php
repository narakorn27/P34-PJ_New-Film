<?php include 'mid_string.php'; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <title>ENT-CENTER OPD Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.8/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">
    <style>
        .custom-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #fff;
        }

        .status-pending {
            background-color: #00adfe;
            border: 1px solid #00adfe;
            color: #ffffff;
        }

        .status-checked {
            background-color: #ffc107;
            border: 1px solid #ffc107;
            color: #ffffff;
        }

        .status-completed {
            background-color: #00e712;
            border: 1px solid #00ce7c;
            color: #ffffff;
        }

        .patient-link {
            color: #007bff !important;
            text-decoration: none !important;
        }

        .patient-link:hover {
            text-decoration: underline !important;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="content">

            <!-- เพิ่มตัวเลือกเดือนและปี -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-center">
                            <form method="GET" action="" class="d-flex align-items-center">
                                <label for="month" class="mr-2">เลือกเดือน:</label>
                                <select name="month" id="month" class="form-control mr-3" style="width: auto;">
                                    <option value="">ทุกเดือน</option>
                                    <option value="1" <?= (isset($_GET['month']) && $_GET['month'] == '1') ? 'selected' : ''; ?>>มกราคม</option>
                                    <option value="2" <?= (isset($_GET['month']) && $_GET['month'] == '2') ? 'selected' : ''; ?>>กุมภาพันธ์</option>
                                    <option value="3" <?= (isset($_GET['month']) && $_GET['month'] == '3') ? 'selected' : ''; ?>>มีนาคม</option>
                                    <option value="4" <?= (isset($_GET['month']) && $_GET['month'] == '4') ? 'selected' : ''; ?>>เมษายน</option>
                                    <option value="5" <?= (isset($_GET['month']) && $_GET['month'] == '5') ? 'selected' : ''; ?>>พฤษภาคม</option>
                                    <option value="6" <?= (isset($_GET['month']) && $_GET['month'] == '6') ? 'selected' : ''; ?>>มิถุนายน</option>
                                    <option value="7" <?= (isset($_GET['month']) && $_GET['month'] == '7') ? 'selected' : ''; ?>>กรกฎาคม</option>
                                    <option value="8" <?= (isset($_GET['month']) && $_GET['month'] == '8') ? 'selected' : ''; ?>>สิงหาคม</option>
                                    <option value="9" <?= (isset($_GET['month']) && $_GET['month'] == '9') ? 'selected' : ''; ?>>กันยายน</option>
                                    <option value="10" <?= (isset($_GET['month']) && $_GET['month'] == '10') ? 'selected' : ''; ?>>ตุลาคม</option>
                                    <option value="11" <?= (isset($_GET['month']) && $_GET['month'] == '11') ? 'selected' : ''; ?>>พฤศจิกายน</option>
                                    <option value="12" <?= (isset($_GET['month']) && $_GET['month'] == '12') ? 'selected' : ''; ?>>ธันวาคม</option>
                                </select>

                                <label for="year" class="mr-2 ml-3">เลือกปี:</label>
                                <select name="year" id="year" class="form-control mr-3" style="width: auto;">
                                    <option value="">ทุกปี</option>
                                    <?php
                                    $current_year = date('Y');
                                    for ($year = $current_year; $year >= 2000; $year--) {
                                        echo "<option value='$year'" . (isset($_GET['year']) && $_GET['year'] == $year ? ' selected' : '') . ">$year</option>";
                                    }
                                    ?>
                                </select>

                                <button type="submit" class="btn btn-primary">กรอง</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ตารางข้อมูลผู้ป่วยภายในการ์ด -->
            <div class="container mt-5">
                <?php if ($user['role'] === 'doctor'): ?>
                    <h2 class="mb-4">ข้อมูลของผู้ป่วยที่ได้รับผิดชอบดูแล</h2>
                <?php else: ?>
                    <h2 class="mb-4">ข้อมูลคนไข้ทั้งหมด</h2>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-border table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>ชื่อผู้ป่วย</th>
                                        <th>ประเภทการรักษา</th>
                                        <th>อาการที่รายงาน</th>
                                        <th>วันที่สร้าง</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // ตรวจสอบ role และกำหนด SQL ตามเงื่อนไข
                                    $month = isset($_GET['month']) ? $_GET['month'] : '';
                                    $year = isset($_GET['year']) ? $_GET['year'] : '';

                                    if ($user['role'] === 'admin' || $user['role'] === 'nurse') {
                                        // ดึงข้อมูลผู้ป่วยทั้งหมดในระบบสำหรับ admin
                                        $sql = "SELECT 
                                            p.id AS patient_id, 
                                            p.first_name, 
                                            p.last_name, 
                                            p.additional_details, 
                                            p.status, 
                                            a.treatment_area, 
                                            DATE_FORMAT(p.created_at, '%Y-%m-%d') AS created_at,  
                                            p.created_at
                                        FROM appointments a
                                        JOIN patients_info p ON a.patient_id = p.id";

                                        // หากมีการเลือกเดือน
                                        if ($month) {
                                            $sql .= " WHERE MONTH(p.created_at) = :month";
                                        }

                                        // หากมีการเลือกปี
                                        if ($year) {
                                            if ($month) {
                                                $sql .= " AND YEAR(p.created_at) = :year";
                                            } else {
                                                $sql .= " WHERE YEAR(p.created_at) = :year";
                                            }
                                        }

                                        $stmt = $conn->prepare($sql);
                                        if ($month) {
                                            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                                        }
                                        if ($year) {
                                            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                                        }
                                        $stmt->execute();
                                    } else {
                                        // ดึงข้อมูลเฉพาะของหมอที่กำลังล็อกอิน
                                            $sql = "SELECT 
                                            p.id AS patient_id, 
                                            p.first_name, 
                                            p.last_name, 
                                            p.additional_details, 
                                            p.status, 
                                            a.treatment_area, 
                                            DATE_FORMAT(p.created_at, '%Y-%m-%d') AS created_at,  
                                            p.created_at
                                        FROM appointments a
                                        JOIN patients_info p ON a.patient_id = p.id
                                        WHERE a.doctor_id = :doctor_id";

                                        // หากมีการเลือกเดือน
                                        if ($month) {
                                            $sql .= " AND MONTH(p.created_at) = :month";
                                        }

                                        // หากมีการเลือกปี
                                        if ($year) {
                                            $sql .= " AND YEAR(p.created_at) = :year";
                                        }

                                        $stmt = $conn->prepare($sql);
                                        $stmt->bindParam(':doctor_id', $user_id, PDO::PARAM_STR);
                                        if ($month) {
                                            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                                        }
                                        if ($year) {
                                            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                                        }
                                        $stmt->execute();
                                    }

                                    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($appointments) {
                                        foreach ($appointments as $row) {
                                            echo "<tr>";
                                            echo "<td><a href='edit_patient.php?id=" . urlencode($row['patient_id']) . "' class='patient-link'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</a></td>";
                                            echo "<td>" . htmlspecialchars($row['treatment_area']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['additional_details']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";

                                            // กำหนดสถานะพร้อม CSS
                                            $status_th = '';
                                            $badge_class = '';
                                            switch ($row['status']) {
                                                case 'Pending':
                                                    $status_th = 'รอตรวจ';
                                                    $badge_class = 'status-pending';
                                                    break;
                                                case 'Checked':
                                                    $status_th = 'กำลังตรวจ';
                                                    $badge_class = 'status-checked';
                                                    break;
                                                case 'Completed':
                                                    $status_th = 'เสร็จสิ้น';
                                                    $badge_class = 'status-completed';
                                                    break;
                                            }
                                            echo "<td><span class='custom-badge $badge_class'>" . htmlspecialchars($status_th) . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>ไม่มีข้อมูล</td></tr>";
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Updated jQuery -->
</body>