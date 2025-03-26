<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "finalpro"; // ชื่อฐานข้อมูลของคุณ


$servername = "localhost";
$username = "entcenterl_pj_final";
$password = "New_Film_2024";
$dbname = "entcenterl_finalpro"; // ชื่อฐานข้อมูลของคุณ


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
