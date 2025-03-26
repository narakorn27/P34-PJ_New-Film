<?php
include 'mid_string.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>ให้ความรู้หลังการรักษา</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <style>
        .header {
            background-color: rgb(0, 158, 251);
            box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 1px;
        }

        .settings {
            margin-top: 10px;
            padding: 20px;
            text-align: center;
        }

        .color-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            margin: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .logout-btn {
            margin-top: 20px;
        }
    </style>

</head>

<body>
    <div class="main-wrapper">

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">ตั้งค่าระบบ</h4>
                    </div>
                </div>


                <div class="container settings">
                    <h4>เลือกสีส่วนหัวของเว็บไซต์</h4>
                    <div class="d-flex justify-content-center flex-wrap">
                        <button class="color-btn" style="background-color: #009efb;" data-color="#009efb"></button>
                        <button class="color-btn" style="background-color: #ff5733;" data-color="#ff5733"></button>
                        <button class="color-btn" style="background-color: #0d4e13;" data-color="#0d4e13"></button>
                        <button class="color-btn" style="background-color: #ffc107;" data-color="#ffc107"></button>
                        <button class="color-btn" style="background-color: #6f42c1;" data-color="#6f42c1"></button>
                        <button class="color-btn" style="background-color: #dc3545;" data-color="#dc3545"></button>
                    </div>


                    <div class="container settings">
                        <h4 class="mt-4">เลือก ไอคอนเว็บไซต์</h4>
                        <!-- Card สำหรับการเลือก Favicon -->
                        <div class="card shadow-sm p-4 mb-4">
                            <div class="d-flex justify-content-center">
                                <div class="text-center">
                                    <label for="faviconUpload" class="btn btn-primary btn-lg">
                                        อัปโหลดไฟล์ไอคอน <i class="bi bi-image"></i>
                                    </label>
                                    <input type="file" id="faviconUpload" accept="image/png, image/x-icon" class="d-none">
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <p class="text-muted">แนะนำขนาด 16x16 หรือ 32x32 px</p>
                            </div>
                        </div>

                        <!-- ปุ่มออกจากระบบ -->
                        <button class="btn btn-primary logout-btn btn-lg w-100" id="logout">คืนค่าเริ่มต้น</button>
                    </div>




                </div>
            </div>
        </div>
    </div>





    <script>
        // ฟังก์ชั่นเปลี่ยน favicon
        function changeFavicon(url) {
            const link = document.getElementById("favicon");
            link.href = url;
        }

        // ฟังก์ชั่นเปลี่ยนสี header
        function changeHeaderColor(color) {
            const header = document.getElementById("header");
            if (header) {
                header.style.backgroundColor = color;
            }
        }

        // โหลด favicon และสี header ที่บันทึกไว้
        document.addEventListener("DOMContentLoaded", function() {
            const savedFavicon = localStorage.getItem("favicon");
            if (savedFavicon) {
                changeFavicon(savedFavicon);
            }

            const savedColor = localStorage.getItem("headerColor");
            if (savedColor) {
                changeHeaderColor(savedColor);
            }
        });

        // อัปโหลด Favicon
        const faviconInput = document.getElementById("faviconUpload");
        faviconInput.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const faviconURL = e.target.result;

                    // อัปเดต favicon
                    changeFavicon(faviconURL);

                    // บันทึก URL ของ favicon ใน localStorage
                    localStorage.setItem("favicon", faviconURL);
                };
                reader.readAsDataURL(file); // อ่านไฟล์และแปลงเป็น URL
            }
        });

        // เปลี่ยนสี header
        const colorButtons = document.querySelectorAll(".color-btn");
        colorButtons.forEach(button => {
            button.addEventListener("click", function() {
                const selectedColor = this.getAttribute("data-color");
                changeHeaderColor(selectedColor);

                // บันทึกสีของ header ใน localStorage
                localStorage.setItem("headerColor", selectedColor);
            });
        });

        // ออกจากระบบ (ล้างค่า)
        const logoutBtn = document.getElementById("logout");
        logoutBtn.addEventListener("click", function() {
            localStorage.removeItem("favicon");
            localStorage.removeItem("headerColor");
            location.reload();
        });
    </script>
</body>