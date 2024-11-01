<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'vicky');

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

$username = $_SESSION['username'];

// Get user_id from username
$query = "SELECT id FROM users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "User not found."]);
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch cart items for the user
$query = "SELECT id, item_name as name, price, quantity FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode($cart_items);

$stmt->close();
$conn->close();
?>