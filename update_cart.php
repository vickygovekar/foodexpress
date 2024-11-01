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

$data = json_decode(file_get_contents("php://input"), true);
$item_id = $data['id'] ?? null;
$new_quantity = $data['quantity'] ?? null;

if (!$item_id || !$new_quantity) {
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit();
}

$query = "UPDATE cart SET quantity = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $new_quantity, $item_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Quantity updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating quantity: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>