<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'vicky');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in (using username instead of user_id)
if (!isset($_SESSION['username'])) {
    echo json_encode(["message" => "User not logged in. Please log in to add items to your cart."]);
    exit();
}

// Get user_id from username
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["message" => "User not found."]);
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Retrieve item data from JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if data was received and print it for debugging
if (!$data) {
    echo json_encode(["message" => "No data received."]);
    exit();
}

$item_name = $data['name'] ?? null;
$price = $data['price'] ?? null;

// Check for missing fields and display what was received
if (!$item_name || !$price) {
    echo json_encode([
        "message" => "Invalid item data.",
        "received_data" => $data  // Display received data for debugging
    ]);
    exit();
}

$quantity = $data['quantity'] ?? 1;

// Check if item already exists in the cart
$query = "SELECT * FROM cart WHERE user_id = ? AND item_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $item_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity if item already exists in the cart
    $query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND item_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $quantity, $user_id, $item_name);
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Quantity updated in cart"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error updating cart: " . $stmt->error
        ]);
    }
} else {
    // Add a new item to the cart
    $query = "INSERT INTO cart (user_id, item_name, price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isdi", $user_id, $item_name, $price, $quantity);
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Item added to cart successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error adding item to cart: " . $stmt->error
        ]);
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>