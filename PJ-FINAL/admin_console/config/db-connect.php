<?php
$host     = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'finalpro';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Connection failed, display an alert message
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful
    echo "Connected successfully";
}
?>
