<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'vicky');

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Get the data from the request
$data = json_decode(file_get_contents("php://input"), true);
$item_id = $data['id'] ?? null;

if (!$item_id) {
    echo json_encode(["success" => false, "message" => "Invalid item ID"]);
    exit();
}

// Get user_id from username
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Delete the item from cart
$query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $item_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Item removed successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Item not found or unauthorized"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error removing item: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>