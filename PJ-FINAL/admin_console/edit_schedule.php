<?php
include 'mid_string.php';


// ดึงข้อมูลตารางเวรแพทย์
$sql = "SELECT ms.id, ms.doctor_id, ms.work_day, ms.category, d.first_name, d.last_name
        FROM medical_schedule ms
        JOIN medical_staff d ON ms.doctor_id = d.id
        ORDER BY d.first_name";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขตารางเวรแพทย์</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./css/style_index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="container mt-5">
                    <h2 class="text-center">แก้ไขตารางเวรแพทย์</h2>

                    <form method="POST" action="">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">เลือก</th>
                                    <th>ชื่อแพทย์</th>
                                    <th>วันทำงาน</th>
                                    <th>ประเภทการตรวจ</th>
                                    <th style="text-align: center;">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="selected_id" value="<?= $row['id']; ?>" required>
                                            <input type="hidden" name="doctor_id_<?= $row['id']; ?>" value="<?= $row['doctor_id']; ?>">
                                        </td>
                                        <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                                        <td><?= htmlspecialchars($row['work_day']); ?></td>
                                        <td><?= htmlspecialchars($row['category']); ?></td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id']; ?>)">ลบ</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="mb-3">
                            <label>เลือกวันทำงานใหม่:</label><br>
                            <?php
                            $days = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
                            foreach ($days as $day): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="work_day" value="<?= $day; ?>" required>
                                    <label class="form-check-label"><?= $day; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label>เลือกประเภทการตรวจใหม่:</label>
                            <select name="category" class="form-control" required>
                                <option value="โสต ศอ นาสิกกรรม">โสต ศอ นาสิกกรรม</option>
                                <option value="ตรวจการได้ยิน">ตรวจการได้ยิน</option>
                                <option value="ตรวจการฝึกพูด">ตรวจการฝึกพูด</option>
                            </select>
                        </div>

                        <button type="button" class="btn btn-success" onclick="updateSchedule()">อัปเดตข้อมูล</button>
                        <a href="medical-schedule.php" class="btn btn-primary">กลับไปหน้าตารางเวร</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "คุณแน่ใจหรือไม่?",
                text: "เมื่อลบแล้วจะไม่สามารถกู้คืนได้!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ใช่, ลบเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './config/delete_schedule.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "ลบสำเร็จ!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "ตกลง"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("เกิดข้อผิดพลาด!", response.message, "error");
                            }
                        },
                        error: function() {
                            Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้", "error");
                        }
                    });
                }
            });
        }

        function updateSchedule() {
            let selectedId = $("input[name='selected_id']:checked").val();
            let workDay = $("input[name='work_day']:checked").val();
            let category = $("select[name='category']").val();

            if (!selectedId) {
                Swal.fire("แจ้งเตือน", "กรุณาเลือกแพทย์ที่ต้องการอัปเดต", "warning");
                return;
            }
            if (!workDay) {
                Swal.fire("แจ้งเตือน", "กรุณาเลือกวันทำงานที่ต้องการอัปเดต", "warning");
                return;
            }
            if (!category) {
                Swal.fire("แจ้งเตือน", "กรุณาเลือกประเภทการตรวจที่ต้องการอัปเดต", "warning");
                return;
            }

            $.ajax({
                url: './config/update_schedule.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: selectedId,
                    work_day: workDay,
                    category: category
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "อัปเดตสำเร็จ!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "ตกลง"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("เกิดข้อผิดพลาด!", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถอัปเดตข้อมูลได้", "error");
                }
            });
        }

        $("input[name='selected_id']").change(function() {
            if ($("input[name='selected_id']:checked").length > 1) {
                $("input[name='selected_id']:checked").not(this).prop("checked", false);
            }
        });
    </script>
</body>

</html>