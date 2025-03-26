<?php include 'mid_string.php'; ?>



<body>
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <!---------------------------- CONTRAINER DOCTOR ------------------------------------>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg1"><i class="fa fa-stethoscope" aria-hidden="true"></i></span>
                        <!----------------------------นับจำนวน USER ในระบบ ------------------------------------>
                        <div class="dash-widget-info text-right">

                            <?php
                            $sql = "SELECT COUNT(*) as doctos FROM  medical_staff";
                            $query = $conn->prepare($sql);
                            $query->execute();
                            $fetch = $query->fetch();

                            ?>
                            <h3><?= $fetch['doctos'] ?></h3>
                            <!----------------------------นับจำนวน USER ในระบบ ------------------------------------>
                            <span class="widget-title1" data-lang="widget-title1" data-lang-en="Doctors" data-lang-th="บุคลากรทางการแพทย์">
                                บุคลากรทางการแพทย์ <i class="fa fa-check" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <!---------------------------- CONTRAINER DOCTOR ------------------------------------>


                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg2"><i class="fa fa-user-o"></i></span>
                        <div class="dash-widget-info text-right">
                            <?php
                            // Query เพื่อดึงจำนวนผู้ป่วยจากตาราง patients_info
                            $sql = "SELECT COUNT(*) as patients FROM patients_info";
                            $query = $conn->prepare($sql);
                            $query->execute();
                            $fetch = $query->fetch();
                            ?>
                            <h3><?= $fetch['patients'] ?></h3>
                            <span class="widget-title2" data-lang="widget-title" data-lang-en="Patients" data-lang-th="ผู้ป่วย">
                                ผู้ป่วย <i class="fa fa-check" aria-hidden="true"></i>
                            </span>

                        </div>
                    </div>
                </div>


                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg3"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                        <div class="dash-widget-info text-right">
                            <?php
                            // Query เพื่อดึงจำนวนผู้ป่วยที่มี appointment_type = 'Pre-booking'
                            $sql = "SELECT COUNT(*) as attend FROM patients_info WHERE appointment_type = 'Pre-booking'";
                            $query = $conn->prepare($sql);
                            $query->execute();
                            $fetch = $query->fetch();
                            ?>
                            <h3><?= $fetch['attend'] ?></h3>
                            <span class="widget-title3" data-lang="widget-title3" data-lang-en="Attend" data-lang-th="ติดตามอาการ">
                                ติดตามอาการ <i class="fa fa-check" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <div class="dash-widget">
                        <span class="dash-widget-bg4"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                        <div class="dash-widget-info text-right">
                            <?php
                            // Query เพื่อนับจำนวนการนัดหมายที่มี status เป็น 'confirmed' หรือ 'rescheduled'
                            $sql = "SELECT COUNT(*) as total_appointments FROM appointments WHERE status IN ('confirmed', 'rescheduled')";
                            $query = $conn->prepare($sql);
                            $query->execute();
                            $fetch = $query->fetch();
                            ?>
                            <h3><?= $fetch['total_appointments'] ?></h3>
                            <span class="widget-title4">นัดหมายแล้ว <i class="fa fa-check" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title d-inline-block">ผู้ป่วยใหม่</h4>
                            <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor'): ?>
                                <a href="patients.php" class="btn btn-primary float-right">ดูทั้งหมด</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-block">
                            <div class="table-responsive">
                                <table class="table mb-0 new-patient-table">
                                    <tbody>
                                        <?php
                                        // SQL Query ดึงข้อมูลผู้ป่วยในช่วงหนึ่งเดือนล่าสุด
                                        $sql = "
                                SELECT first_name, last_name, additional_details, age, status
                                FROM patients_info
                                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                                ORDER BY created_at DESC
                                LIMIT 6
                            ";
                                        $query = $conn->prepare($sql);
                                        $query->execute();
                                        $patients = $query->fetchAll();

                                        // แสดงข้อมูลผู้ป่วย
                                        foreach ($patients as $patient) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <img width="28" height="28" class="rounded-circle" src="assets/img/user.jpg" alt="">
                                                    <h2><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h2>
                                                </td>
                                                <td><?= htmlspecialchars($patient['additional_details']) ?></td>
                                                <td><?= htmlspecialchars($patient['age']) ?> ปี</td>
                                                <td>
                                                    <?php
                                                    // กำหนดสีของปุ่มและสถานะภาษาไทย
                                                    $statusClass = '';
                                                    $statusText = '';

                                                    switch ($patient['status']) {
                                                        case 'Pending':
                                                            $statusClass = 'btn-primary-one';
                                                            $statusText = 'รอเข้าตรวจ';
                                                            break;
                                                        case 'Checked':
                                                            $statusClass = 'btn-primary-two';
                                                            $statusText = 'ตรวจสอบแล้ว';
                                                            break;
                                                        case 'Completed':
                                                            $statusClass = 'btn-primary-three';
                                                            $statusText = 'เสร็จสิ้น';
                                                            break;
                                                    }
                                                    ?>
                                                    <button class="btn btn-primary <?= $statusClass ?> float-right">
                                                        <?= htmlspecialchars($statusText) ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>






</body>