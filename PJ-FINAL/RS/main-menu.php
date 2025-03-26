<?php
include 'mid_string.php'
?>


<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="assets/css/style_main_menu.css" />

    <title>เมนูหลัก</title>


</head>


<body>

    <main>

        <!-- Carousel -->
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/img/bb1.png" class="d-block w-100" alt="hospital 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/img/banner2.png" class="d-block w-100" alt="hospital 2">
                </div>
                <div class="carousel-item">
                    <img src="assets/img/banner3.png" class="d-block w-100" alt="hospital 3">
                </div>
                <div class="carousel-item">
                    <img src="assets/img/pn-3.jpg" class="d-block w-100" alt="hospital 3">
                </div>
            </div>
        </div>



        <div class="container-fluid mt-3">
            <div class="card bg-white p-3 rounded shadow-sm" style="margin-bottom: 5rem;">
                <!-- ส่วนติดต่อแพทย์/พยาบาล -->
                <div class="head-first text-center">
                    <h3>ติดต่อแพทย์/พยาบาล</h3>
                </div>
                <div class="d-flex flex-wrap justify-content-center text-center gap-3">
                    <div class="content-checklist">
                        <a href="medical-schedule.php">
                            <img src="assets/icons/calendar-add-event-button-with-plus-sign-svgrepo-com.svg" alt="ตารางแพทย์ออกตรวจ" class="icon-treatment">
                            <p>ตารางแพทย์ออกตรวจ</p>
                    </div>
                    <div class="content-checklist">
                        <a href="recovery_guide.php">
                            <img src="assets/icons/hand-holding-heart-svgrepo-com.svg" alt="สอบถามหลังการรักษา" class="icon-treatment">
                            <p>สอบถามหลังการรักษา</p>
                        </a>
                    </div>

                </div>

                <!-- เส้นคั่น -->
                <hr>

                <!-- ส่วนทั่วไป -->
                <div class="head-first text-center">
                    <h3>ทั่วไป</h3>
                </div>
                <div class="d-flex justify-content-center text-center">
                    <div class="content-checklist">
                        <a href="calendar.php">
                            <img src="assets/icons/calendar-date-heart-svgrepo-com.svg" alt="การนัดหมาย" class="icon-treatment">
                            <p>การนัดหมาย</p>
                        </a>
                    </div>
                    <div class="content-checklist">
                        <a href="medical_history.php">
                            <img src="assets/icons/medical-history-icon.svg" alt="ประวัติสุขภาพ" class="icon-treatment">
                            <p>ประวัติสุขภาพ</p>
                    </div>
                </div>
            </div>
        </div>





        <!-- ข้อมูลการรักษา -->
        <!-- <div class="head-first">
            <h3>ข้อมูลการรักษา</h3>
        </div>
        <div class="content-form">
            <div class="icon-sys">
                <div class="content-checklist">
                    <img src="assets/icons/medical-history-icon.svg" alt="ประวัติการรักษา" class="icon-treatment">
                    <p>ประวัติการรักษา</p>
                </div>
                <div class="content-checklist">
                    <img src="assets/icons/icons8-drug-allergy-64.png" alt="ประวัติการแพ้ยา" class="icon-treatment">
                    <p>ประวัติการแพ้ยา</p>
                </div>
            </div>
        </div>

        <hr class="divider"> -->







        <!--=============== MAIN JS ===============-->
        <script src="assets/js/main.js"></script>

        <!-- เชื่อมโยง Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

</body>

</html>