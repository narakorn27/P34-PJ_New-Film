<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ยืนยันรหัส PIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
        }

        .pin-container {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .pin-wrapper {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            flex-wrap: nowrap;
            /* ไม่ให้ช่อง PIN ขึ้นบรรทัดใหม่ */
        }

        .pin-input {
            width: 15%;
            min-width: 45px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 2px solid #1e3c72;
            border-radius: 10px;
            transition: 0.3s;
            flex: 1;
            max-width: 60px;
        }

        /* ปรับขนาด PIN เมื่อหน้าจอเล็ก */
        @media (max-width: 360px) {
            .pin-input {
                width: 12%;
                /* ลดขนาดลง */
                min-width: 35px;
                height: 40px;
                font-size: 20px;
            }
        }


        .pin-input:focus {
            border-color: #2a5298;
            outline: none;
            transform: scale(1.1);
        }

        .btn-confirm {
            width: 100%;
            background: #1e3c72;
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-size: 18px;
            margin-top: 20px;
        }

        .btn-confirm:hover {
            background: #2a5298;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="pin-container">
        <h3>ตั้งค่ารหัส PIN</h3>
        <p id="pinPrompt">กรุณากรอกรหัสผ่าน 6 หลัก</p>
        <div class="pin-wrapper">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
            <input type="text" class="pin-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
        </div>
        <p class="error-message" id="errorMessage">รหัส PIN ไม่ตรงกัน กรุณากรอกใหม่</p>
        <button class="btn btn-confirm mt-3" onclick="handlePinSubmit()">ยืนยัน</button>
    </div>

    <script>
        let firstPin = "";
        let isConfirming = false;
        const inputs = document.querySelectorAll(".pin-input");
        const errorMessage = document.getElementById("errorMessage");
        const pinPrompt = document.getElementById("pinPrompt");

        inputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, ""); // อนุญาตให้พิมพ์ได้แค่ตัวเลข
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function getPinValue() {
            return Array.from(inputs).map(input => input.value).join("");
        }

        function clearInputs() {
            inputs.forEach(input => input.value = "");
            inputs[0].focus();
        }

        function handlePinSubmit() {
            let pin = getPinValue();
            if (pin.length !== 6) {
                alert("กรุณากรอก PIN 6 หลักให้ครบ");
                return;
            }

            if (!isConfirming) {
                firstPin = pin;
                isConfirming = true;
                pinPrompt.innerText = "กรุณากรอกรหัส PIN อีกครั้งเพื่อยืนยัน";
                clearInputs();
            } else {
                if (pin === firstPin) {
                    savePinToDatabase(pin);
                } else {
                    errorMessage.style.display = "block";
                    isConfirming = false;
                    pinPrompt.innerText = "กรุณากรอกรหัสผ่าน 6 หลัก";
                    clearInputs();
                }
            }
        }

        function savePinToDatabase(pin) {
            fetch('./config/save_pin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `pin=${pin}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("ตั้งค่า PIN สำเร็จ");
                        window.location.href = "home_page.php";
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>