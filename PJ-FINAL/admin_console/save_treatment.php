<?php include 'mid_string.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <title>บันทึกการรักษา</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">



    <style>
        /* เพิ่มเครื่องหมายดอกจันสีแดงสำหรับฟิลด์ที่จำเป็น */
        .required::after {
            content: " *";
            color: red;
        }

        #urgency option[value="Normal"] {
            color: blue;
            /* สีฟ้าสำหรับปกติ */
        }

        #urgency option[value="Urgent"] {
            color: red;
            /* สีแดงสำหรับเร่งด่วน */
        }
    </style>

</head>

<body>
    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-8 col-4">
                        <h4 class="page-title">บันทึกการรักษา
                    </div>
                </div>

                <!-- Search Form Section -->
                <div id="searchFormSection">
                    <div class="card mb-3">
                        <div class="card-header" data-lang="searchFormTitle" data-lang-en="Search Patient" data-lang-th="ค้นหาผู้ป่วย">
                            ค้นหาผู้ป่วย
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="searchBy" data-lang="searchByLabel" data-lang-en="Search By" data-lang-th="ค้นหาตาม:">ค้นหาตาม:</label>
                                    <select class="form-control" name="searchBy" id="searchBy">
                                        <option value="hn" data-lang="searchHN" data-lang-en="HN" data-lang-th="HN">HN</option>
                                        <option value="full_name" data-lang="searchFullName" data-lang-en="Full Name" data-lang-th="ชื่อ-นามสกุล">ชื่อ-นามสกุล</option>
                                        <option value="id_card" data-lang="searchIDCard" data-lang-en="ID Card" data-lang-th="เลขบัตรประชาชน">เลขบัตรประชาชน</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchValue" data-lang="searchValueLabel" data-lang-en="Search Value" data-lang-th="ค่าที่ต้องการค้นหา:">ค่าที่ต้องการค้นหา:</label>
                                    <input type="text" class="form-control" name="searchValue" id="searchValue" />
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary" style="margin-top: 2rem;" onclick="searchPatient()" data-lang="searchButton" data-lang-en="Search" data-lang-th="ค้นหา">
                                        ค้นหา
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="EditpatientForm" class="bg-white p-4 rounded shadow-sm" style="margin-top: 20px;">
                    <h2 data-lang="updateFormTitle" data-lang-en="Update Treatment Information" data-lang-th="อัปเดตข้อมูลการรักษา">อัปเดตข้อมูลการรักษา</h2>
                    <form method="POST" action="./config/update_patients_appointment.php">

                        <!-- ฟิลด์ซ่อนสำหรับส่งค่า ID -->
                        <input type="hidden" id="account_id" name="account_id">

                        <div class="row mb-3">
                            <!-- HN Field -->
                            <div class="col-md-4">
                                <label for="hn" data-lang="hnLabel" data-lang-en="HN" data-lang-th="HN">HN:</label>
                                <input type="text" class="form-control" id="hn" name="hn" disabled>
                            </div>
                            <!-- ID Card Field -->
                            <div class="col-md-4">
                                <label for="id_card" data-lang="idCardLabel" data-lang-en="ID Card" data-lang-th="เลขบัตรประชาชน">เลขบัตรประชาชน:</label>
                                <input type="text" class="form-control" id="id_card" name="id_card" disabled>
                            </div>
                            <!-- Date of Birth Field -->
                            <div class="col-md-4">
                                <label for="date_of_birth" data-lang="dateOfBirthLabel" data-lang-en="Date of Birth" data-lang-th="วันเกิด">วันเกิด:</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" disabled>
                            </div>
                        </div>


                        <!-- Name Fields in Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" data-lang="firstNameLabel" data-lang-en="First Name" data-lang-th="ชื่อ">ชื่อ:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" data-lang="lastNameLabel" data-lang-en="Last Name" data-lang-th="นามสกุล">นามสกุล:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" disabled>
                            </div>
                        </div>

                        <!-- Status and Urgency Fields in Row -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status_now" class="form-label required" data-lang="statusLabel"
                                    data-lang-en="Status" data-lang-th="สถานะการตรวจ">สถานะการตรวจ</label>
                                <select id="status_now" name="status" class="form-select" required>
                                    <option value="" disabled selected hidden>สถานะการตรวจ</option>
                                    <option value="Pending">รอตรวจ</option>
                                    <option value="Completed">รักษาเสร็จสิ้น</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="urgency" class="form-label required" data-lang="urgencyLabel" data-lang-en="Urgency" data-lang-th="ความเร่งด่วน">ความเร่งด่วน</label>
                                <select id="urgency" name="urgency" class="form-select" required>
                                    <option value="Normal" data-lang="urgencyNormal" data-lang-en="Normal" data-lang-th="ปกติ">ปกติ</option>
                                    <option value="Urgent" data-lang="urgencyUrgent" data-lang-en="Urgent" data-lang-th="เร่งด่วน">เร่งด่วน</option>
                                </select>
                            </div>
                            <!-- ช่องเลือกประเภทการนัด -->
                            <div class="col-md-4">
                                <label for="editAppointmentType" class="form-label required" data-lang="appointmentTypeLabel" data-lang-en="Appointment Type" data-lang-th="ประเภทการนัด">ประเภทการนัด</label>
                                <select id="editAppointmentType" name="appointment_type_edit" class="form-select" required>
                                    <option value="" data-lang="appointmentTypeSelect" data-lang-en="Select Appointment Type" data-lang-th="เลือกประเภทการนัด">เลือกประเภทการนัด</option>
                                    <option value="Walk-in" data-lang="appointmentTypeWalkIn" data-lang-en="Walk-in" data-lang-th="ทั่วไป">ทั่วไป</option>
                                    <option value="Pre-booking" data-lang="appointmentTypeFollowUp" data-lang-en="Follow-up" data-lang-th="ติดตามอาการ">ติดตามอาการ</option>
                                </select>
                            </div>


                        </div>

                        <!-- Medical History Input -->
                        <div class="form-group mb-3">
                            <label for="medicalHistoryInput" class="form-label" data-lang="medicalHistoryLabel" data-lang-en="Medical History" data-lang-th="อาการที่คนไข้ต้องการรักษา / ขอคำแนะนำหลังจากรักษา">อาการของคนไข้ที่แจ้งมาเบื้องต้น</label>
                            <textarea id="medicalHistoryInput" name="additional_details" class="form-control" rows="4" placeholder="ระบุรายละเอียดการรักษาและอาการต่างๆที่มีผลในการจ่ายยา"></textarea>
                        </div>

                        <!-- Doctor Diagnosis and Treatment Area -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="doctorDiagnosis" class="form-label required" data-lang="doctorDiagnosisLabel" data-lang-en="Doctor Diagnosis" data-lang-th="บันทึกอาการ/การกินยาต่างๆ/คำแนะนำจากทีมแพทย์พยาบาล">การวินิจฉัยจากหมอ</label>
                                <textarea id="doctorDiagnosis" name="doctorDiagnosis" class="form-control" rows="4" placeholder="ระบุรายละเอียดการวินิจฉัย..." required></textarea>
                            </div>

                            <div class="col-md-3 text-center align-items-center">
                                <label for="treatment_area" class="form-label required" data-lang="treatmentAreaLabel" data-lang-en="Treatment Area" data-lang-th="สาขาการรักษา">สาขาการรักษา</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="หู" id="ear" required>
                                        <label class="form-check-label" for="ear" data-lang="earLabel" data-lang-en="Ear" data-lang-th="หู">หู</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="คอ" id="throat" required>
                                        <label class="form-check-label" for="throat" data-lang="throatLabel" data-lang-en="Throat" data-lang-th="คอ">คอ</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="จมูก" id="nose" required>
                                        <label class="form-check-label" for="nose" data-lang="noseLabel" data-lang-en="Nose" data-lang-th="จมูก">จมูก</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="selectDoctor" class="form-label required" data-lang="selectDoctorLabel" data-lang-en="Select Doctor" data-lang-th="หมอที่ดูแลเคส">หมอที่ดูแลเคส</label>
                                <select id="selectDoctor" name="selectDoctor" class="form-select" required>
                                    <option value="" data-lang="selectDoctorOption" data-lang-en="Select a Doctor" data-lang-th="เลือกหมอ">เลือกหมอ</option>
                                    <!-- รายชื่อหมอจะถูกเติมโดย JavaScript -->
                                </select>
                            </div>


                            <div class="col-md-3">
                                <label for="appointment_date" data-lang="appointmentDateLabel" data-lang-en="Appointment Date" data-lang-th="เลือกวันนัดหมาย">เลือกวันนัดหมาย:</label>
                                <input type="text" class="form-control" id="appointment_date" name="appointment_date" placeholder="เลือกวันนัดหมาย">
                            </div>


                        </div>


                        <!-- Save Button -->
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success" onclick="updatePatient()" data-lang="saveButton" data-lang-en="Save" data-lang-th="บันทึก">บันทึก</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" onclick="createNewUser()" data-lang="createButton" data-lang-en="Create Account" data-lang-th="สร้างบัญชีใหม่">สร้างบัญชีใหม่</button>
                            </div>
                        </div>




                    </form>
                </div>

            </div>

        </div>
    </div>


    <script>
        function searchPatient() {
            let searchBy = document.getElementById("searchBy").value;
            let searchValue = document.getElementById("searchValue").value.trim();

            if (!searchValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ข้อมูลไม่ครบ',
                    text: 'กรุณากรอกข้อมูลสำหรับค้นหา',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            fetch("search_patient.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `searchBy=${searchBy}&searchPatientId=${searchValue}`,
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.message) {
                        Swal.fire({
                            icon: 'info',
                            title: 'ผลการค้นหา',
                            text: data.message,
                            confirmButtonText: 'ตกลง'
                        });
                    } else {
                        // แสดงข้อมูลผู้ป่วยในฟอร์ม
                        document.getElementById("EditpatientForm").style.display = "block";
                        document.getElementById("hn").value = data.patient.hn || "";
                        document.getElementById("first_name").value = data.patient.first_name || "";
                        document.getElementById("last_name").value = data.patient.last_name || "";
                        document.getElementById("status_now").value = data.patient.status || "";
                        document.getElementById("urgency").value = data.patient.urgency || "";
                        document.getElementById("medicalHistoryInput").value = data.patient.additional_details || "";

                        // แสดงค่า ID card และ date_of_birth
                        document.getElementById("id_card").value = data.patient.id_card || "";
                        document.getElementById("date_of_birth").value = data.patient.date_of_birth || "";

                        document.getElementById('account_id').value = data.patient.id || "";



                        // แสดงค่า appointment_type ในฟิลด์ select
                        let appointmentType = data.patient.appointment_type || "";
                        let appointmentTypeSelect = document.getElementById("editAppointmentType");
                        if (appointmentTypeSelect) {
                            if (appointmentType) {
                                appointmentTypeSelect.value = appointmentType;
                            } else {
                                appointmentTypeSelect.value = "";
                            }
                        }

                        if (data.patient.doctor_diagnosis) {
                            document.getElementById("doctorDiagnosis").value = data.patient.doctor_diagnosis;
                        }

                        let treatmentArea = data.patient.treatment_area || "";
                        let radios = document.getElementsByName("treatment_area");
                        radios.forEach(radio => radio.checked = (radio.value === treatmentArea));

                        if (data.patient.appointment_date) {
                            document.getElementById("appointment_date").value = data.patient.appointment_date;
                        }

                        // แสดงข้อมูลหมอใน select
                        let selectDoctor = document.getElementById("selectDoctor");
                        selectDoctor.innerHTML = "<option value='' data-lang='selectDoctorOption' data-lang-en='Select a Doctor' data-lang-th='เลือกหมอ'>เลือกหมอ</option>"; // รีเซ็ตตัวเลือก

                        // เพิ่มรายชื่อหมอจาก `data.doctors`
                        if (data.doctors && data.doctors.length > 0) {
                            data.doctors.forEach(doctor => {
                                let option = document.createElement("option");
                                option.value = doctor.id; // หรืออาจจะใช้ id ของหมอเพื่อส่งไปในฟอร์ม
                                option.textContent = doctor.full_name; // แสดงชื่อหมอ
                                selectDoctor.appendChild(option); // เพิ่มตัวเลือกหมอใน select
                            });
                        } else {
                            let option = document.createElement("option");
                            option.textContent = "ไม่พบข้อมูลหมอ";
                            selectDoctor.appendChild(option);
                        }

                        // ตั้งค่า select doctor ให้ตรงกับ doctor_id ของผู้ป่วย
                        if (data.patient.doctor_id) {
                            selectDoctor.value = data.patient.doctor_id;
                        }
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถค้นหาผู้ป่วยได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonText: 'ตกลง'
                    });
                });
        }
    </script>

    <script>
        function updatePatient() {
            const hn = document.getElementById("hn").value;
            const status = document.getElementById("status_now").value;
            const urgency = document.getElementById("urgency").value;
            const medicalHistory = document.getElementById("medicalHistoryInput").value;
            const doctorDiagnosis = document.getElementById("doctorDiagnosis").value;
            const treatmentArea = document.querySelector('input[name="treatment_area"]:checked')?.value;
            const appointmentDate = document.getElementById("appointment_date").value;
            const doctorId = document.getElementById("selectDoctor").value; // ดึงค่า doctor_id ที่ผู้ใช้เลือก
            const appointmentType = document.getElementById("editAppointmentType").value; // ดึงประเภทการนัด

            const formData = new FormData();
            formData.append("hn", hn);
            formData.append("status", status);
            formData.append("urgency", urgency);
            formData.append("additional_details", medicalHistory); // เพิ่ม medicalHistory
            formData.append("doctorDiagnosis", doctorDiagnosis);
            formData.append("treatment_area", treatmentArea);
            formData.append("appointment_date", appointmentDate);
            formData.append("doctor_id", doctorId); // เพิ่ม doctor_id
            formData.append("appointment_type_edit", appointmentType); // เพิ่ม appointment_type

            // ส่งคำร้องขอแบบ POST ไปยัง PHP
            fetch("./config/update_patients_appointment.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // เช็คว่ามี error ในข้อมูลหรือไม่
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.error,
                        });
                    } else if (data.message === "ข้อมูลได้รับการอัปเดตแล้ว") {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตข้อมูลสำเร็จ',
                            text: 'ข้อมูลของผู้ป่วยได้รับการอัปเดตแล้ว',
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์',
                    });
                });
        }
    </script>


    <script>
        function createNewUser() {
            const appointmentType = document.getElementById("editAppointmentType").value; // ใช้ ID ที่ถูกต้อง

            if (!appointmentType) {
                Swal.fire({
                    icon: 'warning',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่พบข้อมูลประเภทการนัดหมาย โปรดเลือกก่อนดำเนินการ',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            if (appointmentType !== "Pre-booking") {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกเป็น Pre-booking',
                    text: 'กรุณาเลือกประเภทการนัดหมายเป็น "ติดตามอาการ" ก่อนถึงจะสามารถสร้างบัญชีได้',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const idCard = document.getElementById("id_card")?.value || "";
            const dateOfBirth = document.getElementById("date_of_birth")?.value || "";
            const accountId = document.getElementById("account_id")?.value || "";

            if (!idCard || !dateOfBirth) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                    text: 'โปรดตรวจสอบว่ากรอกเลขบัตรประชาชนและวันเกิดเรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const formData = new FormData();
            formData.append("id_card", idCard);
            formData.append("date_of_birth", dateOfBirth);
            formData.append("appointment_type_edit", appointmentType);
            formData.append("account_id", accountId);

            fetch("./config/check_user_exists.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("📢 Response from PHP:", data); // Debugging

                    if (data.user_exists) {
                        // ถ้ามีผู้ใช้แล้ว
                        Swal.fire({
                            icon: 'info',
                            title: 'มีข้อมูลผู้ใช้อยู่แล้ว',
                            text: 'มีข้อมูลผู้ใช้ในระบบอยู่แล้ว กรุณาตรวจสอบข้อมูล',
                            confirmButtonText: 'ตกลง'
                        });
                    } else if (data.error) {
                        // ถ้ามีข้อผิดพลาด
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.error,
                            confirmButtonText: 'ตกลง'
                        });
                    } else if (data.new_account) {
                        // ถ้าสร้างบัญชีใหม่สำเร็จ
                        Swal.fire({
                            icon: 'success',
                            title: 'สร้างบัญชีผู้ป่วยสำเร็จ!',
                            html: `<b>Username:</b> ${data.new_account.username} <br> <b>Password:</b> ${data.new_account.password}`,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                })
                .catch(error => {
                    console.error("❌ Fetch Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์',
                        confirmButtonText: 'ตกลง'
                    });
                });
        }
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let appointmentInput = document.getElementById("appointment_date");

            flatpickr(appointmentInput, {
                enableTime: true, // เปิดให้เลือกเวลา
                dateFormat: "Y-m-d H:i", // รูปแบบที่บันทึก (เป็น ค.ศ.)
                locale: "th", // ตั้งค่าภาษาไทย
                minDate: "today", // ไม่ให้เลือกวันย้อนหลัง
                time_24hr: true, // ใช้เวลาแบบ 24 ชั่วโมง
                altInput: false, // ปิดการใช้ input ซ้อน (ทำให้ช่องเป็นสีขาวปกติ)

                onReady: function() {
                    appointmentInput.removeAttribute("readonly"); // ✅ ลบ readonly ออก
                },

                onOpen: function(selectedDates, dateStr, instance) {
                    convertToBuddhistYear(instance);
                },

                onYearChange: function(selectedDates, dateStr, instance) {
                    convertToBuddhistYear(instance);
                }
            });
        });

        /**
         * 📌 ฟังก์ชันแปลงปี ค.ศ. → พ.ศ.
         */
        function convertToBuddhistYear(instance) {
            setTimeout(() => {
                let currentYear = instance.currentYear; // ดึงค่าปี ค.ศ.
                let buddhistYear = currentYear + 543; // แปลงเป็น พ.ศ.
                instance.currentYearElement.value = buddhistYear; // อัปเดตให้แสดงปี พ.ศ.
            }, 10);
        }
    </script>


    <script>
        document.getElementById('urgency').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === 'Normal') {
                this.style.color = 'blue'; /* สีฟ้าสำหรับเลือกปกติ */
            } else if (selectedOption.value === 'Urgent') {
                this.style.color = 'red'; /* สีแดงสำหรับเลือกเร่งด่วน */
            }
        });
    </script>


    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- ภาษาไทย -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>