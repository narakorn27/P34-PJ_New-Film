<?php
session_start();
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style_login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Login & Register</title>
</head>


<body>
    <div class="container">

        <div class="bg-index">
            <img src="img/logo_clinic.jpg" style="max-width:100%;height:auto; margin-top: 10rem; margin-left: 1rem">
        </div>

        <!----------------------------- Form box ----------------------------------->


        <!------------------------------login form -------------------------->
        <div class="login-container" id="login">

            <header style="font-size:300%; margin-bottom: 3rem; color: #ffff; margin-left: -10%">เข้าสู่ระบบ</header>
            <div class="form-box">

                <form action="config/db-login.php" method="post">
                    <?php if (isset($_SESSION['error'])) { ?>
                        <div class="alert1 alert-danger1" role="alert">
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_SESSION['warning'])) { ?>
                        <div class="alert alert-warning" role="alert">
                            <?php
                            echo $_SESSION['warning'];
                            unset($_SESSION['warning']);
                            ?>
                        </div>
                    <?php } ?>

                    <div class="lg-text">
                        <div class="lg1">
                            <span style="font-size:150%;"> ชื่อผู้ใช้งาน (Username)</span>
                            <br>
                            <br>
                            <div class="input-box">
                                <input type="text" name="username" class="input-field" placeholder="ชื่อผู้ใช้งาน">
                                <i class="bx bx-user"></i>
                            </div>
                            <span style="font-size:150%;"> รหัสผ่าน (Password)</span>
                            <br>
                            <br>
                            <div class="input-box" style="position: relative;">
                                <!-- Input field -->
                                <input type="password" id="password" name="password" class="input-field" placeholder="รหัสผ่าน" style="padding-left: 50px; padding-right: 40px;">

                                <!-- Lock icon (left side) -->
                                <i class="bx bx-lock-alt" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%);"></i>

                                <!-- Eye icon (right side) -->
                                <button type="button" id="togglePassword" style="background: none; border: none; position: absolute; right: 30px; top: 125%; transform: translateY(-50%);">
                                    <i class="bx bx-show" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="two-col">
                        <div class="one">
                            <input type="checkbox" id="login-check" style="width: 50px;">
                            <label for="login-check"> จดจำฉัน</label>
                        </div>
                        <div class="two">
                            <label><a href="#">ลืมรหัสผ่าน ?</a></label>
                        </div>
                    </div>

                    <div class="input-box">
                        <input type="submit" name="login_go" class="submit" value="เข้าสู่ระบบ">
                    </div>

                </form>

            </div>





        </div>

    </div>


    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon
            if (type === 'text') {
                eyeIcon.classList.remove('bx-show');
                eyeIcon.classList.add('bx-hide');
            } else {
                eyeIcon.classList.remove('bx-hide');
                eyeIcon.classList.add('bx-show');
            }
        });
    </script>

</body>