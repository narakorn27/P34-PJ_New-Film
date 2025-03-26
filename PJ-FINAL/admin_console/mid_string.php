<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: ./employee_login.php');
    exit();
}

$user_id = $_SESSION['user_login'];

// ตรวจสอบสิทธิ์การเข้าถึง และดึง avatar
$stmt = $conn->prepare("SELECT avatar, role FROM medical_staff WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้!';
    header('location: access_denied.php');
    exit();
}

// ตรวจสอบบทบาท
$allowed_roles = ['doctor', 'nurse', 'admin'];
if (!in_array($user['role'], $allowed_roles)) {
    $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้!';
    header('location: access_denied.php');
    exit();
}

// ตรวจสอบ avatar
$avatar = "./assets/img/user.jpg"; // ตั้งค่ารูปเริ่มต้น ถ้าไม่มีในฐานข้อมูล
if (!empty($user['avatar'])) {
    $avatar = 'data:image/jpeg;base64,' . base64_encode($user['avatar']);
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon เริ่มต้น (สำหรับการโหลดครั้งแรก) -->
    <link id="favicon" rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/style_index.css">

    <!-- <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-datetimepicker.min.css"> -->




    <style>
        /* Styling for loading screen */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #loading-spinner {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <style>
        /* Critical CSS */
        body {
            font-family: Arial, sans-serif;
        }

        /* เพิ่ม CSS ที่สำคัญที่นี่ */
    </style>

</head>

<body>
    <div id="loading-screen">
        <div id="loading-spinner"></div>
    </div>
    <!-- MAIND หลักเว็บ Header SideMenu --->
    <div class="main-wrapper">
        <div class="header" id="header">
            <div class="header-left">
                <a href="index.php" class="logo">
                    <img src="img/logo_nodetail.png" width="40" height="35" alt=""> <span style="color: #1053ae">ENT Center</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>



            <!-- Language Switcher -->
            <!-- <div class="language-switcher">
                <div class="dropdown">
                    <button class="dropbtn" id="current-language-btn">
                        <img src="https://flagcdn.com/w20/us.png" class="flag-icon" id="current-flag" />
                        English
                    </button>
                    <div class="dropdown-content" id="language-dropdown">
                        <a href="#" data-language="th">
                            <img src="https://flagcdn.com/w20/th.png" class="flag-icon" />
                            ภาษาไทย
                        </a>
                        <a href="#" data-language="en">
                            <img src="https://flagcdn.com/w20/us.png" class="flag-icon" />
                            English
                        </a>
                    </div>
                </div>
            </div> -->

            <!-- Language Switcher -->



            <!-- User Menu -->
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle"
                                src="<?php echo $avatar; ?>"
                                onerror="this.src='./assets/img/user.jpg';"
                                width="40" height="40" alt="User Avatar">
                            <span class="status online"></span>
                        </span>
                        <span class="user-name">
                            <?php
                            if (isset($_SESSION['user_login'])) {
                                $user_id = $_SESSION['user_login'];
                                $stmt = $conn->prepare("SELECT first_name, last_name FROM medical_staff WHERE id = :user_id");
                                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR); // เปลี่ยนเป็น PDO::PARAM_STR เพราะ id เป็น VARCHAR
                                $stmt->execute();
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($row) {
                                    echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name'], ENT_QUOTES, 'UTF-8');
                                } else {
                                    echo '<span data-lang="user-not-found" data-lang-en="User not found" data-lang-th="ไม่พบข้อมูลผู้ใช้">ไม่พบข้อมูลผู้ใช้</span>';
                                }
                            } else {
                                echo '<span data-lang="please-login" data-lang-en="Please login" data-lang-th="กรุณาเข้าสู่ระบบ">กรุณาเข้าสู่ระบบ</span>';
                            }
                            ?>
                        </span>

                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php?id=<?php echo htmlspecialchars($user_id); ?>">
                            โปรไฟล์ของฉัน
                        </a>
                        <a class="dropdown-item" href="edit-doctor.php?id=<?php echo htmlspecialchars($user_id); ?>">
                            แก้ไขโปรไฟล์
                        </a>
                        <a class="dropdown-item" href="./config/logout.php">ออกจากระบบ</a>
                    </div>

                </li>
            </ul>

            <!-- เมนูผู้ใช้บนมือถือ Mobile User Menu -->
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.html">โปรไฟล์ของฉัน</a>
                    <a class="dropdown-item" href="edit-profile.html">แก้ไขโปรไฟล์</a>
                    <a class="dropdown-item" href="settings.html">การตั้งค่า</a>
                    <a class="dropdown-item" href="login.html">ออกจากระบบ</a>
                </div>
            </div>

            <!-- แถบเมนูด้านข้าง Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
                    <div id="sidebar-menu" class="sidebar-menu">
                        <ul>
                            <li class="menu-title">เมนูหลัก</li>
                            <li>
                                <a href="index.php"><i class="fa fa-dashboard"></i> <span>แดชบอร์ด</span></a>
                            </li>
                            <?php if ($user['role'] === 'admin' || $user['role'] === 'nurse') : ?>
                                <li>
                                    <a href="opd.php"><i class="fa fa-pencil-square"></i> <span>เก็บประวัติผู้ป่วยนอก</span></a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="doctors.php"><i class="fa fa-user-md"></i> <span>บุคลากรทางการแพทย์</span></a>
                            </li>
                            <!-- <li>
                                <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>ผู้ป่วย</span></a>
                            </li> -->
                            <li class="submenu">
                                <a href="#"><i class="fa fa-wheelchair"></i> <span> ผู้ป่วย </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor') : ?>
                                        <li><a href="save_treatment.php">บันทึกการรักษา</a></li>
                                    <?php endif; ?>
                                    <li><a href="patients.php">ข้อมูลผู้ป่วย</a></li>
                                    <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor') : ?>
                                        <li><a href="recovery_guide.php">ให้คำแนะนำหลังการรักษา</a></li>
                                    <?php endif; ?>

                                </ul>
                            </li>
                            <li>
                                <a href="appointments.php"><i class="fa fa-calendar"></i> <span>ตารางการนัดหมาย</span></a>
                            </li>
                            <li>
                                <a href="medical-schedule.php"><i class="fa fa-calendar-check-o"></i> <span>ตารางแพทย์ออกตรวจ</span></a>
                            </li>
                            <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor') : ?>
                                <li>
                                    <a href="calendar-appointment.php"><i class="fa fa-calendar"></i> <span>ปฏิทิน</span></a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="settings.php"><i class="fa fa-cog"></i> <span>การตั้งค่า</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>




        </div>

        <div class="back-button-container">
            <a href="javascript:void(0)" onclick="history.back();" class="back-button">
                <!-- SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="back-icon">
                    <path d="M256 504C119 504 8 393 8 256S119 8 256 8s248 111 248 248-111 248-248 248zM142.1 273l135.5 135.5c9.4 9.4 24.6 9.4 33.9 0l17-17c9.4-9.4 9.4-24.6 0-33.9L226.9 256l101.6-101.6c9.4-9.4 9.4-24.6 0-33.9l-17-17c-9.4-9.4-24.6-9.4-33.9 0L142.1 239c-9.4 9.4-9.4 24.6 0 34z" />
                </svg>
                ย้อนกลับ
            </a>
        </div>


    </div>

    <!-- Container สำหรับเกล็ดหิมะ -->
    <!-- <div id="snow-container"></div> -->





    <!------------------------------------------------------------------------ JAVASCRIP สลับภาษาไทย อิ้ง ----------------------------------------------------------->
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Language Switcher script loaded');

            // ฟังก์ชันในการเปลี่ยนภาษา
            function switchLanguage(language) {
                console.log('Switching language to:', language);

                // อัพเดตข้อความของ elements ที่มี data-attributes
                const elements = document.querySelectorAll('[data-lang-en], [data-lang-th]');
                elements.forEach(el => {
                    el.textContent = el.getAttribute(`data-lang-${language}`);
                });

                // อัพเดตปุ่มและภาพธง
                const currentLanguageBtn = document.getElementById('current-language-btn');
                const currentFlag = document.getElementById('current-flag');
                if (language === 'th') {
                    currentLanguageBtn.innerHTML = '<img src="https://flagcdn.com/w20/th.png" class="flag-icon" id="current-flag"> ภาษาไทย';
                    currentFlag.src = 'https://flagcdn.com/w20/th.png';
                } else {
                    currentLanguageBtn.innerHTML = '<img src="https://flagcdn.com/w20/us.png" class="flag-icon" id="current-flag"> English';
                    currentFlag.src = 'https://flagcdn.com/w20/us.png';
                }

                // เก็บการเลือกภาษาที่เลือกใน localStorage
                localStorage.setItem('language', language);
            }

            // ตรวจสอบว่าภาษาที่เลือกถูกเก็บไว้ใน localStorage หรือไม่
            let currentLanguage = localStorage.getItem('language') || 'en'; // ค่าเริ่มต้นเป็นภาษาอังกฤษ
            switchLanguage(currentLanguage);

            // ตรวจสอบและตั้งค่าการทำงานของปุ่มสลับภาษา
            const langSwitcher = document.querySelector('#language-dropdown');
            if (langSwitcher) {
                langSwitcher.addEventListener('click', function(event) {
                    event.preventDefault(); // ป้องกันการรีเฟรชหน้า
                    const target = event.target.closest('a'); // ดึง <a> element ที่ถูกคลิก
                    if (target) {
                        const selectedLanguage = target.getAttribute('data-language');
                        switchLanguage(selectedLanguage);
                    }
                });
            }
        });
    </script> -->
    <!------------------------------------------------------------------------ JAVASCRIP สลับภาษาไทย อิ้ง ----------------------------------------------------------->



    <!------------------- Function ที่ไว้ตรวจสอบ path เลือกทั้งหมด <a> ภายใน .sidebar-menu เพื่อใช้ในการวนลูปและตรวจสอบว่าเมนูไหนที่ควรจะมีคลาส active ----->
    <!------------------- จากนั้นเลือก <a> ที่มี href ตรงกับชื่อไฟล์ปัจจุบัน (จากที่เราเก็บในตัวแปร path) ----->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current URL path
            var path = window.location.pathname.split('/').pop();

            // Remove 'active' class from all menu items
            var menuItems = document.querySelectorAll('.sidebar-menu a');
            menuItems.forEach(function(item) {
                item.parentElement.classList.remove('active');
            });

            // Add 'active' class to the current menu item
            // หากเมนูที่ตรงกับหน้าเว็บปัจจุบันถูกค้นพบ (currentMenu มีค่า), ให้เพิ่มคลาส active ไปยัง parent element ของมัน .classList.add('active') ใช้เพื่อเพิ่มคลาส active ไปยัง <li> ของเมนูที่ตรงกับหน้าเว็บ
            var currentMenu = document.querySelector(`.sidebar-menu a[href="${path}"]`);
            if (currentMenu) {
                currentMenu.parentElement.classList.add('active');
            }
        });
    </script>

    <script>
        // Simulate a loading process (e.g., loading data, scripts, etc.)
        window.addEventListener('load', function() {
            // Hide the loading screen and show the main content
            document.getElementById('loading-screen').style.display = 'none';

        });
    </script>


    <!-- <script>
        const createSnowflake = () => {
            const snowflake = document.createElement("div");
            snowflake.classList.add("snowflake");
            snowflake.textContent = "❄";
            snowflake.style.left = Math.random() * window.innerWidth + "px";
            snowflake.style.opacity = Math.random();
            snowflake.style.fontSize = Math.random() * 10 + 10 + "px";
            snowflake.style.animationDuration = Math.random() * 3 + 2 + "s";

            document.getElementById("snow-container").appendChild(snowflake);

            setTimeout(() => {
                snowflake.remove();
            }, 5000);
        };

        setInterval(createSnowflake, 100);
    </script> -->


    <script>
        console.log("User Role: <?php echo $_SESSION['role'] ?? 'N/A'; ?>");
        console.log("User ID: <?php echo $_SESSION['user_login'] ?? 'N/A'; ?>");
    </script>

    <script>
        // โหลดค่าสีจาก localStorage เมื่อเปิดหน้าใหม่
        document.addEventListener("DOMContentLoaded", function() {
            const savedColor = localStorage.getItem("headerColor");
            if (savedColor) {
                document.getElementById("header").style.backgroundColor = savedColor;
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // โหลด favicon ที่บันทึกไว้จาก localStorage
            const savedFavicon = localStorage.getItem("favicon");
            if (savedFavicon) {
                document.getElementById("favicon").href = savedFavicon;
            }

            // โหลดสี header ที่บันทึกไว้จาก localStorage
            const savedColor = localStorage.getItem("headerColor");
            if (savedColor) {
                document.getElementById("header").style.backgroundColor = savedColor;
            }
        });
    </script>




    <div class="sidebar-overlay" data-reff=""></div>


    <script src="./assets/js/jquery-3.2.1.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slimscroll.js"></script>
    <script src="./assets/js/app.js"></script>

    <script src="./assets/js/select2.min.js"></script>
    <script src="./assets/js/moment.min.js"></script>
    <script src="./assets/js/bootstrap-datetimepicker.min.js"></script>



</body>

</html>