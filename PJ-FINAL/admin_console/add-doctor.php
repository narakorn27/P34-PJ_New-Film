<?php include 'mid_string.php'; ?> <!---- Include File แจ๋วจัด --->



<head>
    <title>Add Doctor</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.8/dist/sweetalert2.min.css" rel="stylesheet">


    <!-- <link rel="stylesheet" href="./assets/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">

    <!-- <style>
        .text-danger {
            color: red !important;
            font-weight: bold;
            display: inline;
        }
    </style> -->

    <style>
        /* เพิ่มเครื่องหมายดอกจันสีแดงสำหรับฟิลด์ที่จำเป็น */
        .required::after {
            content: " *";
            color: red !important;
        }
    </style>

</head>

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h4 class="page-title" data-lang="en-title" data-lang-en="Add Medical Personnel" data-lang-th="เพิ่มบุคคลากรทางการแพทย์">เพิ่มบุคคลากรทางการแพทย์</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">

                <form id="patient-form" method="POST" enctype="multipart/form-data" onsubmit="submitForm(event)">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">ชื่อจริง</label>
                                <input class="form-control" type="text" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">นามสกุล</label>
                                <input class="form-control" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">ชื่อผู้ใช้</label>
                                <input class="form-control" type="text" name="username" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">อีเมล</label>
                                <input class="form-control" type="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">รหัสผ่าน</label>
                                <input class="form-control" type="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">ยืนยันรหัสผ่าน</label>
                                <input class="form-control" type="password" name="confirm_password">
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
                                <label class="form-label required">เพศ</label>
                                <select class="form-control" name="gender">
                                    <option value="male">ชาย</option>
                                    <option value="female">หญิง</option>
                                    <option value="other">อื่น ๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">สัญชาติ</label>
                                <select class="form-control" name="nationality">
                                    <option value="thai">ไทย</option>
                                    <option value="english">อังกฤษ</option>
                                    <option value="other">อื่น ๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>ที่อยู่</label>
                                <input type="text" class="form-control" name="address">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>จังหวัด</label>
                                <select class="form-control" id="city" name="city">
                                    <option value="">เลือกจังหวัด</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>อำเภอ</label>
                                <select class="form-control" id="district" name="district" disabled>
                                    <option value="">เลือกอำเภอ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>ตำบล</label>
                                <select class="form-control" id="sub_district" name="sub_district" disabled>
                                    <option value="">เลือกตำบล</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>รหัสไปรษณีย์</label>
                                <select class="form-control" id="postal_code" name="postal_code" disabled>
                                    <option value="">เลือกรหัสไปรษณีย์</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">โทรศัพท์</label>
                                <input class="form-control" type="text" name="phone_number">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label required">บทบาท</label>
                                <select class="form-control" name="role" id="role" required onchange="generateID()">
                                    <option value="doctor">แพทย์</option>
                                    <option value="nurse">พยาบาล</option>
                                    <option value="admin">ผู้ดูแลระบบ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select class="form-control" name="status">
                                    <option value="active">ใช้งาน</option>
                                    <option value="inactive">ไม่ใช้งาน</option>
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



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function submitForm(event) {
        event.preventDefault(); // ป้องกันการโหลดหน้าใหม่

        let form = document.getElementById("patient-form");
        let formData = new FormData(form);

        fetch("./config/db-medical-staff.php", { // ส่งข้อมูลไปที่ PHP
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    title: "สำเร็จ!",
                    text: "สร้างข้อมูลบุคลากรสำเร็จ",
                    icon: "success",
                    confirmButtonText: "ตกลง"
                }).then(() => {
                    form.reset(); // เคลียร์ฟอร์ม
                });
            } else {
                Swal.fire({
                    title: "เกิดข้อผิดพลาด!",
                    text: data.message,
                    icon: "error",
                    confirmButtonText: "ลองอีกครั้ง"
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: "ข้อผิดพลาด!",
                text: "ไม่สามารถส่งข้อมูลได้ กรุณาลองใหม่",
                icon: "error",
                confirmButtonText: "ตกลง"
            });
        });
    }
</script>



<!----------------------------------------- ดึงไฟล์ Json มาแสดงโดยมีข้อมูล ตำบล อำเภอ จังหวัด และรหัสไปรษณีย์ของจังหวัดนั้นๆ เรียกตามลำดับ ----------------------------------------------------------->
<script>
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
            dropdown.innerHTML = '<option value="">' + dropdown.querySelector('option').textContent + '</option>';

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

<!----------------------------------------- ดึงไฟล์ Json มาแสดงโดยมีข้อมูล ตำบล อำเภอ จังหวัด และรหัสไปรษณีย์ของจังหวัดนั้นๆ เรียกตามลำดับ ----------------------------------------------------------->