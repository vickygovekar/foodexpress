<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vicky");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Confirm password before deleting account
    $password = $_POST['password'];

    // Verify password
    $sql = "SELECT * FROM users WHERE Username = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Password is correct, proceed with account deletion
        $sql = "DELETE FROM users WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        
        if ($stmt->execute()) {
            session_destroy();
            echo "<script>alert('Account deleted successfully. Redirecting to home page...'); window.location.href='homepage.html';</script>";
        } else {
            echo "<script>alert('Error deleting account: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Incorrect password. Account deletion failed.'); window.location.href='delete.php';</script>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b8d4f0;
            margin: 0;
            padding: 0;
        }

        /* Navigation Bar */
        nav {
            background-color: #2a3f40;
            padding: 10px 0;
            text-align: center;
            color: white;
            position: relative;
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

        /* Logo */
        .logo {
            position: absolute;
            top: 5px;
            left: 30px;
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo h1 {
            color: white;
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        /* Delete Account Form */
        .delete-account-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .delete-account-container h2 {
            text-align: center;
            color: #2a3f40;
            margin-bottom: 30px;
        }

        .delete-account-container form {
            display: flex;
            flex-direction: column;
        }

        .delete-account-container label {
            margin-top: 10px;
            color: #2a3f40;
        }

        .delete-account-container input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .delete-account-container input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .delete-account-container input[type="submit"]:hover {
            background-color: #ff3333;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2a3f40;
            text-decoration: none;
        }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="logo">
            <img src="food.jpg" alt="Food Express Logo">
            <h1>Food Express</h1>
        </div>
        <ul>
            <li><a href="homepage.html ">Home</a></li>
            <li><a href="cart.html">Cart</a></li>
            <li><a href="setting.html">Settings</a></li>
        </ul>
    </nav>

    <!-- Delete Account Form -->
    <div class="delete-account-container">
        <h2>Delete Account</h2>
        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
        <form method="post" action="">
            <label for="password">Enter your password to confirm:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Delete Account">
        </form>
        <a href="setting.html" class="back-link">Back to Settings</a>
    </div>
</body>
</html>