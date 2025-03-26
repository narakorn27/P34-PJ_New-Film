<?php
include 'mid_string.php';

try {
    // ดึงรายชื่อแพทย์จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT id, first_name, last_name FROM medical_staff WHERE role = 'doctor'");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// ถ้ากดปุ่มบันทึก
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $work_days = $_POST['work_days'] ?? []; // รับค่าหลายวันเป็น array
    $category = $_POST['category'];

    if (!empty($doctor_id) && !empty($work_days) && !empty($category)) {
        $duplicateDays = [];
        $successDays = [];

        foreach ($work_days as $day) {
            // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM medical_schedule WHERE doctor_id = ? AND work_day = ?");
            $stmt_check->execute([$doctor_id, $day]);
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $duplicateDays[] = $day;
                continue;
            }

            // ถ้าไม่มีข้อมูลซ้ำให้ INSERT
            $stmt = $conn->prepare("INSERT INTO medical_schedule (doctor_id, work_day, category) VALUES (?, ?, ?)");
            $stmt->execute([$doctor_id, $day, $category]);
            $successDays[] = $day;
        }

        // แจ้งผลการบันทึกผ่าน SweetAlert2
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        if (empty($successDays)) {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถบันทึกข้อมูลได้',
                    text: 'วัน " . implode(", ", $duplicateDays) . " มีข้อมูลอยู่แล้ว!',
                    confirmButtonText: 'ตกลง'
                }).then(() => window.location.href='add_schedule.php');
            </script>";
        } else {
            $message = "บันทึกข้อมูลสำเร็จ!";
            if (!empty($duplicateDays)) {
                $message .= " แต่วัน " . implode(", ", $duplicateDays) . " มีข้อมูลอยู่แล้ว";
            }
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '{$message}',
                    confirmButtonText: 'ตกลง'
                }).then(() => window.location.href='add_schedule.php');
            </script>";
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: 'กรุณาเลือกแพทย์ วันทำงาน และประเภทการตรวจ',
                confirmButtonText: 'ตกลง'
            });
        </script>";
    }
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
    <link rel="stylesheet" href="./css/style_index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>

<body>

    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title" style="color: black;">ตารางแพทย์ออกตรวจ</h4>
                    </div>
                </div>
                <div class="container mt-5">
                    <h2 class="text-center">เพิ่มตารางเวรแพทย์</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">เลือกแพทย์:</label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">-- เลือกแพทย์ --</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id']; ?>"><?= $doctor['first_name'] . " " . $doctor['last_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">เลือกวันทำงาน:</label><br>
                            <?php
                            $days = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
                            foreach ($days as $day): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="work_days[]" value="<?= $day; ?>">
                                    <label class="form-check-label"><?= $day; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">เลือกประเภทการตรวจ:</label>
                            <select name="category" class="form-control" required>
                                <option value="">-- เลือกประเภทการตรวจ --</option>
                                <option value="โสต ศอ นาสิกกรรม">โสต ศอ นาสิกกรรม</option>
                                <option value="ตรวจการได้ยิน">ตรวจการได้ยิน</option>
                                <option value="ตรวจการฝึกพูด">ตรวจการฝึกพูด</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a href="edit_schedule.php" class="btn btn-primary">แก้ไขตารางเวรแพทย์</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
