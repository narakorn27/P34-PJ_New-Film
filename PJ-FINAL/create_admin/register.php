<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.10/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>


<body>
    <div class="container">
        <!-- Left Section for Logo -->
        <div class="logo-section">
            <img src="logo_clinic.png" alt="Clinic Logo">
        </div>

        <!-- Right Section for Forms -->
        <div class="form-section">
            <div class="form-container">
                <!-- Switch Buttons -->
                <div class="btn-switch">
                    <button id="loginBtn" class="btn btn-primary">Login</button>
                    <button id="registerBtn" class="btn btn-secondary">Register</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="active" action="./config/db_login.php" method="post">
                    <h3>Login</h3>
                    <div class="mb-3">
                        <label for="loginUsername" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="loginUsername" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <!-- Register Form -->
                <form id="registerForm" class="hidden" action="./config/db_register.php" method="post">
                    <h3>Register</h3>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-control" id="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" class="form-control" id="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" name="c_password" class="form-control" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // กำหนดตัวแปรที่ใช้ในฟอร์ม Login และ Register
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        // การเปลี่ยนฟอร์มระหว่าง Login และ Register
        loginBtn.addEventListener('click', () => {
            loginForm.classList.add('active');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            registerForm.classList.remove('active');
            loginBtn.classList.add('btn-primary');
            loginBtn.classList.remove('btn-secondary');
            registerBtn.classList.add('btn-secondary');
            registerBtn.classList.remove('btn-primary');
        });

        registerBtn.addEventListener('click', () => {
            registerForm.classList.add('active');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
            loginForm.classList.remove('active');
            registerBtn.classList.add('btn-primary');
            registerBtn.classList.remove('btn-secondary');
            loginBtn.classList.add('btn-secondary');
            loginBtn.classList.remove('btn-primary');
        });

        // Login Form submission (การสมัครสมาชิก)
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // หยุดการ submit ฟอร์มปกติ

            const formData = new FormData(loginForm);

            fetch('./config/db_login.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                        }).then(() => {
                            window.location.href = '/PJ-FINAL/admin_console/index.php'; // Redirect ไปยังหน้าจัดการหลังจากล็อกอินสำเร็จ
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.message,
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Register Form submission (การลงทะเบียน)
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault(); // หยุดการ submit ฟอร์มปกติ

            const formData = new FormData(registerForm);

            fetch('./config/db_register.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                        }).then(() => {
                            window.location.href = 'register.php'; // Redirect ไปที่หน้า Login
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.message,
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.10/dist/sweetalert2.all.min.js"></script>




</body>

</html>