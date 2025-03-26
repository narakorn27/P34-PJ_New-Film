<?php
include 'connect_database.php';
session_start();

header('Content-Type: application/json'); // ✅ บังคับให้ Response เป็น JSON

if (!isset($_SESSION['id'])) {
    error_log("❌ Error: User not logged in"); // Debug Log
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);
$fcm_token = $data['fcm_token'] ?? null;

if ($fcm_token) {
    $sql = "UPDATE patients_login SET fcm_token = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("❌ Error preparing statement: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Database error"]);
        exit();
    }
    
    $stmt->bind_param("ss", $fcm_token, $user_id);
    if ($stmt->execute()) {
        error_log("✅ Token updated for user ID: $user_id");
        echo json_encode(["status" => "success", "message" => "Token updated"]);
    } else {
        error_log("❌ Error executing statement: " . $stmt->error);
        echo json_encode(["status" => "error", "message" => "Failed to update token"]);
    }
} else {
    error_log("❌ No token received");
    echo json_encode(["status" => "error", "message" => "No token received"]);
}
?>
