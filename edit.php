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
    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $new_username = $_POST['username'];
    $password = $_POST['password'];

    // Update user information
    $sql = "UPDATE users SET Fname=?, Email=?, Username=?";
    $params = array($fname, $email, $new_username);
    $types = "sss";

    // Only update password if a new one is provided
    if (!empty($password)) {
        $sql .= ", Password=?";
        $params[] = $password;
        $types .= "s";
    }

    $sql .= " WHERE Username=?";
    $params[] = $username;
    $types .= "s";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully');</script>";
        $_SESSION['username'] = $new_username; // Update session with new username
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Fetch user data
$sql = "SELECT Fname, Email, Username FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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

        /* Edit Profile Form */
        .edit-profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .edit-profile-container h2 {
            text-align: center;
            color: #2a3f40;
            margin-bottom: 30px;
        }

        .edit-profile-container form {
            display: flex;
            flex-direction: column;
        }

        .edit-profile-container label {
            margin-top: 10px;
            color: #2a3f40;
        }

        .edit-profile-container input[type="text"],
        .edit-profile-container input[type="email"],
        .edit-profile-container input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-profile-container input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #2a3f40;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .edit-profile-container input[type="submit"]:hover {
            background-color: #3a5456;
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
            <li><a href="homepage.html">Home</a></li>
            <li><a href="cart.html">Cart</a></li>
            <li><a href="setting.html">Settings</a></li>
        </ul>
    </nav>

    <!-- Edit Profile Form -->
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form method="post" action="">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" value="<?php echo $user['Fname']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['Email']; ?>" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['Username']; ?>" required>

            <label for="password">New Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password">

            <input type="submit" value="Update Profile">
        </form>
        <a href="setting.html" class="back-link">Back to Settings</a>
    </div>

</body>
</html>