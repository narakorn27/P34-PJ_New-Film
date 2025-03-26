<?php include 'mid_string.php'; ?>


<?php
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ดึงข้อมูลเฉพาะของหน้าปัจจุบัน
$sql = "SELECT * FROM medical_staff WHERE status = 'active' LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// หาจำนวนหน้าทั้งหมด
$total_query = "SELECT COUNT(*) as total FROM medical_staff WHERE status = 'active'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch(PDO::FETCH_ASSOC);
$total_pages = ceil($total_row['total'] / $limit);

$roleMapping = [
    'admin' => 'ผู้ดูแลระบบ',
    'doctor' => 'แพทย์',
    'nurse' => 'พยาบาล'
];

?>



<head>
    <title>รายชื่อบุคลากรภายในโรงบาล</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">

    <style>
        .pagination .page-item .page-link {
            color: #007bff;
            border-radius: 8px;
            margin: 0 5px;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
            pointer-events: none;
        }
    </style>

</head>


<body>


    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-4 col-3">
                    <h4 class="page-title">บุคลากรทางการแพทย์</h4>
                </div>
                <div class="col-sm-8 col-9 text-right m-b-20">
                    <?php if (isset($user['role']) && $user['role'] === 'admin') : ?>
                        <a href="add-doctor.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> เพิ่มบุคลากร</a>
                    <?php endif; ?>
                </div>

            </div>

            <div class="row doctor-grid">
                <!-- รายการหมอจะแสดงที่นี่ -->
                <?php
                foreach ($staffs as $staff) {
                    // ตรวจสอบสถานะว่าต้องเป็น 'active' เท่านั้น
                    if ($staff['status'] === 'active') {
                ?>
                        <div class="col-md-4 col-sm-4 col-lg-3" id="doctor-<?php echo $staff['id']; ?>"> <!-- เพิ่ม id ที่นี่ -->
                            <div class="profile-widget">
                                <div class="doctor-img">
                                    <a class="avatar" href="profile.php?id=<?php echo $staff['id']; ?>">
                                        <?php
                                        if ($staff['avatar']) {
                                            echo '<img alt="" src="data:image/jpeg;base64,' . base64_encode($staff['avatar']) . '" />';
                                        } else {
                                            echo '<img alt="" src="./assets/img/user.jpg" />';
                                        }
                                        ?>
                                    </a>
                                </div>
                                <!-- แสดง dropdown เฉพาะ role admin -->
                                <?php if ($user['role'] === 'admin') : ?>
                                    <div class="dropdown profile-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit-doctor.php?id=<?php echo $staff['id']; ?>"><i class="fa fa-pencil m-r-5"></i> แก้ไข</a>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <!-- อาจเพิ่มข้อความหรือไม่แสดงอะไรเลยสำหรับผู้ที่ไม่มีสิทธิ์ -->
                                <?php endif; ?>
                                <h4 class="doctor-name text-ellipsis">
                                    <a href="profile.php?id=<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['first_name']) . ' ' . htmlspecialchars($staff['last_name']); ?></a>
                                </h4>
                                <div class="doc-prof"></div>
                                <div class="user-country">
                                    <i class="fa fa-user-circle"></i>
                                    <?php echo $roleMapping[$staff['role']] ?? 'ไม่ทราบ'; ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>



            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <!-- ปุ่ม Previous -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- ตัวเลขหน้า -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- ปุ่ม Next -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>


        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // เมื่อคลิกปุ่ม Delete
        document.querySelectorAll('.deleteDoctorBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var doctorId = this.getAttribute('data-id'); // ดึงค่าของ id

                // เรียกใช้ SweetAlert2 เพื่อยืนยันการลบ
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        cancelButton: 'cancel-button-red', // ใช้คลาสสำหรับปุ่ม Cancel
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ใช้ AJAX ส่งคำขอลบ
                        $.ajax({
                            url: 'config/delete_doctor.php', // เส้นทางไปยังไฟล์ลบ
                            type: 'GET',
                            data: {
                                id: doctorId
                            },
                            success: function(response) {
                                console.log(response); // ตรวจสอบค่าผลลัพธ์จาก PHP
                                if (response === 'success') {
                                    // ลบรายการหมอจากหน้า
                                    document.getElementById('doctor-' + doctorId).remove();

                                    // แสดงผลการลบด้วย SweetAlert2
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Your file has been deleted.',
                                        icon: 'success',
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Unable to delete doctor.',
                                        icon: 'error',
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong.',
                                    icon: 'error',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var userRole = '<?php echo $user['role']; ?>'; // รับค่าบทบาทจาก PHP
            if (userRole !== 'admin') {
                document.querySelector('.btn-primary').style.display = 'none';
            }
        });
    </script>



</body>