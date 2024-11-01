<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'vicky');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Get user ID
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    echo json_encode(['success' => false, 'message' => 'User  not found']);
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Get the cart items for the user
$stmt = $conn->prepare("SELECT item_id, quantity FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

// Prepare to store order details
$order_items = [];
$total_price = 0;

// Fetch each cart item and calculate total price
while ($item = $cart_items->fetch_assoc()) {
    $item_id = $item['item_id'];
    $quantity = $item['quantity'];

    // Get item details
    $item_stmt = $conn->prepare("SELECT name, price FROM items WHERE id = ?");
    $item_stmt->bind_param("i", $item_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();

    if ($item_result->num_rows) {
        $item_data = $item_result->fetch_assoc();
        $item_name = $item_data['name'];
        $item_price = $item_data['price'];

        // Calculate total price
        $total_price += $item_price * $quantity;

        // Store order item details
        $order_items[] = [
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $quantity
        ];
    }
}

// Generate a unique order ID (you might want to use a more robust method)
$order_id = uniqid();

// Here you would typically insert the order into an orders table
// For example:
// $stmt = $conn->prepare("INSERT INTO orders (user_id, order_id, total_price) VALUES (?, ?, ?)");
// $stmt->bind_param("isd", $user_id, $order_id, $total_price);
// $stmt->execute();

// Clear the cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Return order details
echo json_encode([
    'success' => true,
    'order_id' => $order_id,
    'order_items' => $order_items,
    'total_price' => $total_price
]);

$conn->close();
?>