<?php
include 'mid_string.php'
?>


<head>
    <link rel="stylesheet" href="assets/css/style_contact.css" />

</head>

<body>

    <!-- Carousel -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/img/info_1.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/img/info_2.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/img/info_3.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/img/info_4.png" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Content -->
    <div class="container mt-5 d-flex justify-content-center align-items-center">
        <div class="row text-center w-100">
            <div class="col-12 col-sm-6 col-md-4 mx-auto">
                <i class="fas fa-user-nurse fa-3x" style="color: #2f70d9;"></i>
                <h3 class="mt-3">ยินดีต้อนรับ</h3>
                <p>เราบริการด้วยหัวใจเพื่อความวางใจ</p>
                <a href="about-history.php" class="btn btn-outline" style="color: #3020b9; border-color: #2f70d9;"
                    onmouseover="this.style.backgroundColor='#1e50a2'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2f70d9';">
                    ประวัติความเป็นมา >
                </a>
            </div>
        </div>
    </div>

    <!-- Google Map -->
    <div class="container mt-4 mb-5">
        <h3 class="text-center">แผนที่โรงพยาบาล</h3>

        <!-- แผนที่ -->
        <div class="map-container custom-mb">
            <iframe
                width="100%"
                height="300"
                style="border:0;"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.6031667317886!2d100.534366!3d13.76773!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29f248bfef4a5%3A0x52e3b19f9d8f4e48!2z4LiV4Li24LiB4Liy4LiZ4LmJ4LmC4Lit4LiH4Liy4LiV4Lij4LmA4Lit4Lil4Lil4LmJ4Liy4LiH!5e0!3m2!1sth!2sth!4v1707672939291!5m2!1sth!2sth">
            </iframe>
        </div>


    </div>




    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>



</body>