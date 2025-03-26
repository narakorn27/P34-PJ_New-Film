<?php
include 'mid_string.php';

// ตรวจสอบว่า user_id มาจาก session หรือจาก URL
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} elseif (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    die("Error: User ID is not set.");
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT pi.first_name, pi.last_name, pi.address, pi.city, pi.district, pi.sub_district, pi.postal_code, pi.phone_number, pl.profile_picture
        FROM patients_info pi
        JOIN patients_login pl ON pi.id = pl.id
        WHERE pi.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ตั้งค่าภาพเริ่มต้นหากยังไม่มีการอัปโหลด
// แสดงรูปภาพโปรไฟล์
if (!empty($user['profile_picture'])) {
    // แปลงข้อมูล BLOB ในฐานข้อมูลเป็นรูปภาพ
    $profile_image = "data:image/jpeg;base64," . base64_encode($user['profile_picture']);
} else {
    // ถ้าไม่มีข้อมูล ให้ใช้รูปภาพเริ่มต้น
    $profile_image = "assets/img/user.jpg?" . time(); // เพิ่ม timestamp เพื่อไม่ให้ cache
}
?>


<head>
    <title>แก้ไขข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="assets/css/styles_edit_profile.css" />
</head>

<body>


    <div class="appointment-card ">
        <h4 style="text-align:center;">แก้ไขข้อมูลส่วนตัว</h4>
        <form action="./config/update_profile.php" method="POST" enctype="multipart/form-data">
            <!-- กำหนด user_id ในฟอร์มเพื่อส่งไปหน้า update_profile.php -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- ส่วนอัปโหลดรูปภาพ -->
            <div class="profile-image-container" style="text-align: center; margin-bottom: 20px;">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" id="profilePreview" class="profile-image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; margin-bottom: 1rem; cursor: pointer;" onclick="previewImageClick()">
                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <br>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('profile_image').click();">เปลี่ยนรูป</button>
            </div>

            <!-- พรีวิวรูปภาพ -->
            <div id="imagePreviewModal" class="image-preview-modal" style="display: none;" onclick="closeModal(event)">
                <img id="modalImage" src="" alt="Image Preview" style="width: 100%; max-width: 600px;">
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="first_name" name="first_name"
                    value="<?php echo htmlspecialchars($user['first_name']); ?>"
                    pattern="^[ก-๙a-zA-Z]+$" title="กรุณากรอกเฉพาะตัวอักษร (ภาษาไทยหรืออังกฤษ)" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">นามสกุล</label>
                <input type="text" class="form-control" id="last_name" name="last_name"
                    value="<?php echo htmlspecialchars($user['last_name']); ?>"
                    pattern="^[ก-๙a-zA-Z]+$" title="กรุณากรอกเฉพาะตัวอักษร (ภาษาไทยหรืออังกฤษ)" required>
            </div>


            <div class="mb-3">
                <label for="phone_number" class="form-label">เบอร์โทร</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                    pattern="[0-9]{10}" maxlength="10" required
                    title="กรุณากรอกเบอร์โทร 10 หลัก">
            </div>



            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="district" class="form-label">อำเภอ</label>
                <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($user['district']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="sub_district" class="form-label">ตำบล</label>
                <input type="text" class="form-control" id="sub_district" name="sub_district" value="<?php echo htmlspecialchars($user['sub_district']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">จังหวัด</label>
                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>" required>
            </div>
            <div style="width: 100%; text-align: center; margin-bottom: 3rem;">
                <button type="submit" id="submitBtn" class="btn btn-primary" style="width: 50%;">บันทึก</button>
            </div>

        </form>

    </div>


    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




    <script>
        // ฟังก์ชันพรีวิวภาพเมื่อคลิกที่รูป
        function previewImageClick() {
            const img = document.getElementById("profilePreview");
            const modal = document.getElementById("imagePreviewModal");
            const modalImg = document.getElementById("modalImage");

            modal.style.display = "flex"; // ใช้ 'flex' แทน 'block' เพื่อให้สามารถใช้การจัดแนวแบบ flex ได้
            modalImg.src = img.src;
        }

        // ฟังก์ชันปิดพรีวิว
        function closeModal(event) {
            // เช็คว่าไม่ได้คลิกที่ภาพเพื่อหลีกเลี่ยงการปิดเมื่อคลิกที่ภาพ
            if (event.target.id === "imagePreviewModal") {
                document.getElementById("imagePreviewModal").style.display = "none";
            }
        }

        // ฟังก์ชันพรีวิวภาพที่เลือกจาก input
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById("profilePreview");
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>


    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            // เริ่มต้นการป้องกันฟอร์มจากการส่งก่อน
            event.preventDefault();

            // แสดง SweetAlert ก่อนส่งฟอร์ม
            Swal.fire({
                title: 'คุณแน่ใจไหม?',
                text: "ต้องการบันทึกข้อมูลนี้หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลผ่าน AJAX
                    let formData = new FormData(this); // สร้าง FormData จากฟอร์ม
                    fetch('./config/update_profile.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    window.location.reload(); // รีเฟรชหน้า
                                });
                            } else {
                                Swal.fire({
                                    title: 'ข้อผิดพลาด!',
                                    text: data.message || 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล!',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'ข้อผิดพลาด!',
                                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        });
                }
            });
        });
    </script>


    <!-- ตรวจสอบประเภทไฟล์ก่อนอัปโหลด -->
    <script>
        document.getElementById('profile_image').addEventListener('change', function(event) {
            const file = event.target.files[0]; // เลือกไฟล์จาก input
            const fileType = file.type; // ตรวจสอบประเภทไฟล์

            // กำหนดประเภทไฟล์ที่อนุญาต
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (!allowedTypes.includes(fileType)) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ไม่ถูกต้อง!',
                    text: 'กรุณาอัปโหลดไฟล์ภาพเท่านั้น (.jpg, .jpeg, .png)',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ตกลง'
                });
                event.target.value = ''; // เคลียร์ไฟล์ที่เลือกไป
            }
        });
    </script>



</body>