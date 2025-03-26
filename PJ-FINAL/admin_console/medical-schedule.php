<?php
include 'mid_string.php';


// กำหนดวันในสัปดาห์
$days = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
$schedule = [];

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT ms.doctor_id, ms.work_day, ms.category, 
               m.first_name, m.last_name 
        FROM medical_schedule ms
        JOIN medical_staff m ON ms.doctor_id = m.id
        ORDER BY ms.category, FIELD(ms.work_day, 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์')";

$stmt = $conn->query($sql); // ใช้ PDO query()
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // ใช้ fetchAll() แทน fetch_assoc()

foreach ($result as $row) {
    $schedule[$row['category']][$row['work_day']][] = $row['first_name'] . " " . $row['last_name'];
}

// ปิดการเชื่อมต่อ
$conn = null;
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <title>Preclinic - Medical & Hospital - Bootstrap 4 Admin Template</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">
    <title>ตารางเวรแพทย์</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #ddd;
        }

        .category {
            background-color: #f4f4f4;
            font-weight: bold;
        }


        /* ใช้ .custom-table เพื่อกำหนดตาราง */
        .custom-table td,
        .custom-table th {
            padding: .75rem !important;
            /* เพิ่ม padding */
            vertical-align: top !important;
            border-top: 1px solidrgb(0, 0, 0) !important;
            /* ขอบตารางด้านบน */
        }

        /* กำหนดเส้นขอบและการตั้งค่าสีพื้นหลังให้เหมาะสม */
        .custom-table th {
            background-color: #f2f2f2 !important;
            border-bottom: 1px solid #333 !important;
        }

        .custom-table td {
            background-color: #fff !important;
        }

        /* เพิ่มการตั้งค่าสีพื้นหลังให้กับแผนกต่างๆ */
        .custom-table .category-hearing {
            background-color: #cfe2ff !important;
            /* สีฟ้าอ่อน */
        }

        .custom-table .category-speech {
            background-color: #f7b7b2 !important;
            /* สีชมพูอ่อน */
        }

        .custom-table .category-nasal {
            background-color: #d1e7dd !important;
            /* สีเขียวอ่อน */
        }

        .custom-table .category {
            background-color: #f4f4f4 !important;
            font-weight: bold !important;
            border-right: 2px solid #333 !important;
        }

        .custom-table .custom-margin {
            margin-bottom: auto !important;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title" style="color: black;">ตารางแพทย์ออกตรวจ</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="add_schedule.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-edit"></i> จัดการหน้าตารางเวร</a>
                    </div>
                </div>
                <div class="container mt-5 custom-margin">
                    <h2 class="text-center">ตารางแพทย์ออกตรวจโสต ศอ นาสิกกรรม</h2>
                    <p class="text-center text-success">เวลาทำการ: จันทร์ - ศุกร์ เวลา 16:30 - 19:30 | เสาร์ - อาทิตย์ เวลา 09:00 - 12:00</p>
                    <p class="text-center text-muted">สถานที่: ตึกเฉลิมพระเกียรติฯ ชั้น 6</p>
                    <?php if (!empty($schedule)): ?>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>แผนก</th>
                                        <?php foreach ($days as $day) {
                                            echo "<th>$day</th>";
                                        } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($schedule as $category => $data) {
                                        $category_class = "";
                                        if ($category == "โสต ศอ นาสิกกรรม") {
                                            $category_class = "category-nasal";
                                        } elseif ($category == "ตรวจการได้ยิน") {
                                            $category_class = "category-hearing";
                                        } elseif ($category == "ตรวจการฝึกพูด") {
                                            $category_class = "category-speech";
                                        } else {
                                            $category_class = "category";
                                        }
                                    ?>
                                        <tr>
                                            <td class="<?= $category_class; ?>"><?= $category; ?></td>
                                            <?php foreach ($days as $day) { ?>
                                                <td>
                                                    <?php
                                                    if (!empty($data[$day])) {
                                                        echo implode("<hr style='border: 1px solid black; margin: 5px 0;'>", $data[$day]);
                                                    } else {
                                                        echo "-";
                                                    }
                                                    ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">ยังไม่มีข้อมูลการนัดหมายในตารางแพทย์ออกตรวจ</p>
                    <?php endif; ?>

                </div>








            </div>
        </div>
    </div>



</body>

</html>