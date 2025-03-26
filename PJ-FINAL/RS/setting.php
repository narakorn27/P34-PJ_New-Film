<?php
include 'mid_string.php'
?>

<html>

<head>
    <link rel="stylesheet" href="assets/css/styles_setting.css" />
</head>

<body>
    <h2>ตั้งค่า</h2>
    <div class="appointment-card">
        <a href="edit_profile_user.php?id=<?php echo $_SESSION['id']; ?>" class="menu-item">
            <span>การตั้งค่าความเป็นส่วนตัว</span><span class="arrow">&gt;</span>
        </a>
    </div>
    <h2>ช่วยเหลือ</h2>
    <div class="appointment-card">
        <div class="menu-item" onclick="window.location.href='contactus.php'">
            ศูนย์ช่วยเหลือ<span class="arrow">&gt;</span>
        </div>
        <div class="menu-item">
            <a href="strategy-plan.php">
                <span>นโยบายของโรงพยาบาล</span><span class="arrow">&gt;</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="https://www.pmk.ac.th/index.php/content_page/pmk/about-us.html">
                <span>เกี่ยวกับเรา</span>
                <span class="arrow">&gt;</span>
            </a>
        </div>
    </div>
    <div class="button-container">
        <button class="button" onclick="window.location.href='change_pin.php'">เปลี่ยนรหัส PIN CODE</button>
        <button id="logoutButton" class="button">ออกจากระบบ</button>
    </div>



    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>


    <script>
        document.getElementById('logoutButton').addEventListener('click', function() {
            // ส่งคำขอให้ PHP ทำการ logout
            fetch('./config/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'splash.php'; // รีไดเร็กไปยังหน้าล็อกอิน
                    } else {
                        alert('ไม่สามารถออกจากระบบได้');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

</body>

</html>