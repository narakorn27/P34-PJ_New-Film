<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login ENT-CENTER</title>

    <link rel="stylesheet" href="assets/css/style_login.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* ตั้งค่าทั่วไป */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "IBM Plex Sans Thai", sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* จัดให้อยู่ตรงกลางแนวตั้ง */
            align-items: center;
            /* จัดให้อยู่ตรงกลางแนวนอน */
            background-color: #ffffff;
            /* สีพื้นหลัง */
        }

        /* Navbar สีฟ้า */
        .navbar {
            background-color: #1655b9;
            /* สีฟ้า */
            height: 30px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1000;
        }

        /* Footer สีฟ้า */
        .footer {
            background-color: #1655b9;
            /* สีฟ้า */
            color: white;
            text-align: center;
            padding: 15px 20px;
            width: 100%;
            position: fixed;
            bottom: -1px;
            left: 0;
            z-index: 1000;
            font-size: 0.9rem;
        }

        /* ตั้งค่าหน้า Splash */
        #splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            text-align: center;
        }

        /* โลโก้ */
        #splash-logo {
            max-width: 55%;
            max-height: 55%;
        }

        /* ข้อความ Loading */
        .loading-text {
            font-size: 3vw;
            /* ใช้หน่วยที่ปรับขนาดตามความกว้างของหน้าจอ */
            color: #1655b9;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        /* ซ่อนหน้า Login ก่อน */
        #login-page {
            display: none;
            text-align: center;
            padding: 20px;
        }

        /* สำหรับหน้า Login */
        #login-page {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            background-color: #ffffff;
            padding: 20px;
            overflow-y: auto;
            box-sizing: border-box;
        }

        /* ปรับรูปโลโก้ให้พอดีกับทุกขนาดหน้าจอ */
        .logo_login {
            max-width: 50vw;
            /* ปรับตามความกว้างหน้าจอ */
            max-height: 30vh;
            /* ปรับตามความสูงหน้าจอ */
            margin-bottom: 2rem;
            /* เว้นระยะจากหัวข้อ */
        }

        /* สไตล์หัวข้อ */
        form {
            text-align: left;
            /* ให้ข้อความในฟอร์มชิดซ้าย */
            display: flex;
            flex-direction: column;
            gap: 1rem;
            /* ระยะห่างระหว่างฟิลด์ */
            width: 90%;
            max-width: 400px;/
        }

        #username::placeholder {
            font-size: 0.8rem;
            color: #cccccc;
        }

        #password::placeholder {
            font-size: 0.8rem;
            color: #cccccc;
        }

        /* ปรับขนาดหัวข้อ */
        h1 {
            color: #1655b9;
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .mt-1 {
            margin-top: -1.75rem !important;
        }

        /* ปรับปุ่มให้ตรงกับขนาดฟอนต์ */
        .custom-btn {
            border-radius: 20px;
            /* ขอบมน */
            padding: 5px;
            /* เพิ่มความยาวปุ่มด้วย padding */
            background-color: #1655b9;
            /* ตัวอย่างสี */
            color: white;
            /* ตัวอักษรสีขาว */
            border: none;
            /* ลบเส้นขอบ */
            font-size: 0.8rem;
            /* ปรับขนาดฟอนต์ */
            transition: all 0.3s ease;
            /* เพิ่มเอฟเฟกต์ตอน hover */
            width: auto;
            /* กำหนดให้ปุ่มปรับตามเนื้อหา */
            min-width: 100px;
            /* กำหนดความกว้างขั้นต่ำ */
        }

        .custom-btn:hover {
            background-color: #0d3a75;
            /* สีเมื่อ hover */
        }

        /* ฟอร์ม */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            /* ระยะห่างระหว่างฟิลด์ */
            width: 90%;
            max-width: 400px;
            /* จำกัดความกว้างสูงสุดของฟอร์ม */
        }

        /* Responsive Design */
        /* สำหรับมือถือ */


        /* สำหรับขนาดหน้าจอกลาง (แท็บเล็ต) */
        @media (min-width: 412px) and (max-height: 915px) {
            h1 {
                font-size: 1.8rem;
            }

            .logo_login {
                max-width: 500px;
                max-height: 300px;
            }

            .custom-btn {
                font-size: 1rem;
                /* ขนาดฟอนต์ */
                padding: 8px 20px;
                /* เพิ่ม padding */
            }
        }


        @media (max-width: 576px) {
            h1 {
                font-size: 1.5rem;
            }

            .logo_login {
                max-width: 300px;
            }

            .custom-btn {
                font-size: 1rem;
                padding: 11px 36px;
            }
        }

        /* สำหรับขนาดหน้าจอกลาง (แท็บเล็ต) */
        @media (min-width: 577px) and (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .logo_login {
                max-width: 300px;
            }

            .custom-btn {
                font-size: 1rem;
                /* ขนาดฟอนต์ */
                padding: 8px 20px;
                /* เพิ่ม padding */
            }
        }

        /* สำหรับขนาดหน้าจอใหญ่ (เดสก์ท็อป) */
        @media (min-width: 769px) {
            h1 {
                font-size: 2rem;
            }

            .logo_login {
                max-width: 40vh;
                /* ปรับให้สัมพันธ์กับความสูงของหน้าจอ */
            }

            .custom-btn {
                font-size: 1.2rem;
                /* ขนาดฟอนต์ */
                padding: 5px 25px;
                /* เพิ่ม padding */
            }
        }

        /* ขนาดหน้าจอใหญ่มาก (4K หรือจอใหญ่พิเศษ) */
        @media (min-width: 1200px) {
            .custom-btn {
                font-size: 1.5rem;
                /* ขนาดฟอนต์ */
                padding: 12px 30px;
                /* เพิ่ม padding */
            }
        }
    </style>

</head>

<body>
    <!-- Splash Screen -->
    <div id="splash-screen">
        <div class="navbar"></div>

        <img id="splash-logo" src="assets/img/logo_clinic.png" alt="Splash Logo" />
        <div class="loading-text">Loading...</div>

        <div class="footer"></div>
    </div>

    <!-- Login Page -->
    <div id="login-page">
        <div class="navbar"></div>

        <!-- โลโก้และหัวข้อ -->
        <div class="container text-center py-3">
            <img class="logo_login img-fluid" src="assets/img/logo_clinic.png" alt="Logo" />
            <h1 class="mt-1">เข้าสู่ระบบ</h1>
        </div>

        <!-- ฟอร์ม -->
        <div class="container">
            <form id="loginForm" class="w-100 w-sm-75 w-md-50 w-lg-25 mx-auto">
                <div class="mb-3">
                    <label for="username" class="form-label text-start">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="tel" class="form-control" id="username" name="username"
                        placeholder="หมายเลขบัตรประชาชน 13 หลัก" required maxlength="13"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-start">รหัสผ่าน (Password)</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="วันเดือนปีเกิด (ค.ศ.)" required oninput="this.value = this.value.replace(/[^0-9]/g, '');" inputmode="numeric" />
                </div>


                <!-- ปรับตำแหน่งของปุ่ม -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn custom-btn w-sm-auto">
                        เข้าสู่ระบบ
                    </button>
                </div>
            </form>
        </div>

        <div class="footer"></div>
    </div>

    <script>
        // ตั้งเวลาแสดง Splash Screen
        setTimeout(() => {
            // ซ่อน Splash Screen
            document.getElementById("splash-screen").style.display = "none";

            // แสดงหน้า Login
            document.getElementById("login-page").style.display = "block";
        }, 2000); // แสดงผล 2 วินาที
    </script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการส่งฟอร์มปกติ

            const form = new FormData(this);

            // ป้องกันการกดปุ่มซ้ำ
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            Swal.fire({
                title: 'กำลังตรวจสอบข้อมูล',
                text: 'กรุณารอสักครู่...',
                didOpen: () => Swal.showLoading()
            });

            fetch('config/submit_infouser.php', {
                    method: 'POST',
                    body: form
                })
                .then(response => {
                    if (!response.ok) throw new Error('เซิร์ฟเวอร์ไม่ตอบสนอง');
                    return response.json();
                })
                .then(data => {
                    Swal.close(); // ปิด Loading
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'เข้าสู่ระบบสำเร็จ!',
                            text: data.message,
                            confirmButtonText: 'ดำเนินการต่อ'
                        }).then(() => {
                            window.location.href = data.redirect; // เปลี่ยนหน้าไปยังที่กำหนด
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด!',
                        text: `ไม่สามารถติดต่อเซิร์ฟเวอร์ได้: ${error.message}`
                    });
                })
                .finally(() => {
                    submitButton.disabled = false; // เปิดปุ่มอีกครั้ง
                });
        });
    </script>




    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>

</html>