<?php include 'mid_string.php'; ?>



<?php
// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = $_GET['id']; // รับค่า id จาก query string

    // ดึงข้อมูลจากตาราง medical_staff
    $stmt = $conn->prepare("SELECT * FROM medical_staff WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} else {
    die("No user ID specified.");
}


$roleText = '';

switch ($user['role']) {
    case 'admin':
        $roleText = 'ผู้ดูแลระบบ';
        break;
    case 'doctor':
        $roleText = 'หมอ';
        break;
    case 'nurse':
        $roleText = 'พยาบาล';
        break;
    default:
        $roleText = 'ไม่ทราบตำแหน่ง'; // กรณี role ไม่ตรงกับที่กำหนด
}


// ตั้งค่าชื่อเดือนภาษาไทย
$thaiMonths = [
    "01" => "มกราคม",
    "02" => "กุมภาพันธ์",
    "03" => "มีนาคม",
    "04" => "เมษายน",
    "05" => "พฤษภาคม",
    "06" => "มิถุนายน",
    "07" => "กรกฎาคม",
    "08" => "สิงหาคม",
    "09" => "กันยายน",
    "10" => "ตุลาคม",
    "11" => "พฤศจิกายน",
    "12" => "ธันวาคม"
];

// ดึงและแปลงวันที่จากฐานข้อมูล
$dob = new DateTime($user['date_of_birth']);
$day = $dob->format("j"); // วันที่ (1-31)
$month = $thaiMonths[$dob->format("m")]; // แปลงเดือนเป็นภาษาไทย
$year = $dob->format("Y") + 543; // แปลง ค.ศ. เป็น พ.ศ.

$birthdayText = "$day $month $year";

?>
<!DOCTYPE html>
<html lang="th">


<head>
    <title>Doctors Console</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">
</head>

<body>
    <div class="main-wrapper">

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">โปรไฟล์ของฉัน</h4>
                    </div>

                    <div class="col-sm-5 col-6 text-right m-b-30">
                        <a href="edit-doctor.php?id=<?php echo $_GET['id']; ?>" class="btn btn-primary btn-rounded">
                            <i class="fa fa-plus"></i> แก้ไขข้อมูลส่วนตัว
                        </a>
                    </div>

                </div>


                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <a href="#">
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img class="avatar" src="data:image/jpeg;base64,<?= base64_encode($user['avatar']) ?>" alt="Profile Image">
                                            <?php else: ?>
                                                <img class="avatar" src="./assets/img/user.jpg" alt="Default Profile Image">
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                                                <small class="text-muted"><?= htmlspecialchars($roleText) ?></small>
                                                <div class="staff-id">รหัสพนักงาน ID : <?= htmlspecialchars($user['id']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">เบอร์โทร:</span>
                                                    <span class="text"><a href="#"><?= htmlspecialchars($user['phone_number']) ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">อีเมล:</span>
                                                    <span class="text"><a href="#"><?= htmlspecialchars($user['email']) ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">วันเดือนปีเกิด:</span>
                                                    <span class="text"><?= htmlspecialchars($birthdayText, ENT_QUOTES, 'UTF-8') ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">ที่อยู่:</span>
                                                    <span class="text">
                                                        <?= htmlspecialchars($user['address'] . ', ' . $user['city'] . ', ' . $user['district'] . ', ' . $user['postal_code']) ?>
                                                    </span>
                                                </li>
                                                <li>
                                                    <span class="title">เพศ:</span>
                                                    <span class="text"><?= htmlspecialchars(ucfirst($user['gender'])) ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>







            </div>
        </div>
    </div>


</body>


<!-- profile23:03-->

</html>