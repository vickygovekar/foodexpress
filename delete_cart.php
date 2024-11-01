<?php
// Database configuration
$servername = "localhost"; // Typically localhost for XAMPP
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP (usually empty)
$dbname = "vicky"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to delete all data from the cart table
$sql = "DELETE FROM cart";

if ($conn->query($sql) === TRUE) {
    echo "";
} else {
    echo "Error deleting records: " . $conn->error;
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b8d4f0;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #2a3f40;
            padding: 10px 0;
            text-align: center;
            color: white;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .confirm-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2a3f40;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .confirm-btn:hover {
            background-color: #3a5456;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="homepage.html">Home</a></li>
            <li><a href="cart.html">Cart</a></li>
            <li><a href="settings.html">setting</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your order!</p>
        <p>Your order will be delivered within 30-45 minutes.</p>
        <a href="homepage.html" class="confirm-btn">Return to Home</a>
    </div>
</body>
</html>