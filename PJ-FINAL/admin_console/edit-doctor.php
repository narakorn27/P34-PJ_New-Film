<?php include 'mid_string.php'; ?>

<?php
require_once './config/db.php';

// รับค่า id จาก URL
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // เชื่อมต่อฐานข้อมูลด้วย PDO
        $stmt = $conn->prepare("SELECT * FROM medical_staff WHERE id = ?");
        $stmt->execute([$id]);
        $staff = $stmt->fetch();

        if (!$staff) {
            echo "หมอที่ต้องการแก้ไขไม่พบ";
            exit;
        }
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
        exit;
    }
} else {
    echo "ID ไม่ถูกต้อง";
    exit;
}
?>


<!-- ส่วนของ head -->

<head>
    <title>แก้ไขข้อมูลบุคลากร</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">

    <!-- Font Awesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


</head>


<body>
    <div class="main-wrapper">


        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">แก้ไขข้อมูล</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form action="./config/update_staff.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($staff['id']) ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>ชื่อจริง <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" value="<?= htmlspecialchars($staff['first_name']) ?>" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>นามสกุล <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" value="<?= htmlspecialchars($staff['last_name']) ?>" name="last_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>ชื่อผู้ใช้งาน <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" value="<?= htmlspecialchars($staff['username']) ?>" name="username" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>อีเมล <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" value="<?= htmlspecialchars($staff['email']) ?>" name="email" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>รหัสผ่าน <span class="text-danger">*</span> </label>
                                        <input class="form-control" type="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>ยืนยันรหัสผ่าน <span class="text-danger">*</span> </label>
                                        <input class="form-control" type="password" name="confirm_password" required>
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>วันเกิด</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="date_of_birth" value="<?= date('d/m/Y', strtotime($staff['date_of_birth'])) ?>" required />
                                        </div>
                                    </div>
                                </div>




                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>เพศ:</label>
                                        <select class="form-control" name="gender">
                                            <option value="Male" <?= $staff['gender'] == 'Male' ? 'selected' : '' ?>>ชาย</option>
                                            <option value="Female" <?= $staff['gender'] == 'Female' ? 'selected' : '' ?>>หญิง</option>
                                            <option value="Other" <?= $staff['gender'] == 'Other' ? 'selected' : '' ?>>อื่นๆ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>ที่อยู่</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['address']) ?>" name="address" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>จังหวัด</label>
                                        <select class="form-control" id="city" name="city" required>
                                            <option value="">เลือกจังหวัด</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>อำเภอ</label>
                                        <select class="form-control" id="district" name="district" disabled required>
                                            <option value="">เลือกอำเภอ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>ตำบล</label>
                                        <select class="form-control" id="sub_district" name="sub_district" disabled required>
                                            <option value="">เลือกตำบล</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>รหัสไปรษณีย์</label>
                                        <select class="form-control" id="postal_code" name="postal_code" disabled required>
                                            <option value="">เลือกรหัสไปรษณีย์</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>เบอร์โทรศัพท์</label>
                                        <input class="form-control" type="text" value="<?= htmlspecialchars($staff['phone_number']) ?>" name="phone_number" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>รูปภาพของคุณ :</label>
                                        <div class="profile-upload">
                                            <div class="upload-img">
                                                <a class="avatar">
                                                    <?php
                                                    if ($staff['avatar']) {
                                                        echo '<img alt="Avatar" src="data:image/jpeg;base64,' . base64_encode($staff['avatar']) . '" />';
                                                    } else {
                                                        echo '<img alt="Avatar" src="./assets/img/user.jpg" />';
                                                    }
                                                    ?>
                                                </a>
                                            </div>
                                            <div class="upload-input">
                                                <input type="file" class="form-control" name="avatar">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>บทบาทของผู้ใช้งาน</label>
                                            <select class="form-control select" name="role">
                                                <option value="doctor" <?= $staff['role'] == 'doctor' ? 'selected' : '' ?>>แพทย์</option>
                                                <option value="nurse" <?= $staff['role'] == 'nurse' ? 'selected' : '' ?>>พยาบาล</option>
                                                <option value="admin" <?= $staff['role'] == 'admin' ? 'selected' : '' ?>>ผู้ดูแลระบบ</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>สถานะ</label>
                                        <select class="form-control" name="status">
                                            <option value="active" <?php echo (isset($staff['status']) && $staff['status'] === 'active') ? 'selected' : ''; ?>>ใช้งาน</option>
                                            <option value="inactive" <?php echo (isset($staff['status']) && $staff['status'] === 'inactive') ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>



            </div>
        </div>



    </div>




    <script>
        // Function to fetch JSON data and return the matched item
        function getNameById(data, id, idKey, nameKey) {
            let item = data.find(item => item[idKey] === id);
            return item ? item[nameKey] : '';
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Function to fetch JSON data
            function fetchJSON(url) {
                return fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error fetching JSON:', error);
                        throw error;
                    });
            }

            // Function to populate dropdown
            function populateDropdown(data, dropdownId, valueKey, textKey) {
                var dropdown = document.getElementById(dropdownId);
                if (!dropdown) return;

                // Clear existing options
                // dropdown.innerHTML = '<option value="">' + dropdown.querySelector('option').textContent + '</option>';
                dropdown.innerHTML = '<option value="">' + (dropdown.querySelector('option') ? dropdown.querySelector('option').textContent : 'เลือก') + '</option>';

                // Add new options
                data.forEach(item => {
                    var option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    dropdown.appendChild(option);
                });
            }

            // Fetch provinces and populate city dropdown
            fetchJSON('./js/provinces.json')
                .then(data => {
                    console.log('Provinces data:', data);
                    populateDropdown(data, 'city', 'PROVINCE_ID', 'PROVINCE_NAME');
                })
                .catch(error => {
                    console.error('Error loading provinces:', error);
                });

            // Fetch districts when city dropdown changes
            document.getElementById('city').addEventListener('change', function() {
                var selectedProvinceId = this.value;
                if (!selectedProvinceId) {
                    document.getElementById('district').innerHTML = '<option value="">Select District</option>';
                    document.getElementById('district').disabled = true;
                    return;
                }
                fetchJSON('./js/districts.json')
                    .then(data => {
                        console.log('Districts data:', data);
                        var filteredData = data.filter(item => item.PROVINCE_ID === parseInt(selectedProvinceId));
                        populateDropdown(filteredData, 'district', 'DISTRICT_ID', 'DISTRICT_NAME');
                        document.getElementById('district').disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                    });
            });

            // Fetch sub-districts when district dropdown changes
            document.getElementById('district').addEventListener('change', function() {
                var selectedDistrictId = this.value;
                if (!selectedDistrictId) {
                    document.getElementById('sub_district').innerHTML = '<option value="">Select Sub-District</option>';
                    document.getElementById('sub_district').disabled = true;
                    return;
                }
                fetchJSON('./js/sub_districts.json')
                    .then(data => {
                        console.log('Sub-Districts data:', data);
                        var filteredData = data.filter(item => item.DISTRICT_ID === parseInt(selectedDistrictId));
                        populateDropdown(filteredData, 'sub_district', 'SUB_DISTRICT_ID', 'SUB_DISTRICT_NAME');
                        document.getElementById('sub_district').disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading sub-districts:', error);
                    });
            });

            // Fetch postal codes when sub-district dropdown changes
            document.getElementById('sub_district').addEventListener('change', function() {
                var selectedSubDistrictId = this.value;
                if (!selectedSubDistrictId) {
                    document.getElementById('postal_code').innerHTML = '<option value="">Select Postal Code</option>';
                    document.getElementById('postal_code').disabled = true;
                    return;
                }
                fetchJSON('./js/postal_codes.json')
                    .then(data => {
                        console.log('Postal codes data:', data);
                        var filteredData = data.filter(item => item.SUB_DISTRICT_ID === selectedSubDistrictId);
                        populateDropdown(filteredData, 'postal_code', 'POSTAL_ID', 'ZIP_CODE');
                        document.getElementById('postal_code').disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading postal codes:', error);
                    });
            });
        });
    </script>





    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            if ($('.datetimepicker').length) {
                moment.locale('th');
                $('.datetimepicker').datetimepicker({
                    format: 'DD/MM/YYYY',
                    locale: 'th'
                });
            }
        });
    </script>




    <script>
        // ตรวจสอบ URL สำหรับการแจ้งเตือน
        <?php if (isset($_GET['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'ข้อมูลถูกบันทึกสำเร็จ!',
            });
        <?php elseif (isset($_GET['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล!',
            });
        <?php endif; ?>
    </script>



</body>