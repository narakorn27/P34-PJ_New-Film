<?php
header('Content-Type: application/json');

// Database connection
include('./config/connect_database.php');

$sql = "
    SELECT 
        a.appointment_date,
        a.treatment_area,
        a.appointment_id,
        p.status
    FROM 
        appointments a
    LEFT JOIN 
        patients_info p 
    ON 
        a.patient_id = p.id
";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = date('Y-m-d', strtotime($row['appointment_date'])); // แปลงวันที่
        $time = date('H:i', strtotime($row['appointment_date'])); // แปลงเวลา
        $appointment_id = substr($row['appointment_id'], -4); // เลขท้าย 4 ตัว
        $status = $row['status'] ?? 'Pending'; // Default 'Pending'

        $events[$date][] = [
            'time' => $time,
            'treatment_area' => $row['treatment_area'],
            'appointment_id' => $appointment_id,
            'status' => $status
        ];
    }
}

echo json_encode($events);
$conn->close();
?>
