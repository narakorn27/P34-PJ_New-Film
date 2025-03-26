<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เปลี่ยนรหัส PIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
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
        <h3>เปลี่ยนรหัส PIN</h3>
        <p id="pinPrompt">กรุณากรอกรหัส PIN ปัจจุบัน 6 หลัก</p>
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
        let currentPin = "";
        let newPin = "";
        let confirmPin = ""; // เพิ่มตัวแปรสำหรับยืนยัน PIN ใหม่
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
                currentPin = pin;
                isConfirming = true;
                pinPrompt.innerText = "กรุณากรอกรหัส PIN ใหม่ 6 หลัก";
                clearInputs();
            } else if (newPin === "") {
                newPin = pin; // เก็บ PIN ใหม่ (ครั้งที่ 1)
                pinPrompt.innerText = "กรุณากรอกรหัส PIN ใหม่อีกครั้งเพื่อยืนยัน";
                clearInputs();
            } else if (confirmPin === "") {
                confirmPin = pin; // เก็บ PIN ใหม่ (ครั้งที่ 2) เพื่อยืนยัน
                pinPrompt.innerText = "กำลังเปลี่ยนรหัส PIN...";
                clearInputs();
                if (newPin !== confirmPin) {
                    alert("รหัส PIN ใหม่ทั้งสองไม่ตรงกัน กรุณากรอกใหม่");
                    resetPinFields();
                } else {
                    savePinToDatabase();
                }
            }
        }

        function savePinToDatabase() {
            fetch('./config/update_pin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `currentPin=${encodeURIComponent(currentPin)}&newPin=${encodeURIComponent(newPin)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("เปลี่ยนรหัส PIN สำเร็จ");
                        window.location.href = "setting.php";
                    } else {
                        alert(data.message);
                        resetPinFields();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function resetPinFields() {
            newPin = "";
            confirmPin = "";
            isConfirming = false;
            pinPrompt.innerText = "กรุณากรอกรหัส PIN ปัจจุบัน 6 หลัก";
            clearInputs();
        }
    </script>

</body>

</html>