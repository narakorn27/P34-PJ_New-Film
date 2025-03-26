<?php include 'mid_string.php'; ?>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <title>เก็บประวัติผู้ป่วยนอก</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.8/dist/sweetalert2.min.css" rel="stylesheet">


    <!-- <link rel="stylesheet" href="./assets/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="./assets/css/select2.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="./css/style_index.css">


    <style>
        /* เพิ่มเครื่องหมายดอกจันสีแดงสำหรับฟิลด์ที่จำเป็น */
        .required::after {
            content: " *";
            color: red;
        }

        .btn-no-border {
            background-color: #009efb;
            /* ปรับสีปุ่ม */
            color: white;
            /* สีตัวอักษร */
            padding: 8px 16px;
            border: none;
            /* ไม่มีขอบ */
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-no-border:hover {
            background-color: #0056b3;
            /* เปลี่ยนสีตอน hover */
            border: none;
            /* ไม่มีขอบ */
        }
    </style>

</head>


<body>

    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-8 col-4">
                        <h4 class="page-title" data-lang="Medical Records" data-lang-en="Medical Records" data-lang-th="ข้อมูลผู้ป่วยและประวัติการรักษา">ข้อมูลผู้ป่วยและประวัติการรักษา</h4>
                    </div>
                </div>

                <!-- Toggle Buttons -->
                <div class="mb-3">
                    <button id="patientButton" type="button" class="btn btn-primary" onclick="toggleForm('patientFormSection', 'patientButton')" data-lang="OPD Patienty" data-lang-en="OPD Patient" data-lang-th="ซักประวัติ">ซักประวัติ</button>
                    <button id="searchButton" type="button" class="btn btn-secondary" onclick="toggleForm('searchFormSection', 'searchButton')" data-lang="searchPatient" data-lang-en="Search Patient" data-lang-th="ค้นหาประวัติการรักษา">ค้นหาประวัติการรักษา</button>
                </div>

                <!-- Patient Form Section -->
                <div id="patientFormSection">
                    <form id="patientForm" method="POST" action="./config/save_opd_patient.php">
                        <!-- Patient Information -->
                        <div class="card mb-3">
                            <div class="card-header" data-lang="patientInfo" data-lang-en="Patient Information" data-lang-th="ข้อมูลผู้ป่วย">ข้อมูลผู้ป่วย</div>
                            <div class="card-body">

                                <div class="row mb-3">
                                    <!-- ช่องกรอกรหัสผู้ป่วย -->
                                    <!-- <div class="col-md-3">
                                        <label for="id_card" class="form-label required" data-lang="id_card" data-lang-en="Patient ID (Please enter ID card number)" data-lang-th="รหัสบัตรประชาชน">รหัสบัตรประชาชน</label>
                                        <input type="text" id="id_card" name="id_card" class="form-control" placeholder="กรอกบัตรประชาชน" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                    </div>
                                     -->
                                    <div class="col-md-6">
                                        <label for="id_card" class="form-label required" data-lang="id_card" data-lang-en="Patient ID (Please enter ID card number)" data-lang-th="รหัสบัตรประชาชน">รหัสบัตรประชาชน</label>
                                        <div class="input-group">
                                            <input type="text" id="id_card" name="id_card" class="form-control" placeholder="กรอกบัตรประชาชน" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                            <button type="button" id="checkPatient" class="btn-no-border">ตรวจสอบข้อมูล</button>
                                        </div>
                                    </div>


                                    <!-- ช่องกรอกชื่อ -->
                                    <div class="col-md-3">
                                        <label for="firstName" class="form-label required" data-lang="firstNameLabel" data-lang-en="First Name" data-lang-th="ชื่อ">ชื่อ</label>
                                        <input type="text" id="firstName" name="first_name" class="form-control" placeholder="ระบุชื่อ" required maxlength="24">
                                    </div>

                                    <!-- ช่องกรอกนามสกุล -->
                                    <div class="col-md-3">
                                        <label for="lastName" class="form-label required" data-lang="lastNameLabel" data-lang-en="Last Name" data-lang-th="นามสกุล">นามสกุล</label>
                                        <input type="text" id="lastName" name="last_name" class="form-control" placeholder="ระบุนามสกุล" required maxlength="24">
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <!-- เบอร์โทร -->
                                    <div class="col-md-3">
                                        <label for="phoneNumber" class="form-label required" data-lang="phoneLabel" data-lang-en="Phone Number" data-lang-th="เบอร์โทร">เบอร์โทร</label>
                                        <input type="tel" id="phoneNumber" name="phone_number" class="form-control" placeholder="ระบุเบอร์โทร" required pattern="[0-9]{10}" maxlength="10">
                                    </div>

                                    <!-- ช่องกรอกวันเดือนปีเกิด -->
                                    <div class="col-md-3">
                                        <label for="date_of_birth" class="form-label" data-lang="dateOfBirthLabel" data-lang-en="Date of Birth" data-lang-th="วันเดือนปีเกิด">วันเดือนปีเกิด</label>
                                        <input type="text" id="date_of_birth" name="date_of_birth" class="form-control" required>
                                    </div>

                                    <!-- ช่องกรอกอายุ -->
                                    <div class="col-md-3">
                                        <label for="age" class="form-label" data-lang="ageLabel" data-lang-en="Age" data-lang-th="อายุ">อายุ</label>
                                        <input type="number" id="age" name="age" class="form-control" placeholder="อายุ" required readonly>
                                    </div>


                                    <!-- ช่องเลือกเพศ -->
                                    <div class="col-md-3">
                                        <label for="gender" class="form-label required" data-lang="genderLabel" data-lang-en="Gender" data-lang-th="เพศ">เพศ</label>
                                        <select id="gender" name="gender" class="form-select" required>
                                            <option value="" data-lang="genderSelect" data-lang-en="Select Gender" data-lang-th="เลือกเพศ">เลือกเพศ</option>
                                            <option value="male" data-lang="genderMale" data-lang-en="Male" data-lang-th="ชาย">ชาย</option>
                                            <option value="female" data-lang="genderFemale" data-lang-en="Female" data-lang-th="หญิง">หญิง</option>
                                            <option value="other" data-lang="genderOther" data-lang-en="Other" data-lang-th="อื่นๆ">อื่นๆ</option>
                                        </select>
                                    </div>

                                    <!-- ช่องเลือกประเภทการนัด -->
                                    <!-- <div class="col-md-3">
                                        <label for="appointmentType" class="form-label required" data-lang="appointmentTypeLabel" data-lang-en="Appointment Type" data-lang-th="ประเภทการนัด">ประเภทการรักษา</label>
                                        <select id="appointmentType" name="appointment_type" class="form-select" required>
                                            <option value="" data-lang="appointmentTypeSelect" data-lang-en="Select Appointment Type" data-lang-th="เลือกประเภทการนัด">เลือกประเภท</option>
                                            <option value="Walk-in" data-lang="appointmentTypeWalkIn" data-lang-en="Walk-in" data-lang-th="ทั่วไป">ทั่วไป</option>
                                            <option value="Pre-booking" data-lang="appointmentTypeFollowUp" data-lang-en="Follow-up" data-lang-th="ติดตามอาการ">ติดตามอาการ</option>
                                        </select>
                                    </div> -->

                                </div>



                            </div>
                        </div>


                        <!-- General Information -->
                        <div class="card mb-3">
                            <div class="card-header" data-lang="generalInfo" data-lang-en="General Information" data-lang-th="ข้อมูลทั่วไป">ข้อมูลทั่วไป</div>
                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="weight" class="form-label" data-lang="weightLabel" data-lang-en="Weight (Kg)" data-lang-th="น้ำหนัก (Kg)">น้ำหนัก (Kg)</label>
                                        <input type="number" id="weight" name="weight" class="form-control" placeholder="ระบุน้ำหนัก" oninput="calculateBMI()">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="height" class="form-label" data-lang="heightLabel" data-lang-en="Height (Cm)" data-lang-th="ส่วนสูง (Cm)">ส่วนสูง (Cm)</label>
                                        <input type="number" id="height" name="height" class="form-control" placeholder="ระบุส่วนสูง" oninput="calculateBMI()">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bmi" class="form-label" data-lang="bmiLabel" data-lang-en="BMI" data-lang-th="BMI">BMI</label>
                                        <input type="text" id="bmi" name="bmi" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bloodType" class="form-label" data-lang="bloodTypeLabel" data-lang-en="Blood Type" data-lang-th="กรุ๊ปเลือด">กรุ๊ปเลือด</label>
                                        <select id="bloodType" name="blood_type" class="form-select">
                                            <option value="" data-lang="selectBloodType" data-lang-en="Please select blood type" data-lang-th="กรุณากรอกกรุ๊ปเลือด">กรุณากรอกกรุ๊ปเลือด</option>
                                            <option value="A" data-lang="A" data-lang-en="A" data-lang-th="A">A</option>
                                            <option value="B" data-lang="B" data-lang-en="B" data-lang-th="B">B</option>
                                            <option value="AB" data-lang="AB" data-lang-en="AB" data-lang-th="AB">AB</option>
                                            <option value="O" data-lang="O" data-lang-en="O" data-lang-th="O">O</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="temperature" class="form-label" data-lang="temperatureLabel" data-lang-en="Temperature (°C)" data-lang-th="อุณหภูมิ (°C)">อุณหภูมิ (°C)</label>
                                        <input type="number" id="temperature" name="temperature" class="form-control" placeholder="ระบุอุณหภูมิ">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="heartRate" class="form-label" data-lang="heartRateLabel" data-lang-en="Heart Rate (/minute)" data-lang-th="อัตราเต้นหัวใจ (/นาที)">อัตราเต้นหัวใจ (/นาที)</label>
                                        <input type="number" id="heartRate" name="heart_rate" class="form-control" placeholder="ระบุอัตราเต้นหัวใจ">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bloodPressure" class="form-label" data-lang="bloodPressureLabel" data-lang-en="Blood Pressure (mmHg)" data-lang-th="ความดันโลหิต (mmHg)">ความดันโลหิต (mmHg)</label>
                                        <input type="text" id="bloodPressure" name="blood_pressure" class="form-control" placeholder="ตัวอย่าง: 120/80">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="symptomDays" class="form-label" data-lang="symptomDaysLabel" data-lang-en="Symptom Duration (days)" data-lang-th="อาการมาแล้ว (วัน)">อาการมาแล้ว (วัน)</label>
                                        <input type="number" id="symptomDays" name="symptom_days" class="form-control" placeholder="ระบุจำนวนวัน">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="smoking" class="form-label" data-lang="smokingLabel" data-lang-en="Smoking" data-lang-th="การสูบบุหรี่">การสูบบุหรี่</label>
                                        <select id="smoking" name="smoking" class="form-select">
                                            <option value="No">ไม่สูบ</option>
                                            <option value="Yes">สูบบุหรี่</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="drinking" class="form-label" data-lang="drinkingLabel" data-lang-en="Drinking" data-lang-th="การดื่มสุรา">การดื่มสุรา</label>
                                        <select id="drinking" name="alcohol" class="form-select">
                                            <option value="No">ไม่ดื่ม</option>
                                            <option value="Yes">ดื่มสุรา</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="allergy" class="form-label" data-lang="allergyLabel" data-lang-en="Drug Allergy" data-lang-th="การแพ้ยา">การแพ้ยา</label>
                                        <select id="allergy" name="drug_allergy" class="form-select">
                                            <option value="No">ไม่มี</option>
                                            <option value="Yes">มี</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="allergyDetails" class="form-label" data-lang="allergyDetailsLabel" data-lang-en="Drug Allergy Details" data-lang-th="รายละเอียดการแพ้ยา">รายละเอียดการแพ้ยา</label>
                                        <input type="text" id="allergyDetails" name="allergy_details" class="form-control" placeholder="ระบุรายละเอียดการแพ้ยา">
                                    </div>
                                </div>
                                <!-- ช่องกรอกข้อมูลประวัติการรักษา -->
                                <div class="col-mb-3">
                                    <label for="medicalHistory" class="form-label" data-lang="medicalHistoryLabel" data-lang-en="Details of Medical History or Symptoms" data-lang-th="ซักอาการเบื้องต้น">ซักอาการเบื้องต้น</label>
                                    <textarea id="medicalHistory" name="additional_details" class="form-control" rows="5" placeholder="ระบุรายละเอียดอาการคร่าวๆเบื้องต้น"></textarea>
                                </div>

                            </div>
                        </div>


                        <!-- Additional Information -->
                        <div class="card mb-3">
                            <div class="card-header" data-lang="additionalInfo" data-lang-en="Additional Information" data-lang-th="ข้อมูลเพิ่มเติม">ข้อมูลเพิ่มเติม</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="department" class="form-label" data-lang="departmentLabel" data-lang-en="Department" data-lang-th="แผนกที่ตรวจ">แผนกที่ตรวจ</label>
                                        <select id="department" name="department" class="form-select" required>
                                            <option value="โสต ศอ นาสิก" selected>โสต ศอ นาสิก</option>
                                        </select>
                                    </div>

                                    <!-- สถานะการตรวจ -->
                                    <div class="col-md-3">
                                        <label for="status" class="form-label required" data-lang="statusLabel" data-lang-en="Examination Status" data-lang-th="สถานะการตรวจ">สถานะการตรวจ</label>
                                        <select id="status" name="status" class="form-select" required>
                                            <option value="">เลือกสถานะ</option>
                                            <option value="Pending">รอตรวจ</option>
                                            <option value="Completed">ตรวจเสร็จ</option>
                                        </select>
                                    </div>
                                    <!-- ความเร่งด่วน -->
                                    <div class="col-md-3">
                                        <label for="urgency" class="form-label required" data-lang="urgencyLabel" data-lang-en="Urgency" data-lang-th="ความเร่งด่วน">ความเร่งด่วน</label>
                                        <select id="urgency" name="urgency" class="form-select" required>
                                            <option value="Normal" style="color: blue;">ปกติ</option>
                                            <option value="Urgent" style="color: red;">เร่งด่วน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--------- ข้อมูลบุคคลที่สามารถติดต่อได้ -->
                            <div class="card-header" data-lang="additionalInfo" data-lang-en="Emergency Contact" data-lang-th="ข้อมูลบุคคลที่สามารถติดต่อได้">ข้อมูลบุคคลที่สามารถติดต่อได้</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- ชื่อ-นามสกุลของบุคคลที่สามารถติดต่อได้ -->
                                    <div class="col-md-4">
                                        <label for="emer_contact_name" class="form-label" data-lang="contactNameLabel" data-lang-en="Emergency Contact Name" data-lang-th="ชื่อ-นามสกุล (บุคคลที่ติดต่อได้)">ชื่อ-นามสกุล (บุคคลที่ติดต่อได้)</label>
                                        <input type="text" id="emer_contact_name" name="emer_contact_name" class="form-control" placeholder="ระบุชื่อ-นามสกุล">
                                    </div>

                                    <!-- เบอร์โทรของบุคคลที่สามารถติดต่อได้ -->
                                    <div class="col-md-4">
                                        <label for="emer_contact_phone" class="form-label" data-lang="contactPhoneLabel" data-lang-en="Emergency Contact Phone" data-lang-th="เบอร์โทร (บุคคลที่ติดต่อได้)">เบอร์โทร (บุคคลที่ติดต่อได้)</label>
                                        <input type="text" id="emer_contact_phone" name="emer_contact_phone" class="form-control" placeholder="ระบุเบอร์โทร" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                    </div>
                                </div>
                            </div>

                        </div>



                        <!-- Buttons -->
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" onclick="clearForm()" data-lang="clearButton" data-lang-en="Clear" data-lang-th="เคลียร์">เคลียร์</button>
                            <button type="submit" class="btn btn-primary" data-lang="saveButton" data-lang-en="Save" data-lang-th="บันทึก">บันทึก</button>
                        </div>

                    </form>
                </div>

                <!-- Search Form Section -->
                <div id="searchFormSection" style="display: none">
                    <div class="card mb-3">
                        <div class="card-header" data-lang="searchFormTitle" data-lang-en="Search Patient" data-lang-th="ค้นหาผู้ป่วย">
                            ค้นหาผู้ป่วย
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- Search By Dropdown -->
                                <div class="col-md-4">
                                    <label for="searchBy" data-lang="searchByLabel" data-lang-en="Search By" data-lang-th="ค้นหาตาม:">ค้นหาตาม:</label>
                                    <select class="form-control" name="searchBy" id="searchBy">
                                        <option value="hn" data-lang="searchHN" data-lang-en="HN" data-lang-th="HN">HN</option>
                                        <option value="full_name" data-lang="searchFullName" data-lang-en="Full Name" data-lang-th="ชื่อ-นามสกุล">ชื่อ-นามสกุล</option>
                                        <option value="id_card" data-lang="searchIDCard" data-lang-en="ID Card" data-lang-th="เลขบัตรประชาชน">เลขบัตรประชาชน</option>
                                    </select>
                                </div>

                                <!-- Search Value Input -->
                                <div class="col-md-4">
                                    <label for="searchValue" data-lang="searchValueLabel" data-lang-en="Search Value" data-lang-th="ค่าที่ต้องการค้นหา:">ค่าที่ต้องการค้นหา:</label>
                                    <input type="text" class="form-control" name="searchValue" id="searchValue" />
                                </div>

                                <!-- Search Button -->
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary mt-4" onclick="searchPatient()" data-lang="searchButton" data-lang-en="Search" data-lang-th="ค้นหา">
                                        ค้นหา
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="EditpatientForm" class="bg-white p-4 rounded shadow-sm" style="display: none; margin-top: 20px;">
                    <h2 data-lang="updateFormTitle" data-lang-en="Update Treatment Information" data-lang-th="อัปเดตข้อมูลการรักษา">ข้อมูลคนไข้</h2>
                    <form method="POST" action="update_patients_appointment.php">
                        <!-- HN Field -->
                        <div class="form-group">
                            <label for="hn" data-lang="hnLabel" data-lang-en="HN" data-lang-th="HN">HN:</label>
                            <input type="text" class="form-control" id="hn" name="hn" readonly>
                        </div>

                        <!-- Name Fields in Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" data-lang="firstNameLabel" data-lang-en="First Name" data-lang-th="ชื่อ">ชื่อ:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" data-lang="lastNameLabel" data-lang-en="Last Name" data-lang-th="นามสกุล">นามสกุล:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" readonly>
                            </div>
                        </div>

                        <!-- Status and Urgency Fields in Row -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status_now" class="form-label required" data-lang="statusLabel" data-lang-en="Status" data-lang-th="สถานะการตรวจ">สถานะการตรวจ</label>
                                <select id="status_now" name="status" class="form-select" disabled>
                                    <option value="" data-lang="selectStatus" data-lang-en="Select Status" data-lang-th="เลือกสถานะ">เลือกสถานะ</option>
                                    <option value="Pending" data-lang="statusPending" data-lang-en="Pending" data-lang-th="รอตรวจ">รอตรวจ</option>
                                    <option value="Checked" data-lang="statusChecked" data-lang-en="Checked" data-lang-th="กำลังตรวจ">กำลังตรวจ</option>
                                    <option value="Completed" data-lang="statusCompleted" data-lang-en="Completed" data-lang-th="ตรวจเสร็จ">ตรวจเสร็จ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="urgency" class="form-label required" data-lang="urgencyLabel" data-lang-en="Urgency" data-lang-th="ความเร่งด่วน">ความเร่งด่วน</label>
                                <select id="urgency" name="urgency" class="form-select" disabled>
                                    <option value="Normal" data-lang="urgencyNormal" data-lang-en="Normal" data-lang-th="ปกติ">ปกติ</option>
                                    <option value="Urgent" data-lang="urgencyUrgent" data-lang-en="Urgent" data-lang-th="เร่งด่วน">เร่งด่วน</option>
                                </select>
                            </div>
                            <!-- ช่องเลือกประเภทการนัด -->
                            <div class="col-md-4">
                                <label for="editAppointmentType" class="form-label required" data-lang="appointmentTypeLabel" data-lang-en="Appointment Type" data-lang-th="ประเภทการนัด">ประเภทการนัด</label>
                                <select id="editAppointmentType" name="appointment_type_edit" class="form-select" disabled>
                                    <option value="" data-lang="appointmentTypeSelect" data-lang-en="Select Appointment Type" data-lang-th="เลือกประเภทการนัด">เลือกประเภทการนัด</option>
                                    <option value="Walk-in" data-lang="appointmentTypeWalkIn" data-lang-en="Walk-in" data-lang-th="ทั่วไป">ทั่วไป</option>
                                    <option value="Pre-booking" data-lang="appointmentTypeFollowUp" data-lang-en="Follow-up" data-lang-th="ติดตามอาการ">ติดตามอาการ</option>
                                </select>
                            </div>
                        </div>

                        <!-- Medical History Input -->
                        <div class="form-group mb-3">
                            <label for="medicalHistoryInput" class="form-label" data-lang="medicalHistoryLabel" data-lang-en="Medical History" data-lang-th="อาการที่คนไข้ต้องการรักษา / ขอคำแนะนำหลังจากรักษา">อาการของคนไข้ที่แจ้งมาเบื้องต้น</label>
                            <textarea id="medicalHistoryInput" name="additional_details" class="form-control" rows="4" readonly></textarea>
                        </div>

                        <!-- Doctor Diagnosis and Treatment Area -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="doctorDiagnosis" class="form-label required" data-lang="doctorDiagnosisLabel" data-lang-en="Doctor Diagnosis" data-lang-th="บันทึกอาการ/การกินยาต่างๆ/คำแนะนำจากทีมแพทย์พยาบาล">การวินิจฉัยจากหมอ</label>
                                <textarea id="doctorDiagnosis" name="doctorDiagnosis" class="form-control" rows="4" readonly></textarea>
                            </div>

                            <div class="col-md-3 text-center align-items-center">
                                <label for="treatment_area" class="form-label required" data-lang="treatmentAreaLabel" data-lang-en="Treatment Area" data-lang-th="สาขาการรักษา">สาขาการรักษา</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="หู" id="ear" disabled>
                                        <label class="form-check-label" for="ear" data-lang="earLabel" data-lang-en="Ear" data-lang-th="หู">หู</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="คอ" id="throat" disabled>
                                        <label class="form-check-label" for="throat" data-lang="throatLabel" data-lang-en="Throat" data-lang-th="คอ">คอ</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="treatment_area" value="จมูก" id="nose" disabled>
                                        <label class="form-check-label" for="nose" data-lang="noseLabel" data-lang-en="Nose" data-lang-th="จมูก">จมูก</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="selectDoctor" class="form-label required" data-lang="selectDoctorLabel" data-lang-en="Select Doctor" data-lang-th="หมอที่ดูแลเคส">หมอที่ดูแลเคส</label>
                                <select id="selectDoctor" name="selectDoctor" class="form-select" disabled>
                                    <option value="" data-lang="selectDoctorOption" data-lang-en="Select a Doctor" data-lang-th="เลือกหมอ">เลือกหมอ</option>
                                    <!-- รายชื่อหมอจะถูกเติมโดย JavaScript -->
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="appointment_date">วันนัดหมาย:</label>
                                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" readonly>
                            </div>
                        </div>

                    </form>
                </div>





            </div>
        </div>



    </div>



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- CSS for jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- ภาษาไทย -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.12.1/ui/i18n/datepicker-th.js"></script>

    <script>
        $(document).ready(function() {
            $("#date_of_birth").datepicker({
                dateFormat: "yy-mm-dd", // รูปแบบวันที่เป็น "yyyy-mm-dd"
                changeMonth: true,
                changeYear: true,
                yearRange: "1900:+10",
                onSelect: function(dateText) {
                    calculateAge(dateText);
                }
            });
        });

        function calculateAge(dobText) {
            var dob = new Date(dobText);
            if (isNaN(dob)) return;

            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var monthDiff = today.getMonth() - dob.getMonth();
            var dayDiff = today.getDate() - dob.getDate();

            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                age--; // ถ้ายังไม่ถึงวันเกิดในปีนี้ให้ลดอายุลง 1 ปี
            }

            $("#age").val(age); // ใส่อายุในช่องอายุ
        }
    </script>



    <script>
        document.getElementById("phoneNumber").addEventListener("input", function() {
            this.value = this.value.replace(/[^0-9]/g, ""); // กรองเฉพาะตัวเลข
        });
    </script>


    <script>
        document.getElementById("checkPatient").addEventListener("click", function() {
            let idCard = document.getElementById("id_card").value.trim();

            if (idCard.length !== 13) {
                Swal.fire({
                    icon: "warning",
                    title: "ข้อมูลไม่ถูกต้อง",
                    text: "กรุณากรอกเลขบัตรประชาชนให้ครบ 13 หลัก",
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            fetch(`./config/check_patient.php?id_card=${idCard}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "found") {
                        document.getElementById("firstName").value = data.first_name || "";
                        document.getElementById("lastName").value = data.last_name || "";
                        document.getElementById("phoneNumber").value = data.phone_number || "";
                        document.getElementById("date_of_birth").value = data.date_of_birth || "";
                        document.getElementById("age").value = data.age || "";
                        document.getElementById("gender").value = data.gender || "";
                        document.getElementById("weight").value = data.weight || "";
                        document.getElementById("height").value = data.height || "";
                        document.getElementById("bmi").value = data.bmi || "";
                        document.getElementById("bloodType").value = data.blood_type || "";
                        document.getElementById("temperature").value = data.temperature || "";
                        document.getElementById("heartRate").value = data.heart_rate || "";
                        document.getElementById("bloodPressure").value = data.blood_pressure || "";
                        document.getElementById("symptomDays").value = data.symptom_days || "";
                        document.getElementById("smoking").value = data.smoking || "";
                        document.getElementById("drinking").value = data.alcohol || "";
                        document.getElementById("allergy").value = data.drug_allergy || "";
                        document.getElementById("allergyDetails").value = data.allergy_details || "";
                        document.getElementById("medicalHistory").value = data.additional_details || "";
                        document.getElementById("emer_contact_name").value = data.emer_contact_name || "";
                        document.getElementById("emer_contact_phone").value = data.emer_contact_phone || "";

                        Swal.fire({
                            icon: "success",
                            title: "โหลดข้อมูลสำเร็จ!",
                            confirmButtonText: 'ตกลง',
                            html: `
                            <p>บัตรประชาชน: <b>${data.id_card}</b></p>
                            <p>พบข้อมูลของคุณ</p>
                            <p>ชื่อ: <b>${data.first_name} ${data.last_name}</b></p>`,
                        });

                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "ไม่พบข้อมูลผู้ป่วย",
                            text: "ยังไม่มีการบันทึกข้อมูลผู้ป่วยนี้ในระบบ",
                            confirmButtonText: 'ตกลง'
                        });
                    }
                })
                .catch(error => {
                    console.error("เกิดข้อผิดพลาด:", error);
                    Swal.fire({
                        icon: "error",
                        title: "เกิดข้อผิดพลาด",
                        text: "ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง",
                        confirmButtonText: 'ตกลง'
                    });
                });
        });
    </script>




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

            fetch("./config/search_patient.php", {
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

        // function updatePatient() {
        //     const hn = document.getElementById("hn").value;
        //     const status = document.getElementById("status_now").value;
        //     const urgency = document.getElementById("urgency").value;
        //     const medicalHistory = document.getElementById("medicalHistoryInput").value;
        //     const doctorDiagnosis = document.getElementById("doctorDiagnosis").value;
        //     const treatmentArea = document.querySelector('input[name="treatment_area"]:checked')?.value;
        //     const appointmentDate = document.getElementById("appointment_date").value;
        //     const doctorId = document.getElementById("selectDoctor").value; // ดึงค่า doctor_id ที่ผู้ใช้เลือก
        //     const appointmentType = document.getElementById("editAppointmentType").value; // ดึงประเภทการนัด

        //     const formData = new FormData();
        //     formData.append("hn", hn);
        //     formData.append("status", status);
        //     formData.append("urgency", urgency);
        //     formData.append("additional_details", medicalHistory); // เพิ่ม medicalHistory
        //     formData.append("doctorDiagnosis", doctorDiagnosis);
        //     formData.append("treatment_area", treatmentArea);
        //     formData.append("appointment_date", appointmentDate);
        //     formData.append("doctor_id", doctorId); // เพิ่ม doctor_id
        //     formData.append("appointment_type_edit", appointmentType); // เพิ่ม appointment_type

        //     // ส่งคำร้องขอแบบ POST ไปยัง PHP
        //     fetch("update_patients_appointment.php", {
        //             method: "POST",
        //             body: formData
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             // เช็คว่ามี error ในข้อมูลหรือไม่
        //             if (data.error) {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'เกิดข้อผิดพลาด',
        //                     text: data.error,
        //                 });
        //             } else if (data.message === "ข้อมูลได้รับการอัปเดตแล้ว") {
        //                 Swal.fire({
        //                     icon: 'success',
        //                     title: 'อัปเดตข้อมูลสำเร็จ',
        //                     text: 'ข้อมูลของผู้ป่วยได้รับการอัปเดตแล้ว',
        //                 });
        //             }
        //         })
        //         .catch(error => {
        //             console.error("Error:", error);
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'เกิดข้อผิดพลาด',
        //                 text: 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์',
        //             });
        //         });
        // }
    </script>


    <script>
        function toggleForm(sectionId, buttonId) {
            // รายชื่อฟอร์มทั้งหมด
            const forms = ['patientFormSection', 'searchFormSection'];
            const buttons = ['patientButton', 'searchButton'];

            // ซ่อนฟอร์มทั้งหมดและตั้งค่าปุ่มเป็นปกติ
            forms.forEach(id => {
                document.getElementById(id).style.display = 'none';
            });
            buttons.forEach(id => {
                document.getElementById(id).classList.remove('btn-primary');
                document.getElementById(id).classList.add('btn-secondary');
            });

            // แสดงฟอร์มที่เลือก
            document.getElementById(sectionId).style.display = 'block';
            // เปลี่ยนปุ่มที่เลือกเป็น active
            document.getElementById(buttonId).classList.remove('btn-secondary');
            document.getElementById(buttonId).classList.add('btn-primary');
        }
    </script>

    <script>
        function toggleForm(formId, buttonId) {
            // ซ่อนฟอร์มทั้งหมด
            document.getElementById('patientFormSection').style.display = 'none';
            document.getElementById('searchFormSection').style.display = 'none';
            document.getElementById('EditpatientForm').style.display = 'none'; // ซ่อนฟอร์ม EditpatientForm

            // แสดงฟอร์มที่ต้องการ
            document.getElementById(formId).style.display = 'block';

            // เปลี่ยนสีปุ่มที่เลือก
            document.getElementById('patientButton').classList.remove('btn-primary');
            document.getElementById('patientButton').classList.add('btn-secondary');

            document.getElementById('searchButton').classList.remove('btn-primary');
            document.getElementById('searchButton').classList.add('btn-secondary');

            // เปลี่ยนปุ่มที่ถูกเลือกเป็นสีฟ้า
            document.getElementById(buttonId).classList.remove('btn-secondary');
            document.getElementById(buttonId).classList.add('btn-primary');
        }
    </script>



    <script>
        // ตัวอย่างการส่งข้อมูลผ่าน AJAX
        document.querySelector('#patientForm').addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการโหลดหน้าใหม่
            const formData = new FormData(this);
            const idCard = formData.get('id_card'); // ดึงค่า id_card จากฟอร์ม

            // ตรวจสอบความยาวของเลขบัตรประชาชน
            if (idCard.length !== 13) {
                Swal.fire({
                    icon: 'error',
                    title: 'เลขบัตรประชาชนต้องมีความยาว 13 ตัว',
                    text: 'กรุณากรอกเลขบัตรประชาชนให้ครบ 13 ตัว'
                });
                return; // หยุดการส่งข้อมูลไปยังเซิร์ฟเวอร์
            }

            fetch('config/save_opd_patient.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response Data:', data); // ตรวจสอบข้อมูลที่ส่งกลับมา
                    if (data.success) {
                        if (!data.action || data.action === 'insert') {
                            // แจ้งเตือนเมื่อเพิ่มข้อมูลใหม่
                            Swal.fire({
                                icon: 'success',
                                title: 'เพิ่มข้อมูลสำเร็จ!',
                                html: `
                                <p>Hospital Number (HN): <b>${data.hn}</b></p>
                                <p>Patient ID: <b>${data.patientID}</b></p>
                                <p>(เพิ่มข้อมูลคนไข้ใหม่เรียบร้อยแล้ว)</p>
                            `
                            }).then(() => {
                                window.location = 'opd.php'; // เปลี่ยน URL ตามที่ต้องการ
                            });
                        } else if (data.action === 'update') {
                            // แจ้งเตือนเมื่ออัปเดตข้อมูล
                            Swal.fire({
                                icon: 'success',
                                title: 'อัปเดตข้อมูลสำเร็จ!',
                                text: 'ข้อมูลผู้ป่วยถูกอัปเดตเรียบร้อยแล้ว'
                            }).then(() => {
                                window.location = 'opd.php';
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                    console.error('Error:', error);
                });
        });
    </script>


    <script>
        function calculateBMI() {
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const height = parseFloat(document.getElementById('height').value) || 0;
            if (height > 0) {
                const bmi = (weight / ((height / 100) ** 2)).toFixed(2);
                document.getElementById('bmi').value = bmi;
            } else {
                document.getElementById('bmi').value = '';
            }
        }

        function clearForm() {
            document.getElementById('patientForm').reset();
            document.getElementById('bmi').value = '';
        }
    </script>






    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>