<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './config/connect_database.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['id'])) {
    header("Location: splash.php");
    exit();
}

$user_id = $_SESSION['id'];

// ตรวจสอบว่าเคยตั้ง PIN แล้วหรือยัง
$sql = "SELECT pin_code FROM patients_login WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// ถ้ายังไม่มี PIN ให้ไปหน้า set_pin.php
if ($row && empty($row['pin_code'])) {
    header("Location: set_pin.php");
    exit();
}

// ตรวจสอบว่าผู้ใช้ยืนยัน PIN แล้วหรือยัง
if (!isset($_SESSION['pin_verified'])) {
    header("Location: enter_pin.php");
    exit();
}


// ดึงรูปโปรไฟล์จากฐานข้อมูล
$sql = "SELECT profile_picture FROM patients_login WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);  // ใช้ user_id ในการดึงข้อมูล
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// ตรวจสอบว่ามีข้อมูลรูปโปรไฟล์หรือไม่
if ($row && !empty($row['profile_picture'])) {
    $profile_picture = $row['profile_picture']; // ข้อมูล BLOB รูปโปรไฟล์
    // แปลง BLOB เป็น base64 เพื่อแสดงใน img tag
    $profile_image = 'data:image/jpeg;base64,' . base64_encode($profile_picture);
} else {
    // ถ้าไม่มีรูปโปรไฟล์ ใช้ภาพเริ่มต้น
    $profile_image = 'assets/img/user.jpg';
}

?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Frameworks & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style_header.css" />


    <script>
        document.addEventListener("scroll", function() {
            const header = document.querySelector(".custom-header");
            if (window.scrollY > 50) {
                header.classList.add("scrolling");
            } else {
                header.classList.remove("scrolling");
            }
        });
    </script>
</head>

<body>

    <div id="splash-screen">
        <img id="splash-logo" src="assets/img/logo_clinic.png" alt="Splash Logo" />
        <div class="loading-text">Loading...</div>
    </div>


    <div class="custom-navbar"></div>

    <header class="custom-header">
        <img src="assets/img/logo_banner3.png" alt="Logo" class="logo">

        <!-- Desktop Navigation -->
        <nav class="desktop-nav">
            <ul>
                <li><a href="home_page.php">หน้าแรก</a></li>
                <li><a href="main-menu.php">เมนู</a></li>
                <li><a href="contact.php">ข้อมูลข่าวสาร</a></li>
                <li><a href="setting.php">ตั้งค่า</a></li>
            </ul>
        </nav>

        <!-- Mobile Icons -->
        <div class="mobile-icons">
            <div class="custom-notification-bell" id="custom-notificationBell">
                <i class="bx bx-bell"></i>
                <div class="custom-notification-count" id="custom-notificationCount">0</div>
            </div>

            <a href="medical_history.php">
                <img id="profileImage"
                    src="<?php echo isset($profile_image) && !empty($profile_image) ? $profile_image : 'assets/img/user.jpg'; ?>"
                    alt="Profile Picture" class="custom-nav__img" />
            </a>



    </header>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
        <ul>
            <li><a href="home_page.php"><i class='bx bx-home-alt custom-nav__icon'></i><span>หน้าแรก</span></a></li>
            <li><a href="main-menu.php"><i class='bx bx-list-ul custom-nav__icon'></i><span>เมนู</span></a></li>
            <li><a href="contact.php"><i class='bx bx-message-square-detail custom-nav__icon'></i><span>ข้อมูลข่าวสาร</span></a></li>
            <li><a href="setting.php"><img id="iconPreview" class="custom-nav__icon" src="assets/icons/settings-icon.svg" alt="icon"><span>ตั้งค่า</span></a></li>
        </ul>
    </nav>


    <main>

    </main>


    <!-- โหลด Bootstrap JS (และ Popper.js หากจำเป็น) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>



    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import {
            getMessaging,
            getToken,
            onMessage
        } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBcXtu7HGC2eHOaEH8msEBqomBpZ95mXi4",
            authDomain: "ent-center-notification.firebaseapp.com",
            projectId: "ent-center-notification",
            storageBucket: "ent-center-notification.firebasestorage.app",
            messagingSenderId: "381805619576",
            appId: "1:381805619576:web:88fdda76279981230cc08d",
            measurementId: "G-HQJV0L42N6"
        };

        // ✅ Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // ✅ ลงทะเบียน Service Worker (ใช้ sw.js)
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("sw.js")
                .then((registration) => {
                    console.log("✅ Service Worker ลงทะเบียนสำเร็จ!", registration);

                    // ✅ ขอสิทธิ์แจ้งเตือนหลังจากลงทะเบียน Service Worker สำเร็จ
                    return Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            console.log("✅ Notification permission granted.");
                            return getToken(messaging, {
                                vapidKey: "BFbqo6UXBNRAOvtgVHtcAZI0ualTGix62IWkZYp1_NsqIS5g6KkmBFc9wttLWX0a3fD5Vgt-LMuwBlmJK4nvaRY",
                                serviceWorkerRegistration: registration // บังคับให้ใช้ sw.js
                            });
                        } else {
                            console.log("❌ Notification permission denied.");
                            return null;
                        }
                    });
                })
                .then(token => {
                    if (token) {
                        console.log("🔥 FCM Token:", token);
                        saveTokenToDatabase(token); // ส่ง Token ไปเก็บในฐานข้อมูล
                    } else {
                        console.warn("⚠️ No FCM Token received.");
                    }
                })
                .catch(error => {
                    console.error("❌ Error:", error);
                });
        } else {
            console.warn("⚠️ Browser ไม่รองรับ Service Worker!");
        }

        // ✅ รับข้อความขณะเปิดหน้าเว็บ
        onMessage(messaging, (payload) => {
            console.log("📩 Message received:", payload);
            new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: payload.notification.icon
            });
        });

        // ✅ ฟังก์ชันบันทึก Token ลงฐานข้อมูล
        function saveTokenToDatabase(token) {
            fetch("./config/save_fcm_token.php", {
                    method: "POST",
                    body: JSON.stringify({
                        fcm_token: token
                    }),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.text()) // อ่านเป็น text ก่อน
                .then(text => {
                    try {
                        console.log("📝 Raw Response:", text); // ✅ Debug ดูข้อมูลที่ได้
                        const data = JSON.parse(text); // แปลงเป็น JSON
                        console.log("✅ Token saved:", data);
                    } catch (error) {
                        console.error("❌ JSON Parse Error:", text);
                    }
                })
                .catch(error => console.error("❌ Error saving token:", error));
        }
    </script>

    <script>
        function hideSplashScreen() {
            console.log("🔥 hideSplashScreen() ทำงานแล้ว");

            const splashScreen = document.getElementById("splash-screen");
            const mainContent = document.querySelector("main"); // ใช้แท็ก <main> แทน

            if (!splashScreen) {
                console.warn("⚠️ ไม่มี Splash Screen ใน DOM");
                return;
            }

            splashScreen.style.opacity = "0";
            setTimeout(() => {
                splashScreen.style.display = "none";
                if (mainContent) {
                    mainContent.style.display = "block"; // แสดงเนื้อหาหลัก
                }
            }, 500);
        }

        // ซ่อน Splash Screen หลังจากโหลดข้อมูลเสร็จ
        window.onload = hideSplashScreen;
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentPage = window.location.pathname.split("/").pop(); // ดึงชื่อไฟล์จาก URL
            let menuItems = document.querySelectorAll(".desktop-nav ul li a, .mobile-nav ul li a");

            menuItems.forEach(item => {
                if (item.getAttribute("href") === currentPage) {
                    item.parentElement.classList.add("active");
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            checkAppointments(); // โหลดการแจ้งเตือนตอนแรก

            var notificationBell = document.getElementById("custom-notificationBell");
            var notificationCount = document.getElementById("custom-notificationCount");

            // ฟังก์ชันอัปเดตจำนวนการแจ้งเตือน
            function updateNotificationCount(count) {
                if (notificationCount) {
                    notificationCount.textContent = count;
                    if (count > 0) {
                        notificationCount.style.visibility = 'visible'; // แสดงป้ายแจ้งเตือน
                        notificationBell.classList.add('shake'); // กระดิ่งจะสั่น
                        notificationCount.classList.add('shake'); // ป้ายแจ้งเตือนจะสั่น
                    } else {
                        notificationCount.style.visibility = 'hidden'; // ซ่อนป้ายแจ้งเตือน
                        notificationBell.classList.remove('shake'); // หยุดการสั่นของกระดิ่ง
                        notificationCount.classList.remove('shake'); // หยุดการสั่นของป้ายแจ้งเตือน
                    }
                }
            }

            // ฟังก์ชันตรวจสอบการนัดหมาย
            function checkAppointments() {
                fetch("./config/check_appointments.php")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            updateNotificationCount(data.unread_count);
                        }
                    })
                    .catch(error => console.error("Error checking appointments:", error));
            }

            // เมื่อคลิกที่กระดิ่ง, จำนวนการแจ้งเตือนจะเพิ่มขึ้น
            notificationBell.addEventListener('click', function() {
                fetch("./config/check_appointments.php")
                    .then(response => response.json())
                    .then(data => {
                        console.log("Received data:", data);

                        if (data.status === "success" && Array.isArray(data.appointments) && data.appointments.length > 0) {
                            showAppointmentModal(data.appointments);
                        } else {
                            showNoNotificationModal();
                        }
                    })
                    .catch(error => console.error("Error fetching appointments:", error));
            });

            function removeExistingModal() {
                document.querySelectorAll(".modal").forEach(modal => modal.remove());
            }

            function showAppointmentModal(appointments) {
                removeExistingModal();

                let modalContent = `
        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แจ้งเตือนการนัดหมาย</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">`;

                appointments.forEach(appointment => {
                    modalContent += `
                <p><strong>รหัส:</strong> ${appointment.appointment_id}</p>
                <p><strong>วันนัดหมาย:</strong> ${appointment.appointment_date}</p>
                <hr/>`;
                });

                modalContent += `
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="markAsReadButton" data-bs-dismiss="modal">อ่านแล้ว</button>
            </div>
        </div>
    </div>
</div>`;

                document.body.insertAdjacentHTML("beforeend", modalContent);
                const modal = new bootstrap.Modal(document.getElementById("appointmentModal"));
                modal.show();

                document.getElementById("markAsReadButton").addEventListener("click", function() {
                    markNotificationsAsRead();
                });
            }

            function showNoNotificationModal() {
                removeExistingModal();

                const modalContent = `
        <div class="modal fade" id="noNotificationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แจ้งเตือน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>ไม่มีการแจ้งเตือนใหม่</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>`;

                document.body.insertAdjacentHTML("beforeend", modalContent);
                new bootstrap.Modal(document.getElementById("noNotificationModal")).show();
            }

            function markNotificationsAsRead() {
                fetch("./config/update_notification_status.php", {
                        method: "POST",
                        body: JSON.stringify({
                            is_read: 1
                        }),
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Notification status updated:", data);
                        updateNotificationCount(0);
                        checkAppointments();
                    })
                    .catch(error => console.error("Error updating notification status:", error));
            }

            document.addEventListener("visibilitychange", function() {
                if (document.visibilityState === "visible") {
                    checkAppointments();
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var profileImageElement = document.getElementById("profileImage");

            // ตรวจสอบว่ามี element ที่มี id="profileImage" หรือไม่
            if (profileImageElement) {
                var profileImageUrl = "<?php echo $profile_image ? $profile_image : 'assets/img/user.jpg'; ?>";

                // ตั้งค่ารูปโปรไฟล์
                if (profileImageUrl && profileImageUrl.trim() !== "") {
                    profileImageElement.src = profileImageUrl;
                } else {
                    profileImageElement.src = 'assets/img/user.jpg'; // fallback image
                }
            } else {
                console.error("Element with id 'profileImage' not found.");
            }
        });
    </script>


</body>

</html>