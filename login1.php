<?php
session_start(); // Start the session at the beginning

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vicky";

// Establish the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Check if the request method is POST (i.e., form was submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user inputs
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a parameterized query to prevent SQL injection
    $sql = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        $dbPassword = $row['Password'];

        // Verify the entered password matches the stored password
        if ($password === $dbPassword) {
            // Correct password, set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['id']; // Use unique ID
            $_SESSION['username'] = $row['Username'];
            $_SESSION['fullname'] = $row['Fname'];

            // Redirect to the homepage
            header("Location: homepage.html");
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "Username does not exist. Please check and try again.";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

// If there's an error message, display it
if (!empty($error_message)) {
    echo "<script>alert('$error_message');</script>";
}
?>