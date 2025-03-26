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
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $schedule[$row['category']][$row['work_day']][] = $row['first_name'] . " " . $row['last_name'];
}

$conn->close();
?>



<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- <link rel="stylesheet" href="assets/css/style_main_menu.css" /> -->

    <!-- เพิ่มการโหลด Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <title>เมนูหลัก</title>


    <style>
        main {
            margin-top: 90px;
            margin-bottom: 5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            border-bottom: 2px solid #333;
        }

        td {
            background-color: #fff;
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

    <title>ตารางแพทย์ออกตรวจ</title>
</head>

<body>
    <main>
        <div class="container mt-5 custom-margin">
            <h2 class="text-center">ตารางแพทย์ออกตรวจโสต ศอ นาสิกกรรม</h2>
            <p class="text-center text-success">เวลาทำการ: จันทร์ - ศุกร์ เวลา 16:30 - 19:30 | เสาร์ - อาทิตย์ เวลา 09:00 - 12:00</p>
            <p class="text-center text-muted">สถานที่: ตึกเฉลิมพระเกียรติฯ ชั้น 6</p>
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
        </div>

    </main>

    <!-- โหลด jQuery, Popper.js, และ Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
</body>

</html>