<?php
session_start();

// Database connection settings
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "tvote";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the username and password from the form
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and bind
$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $hashed_password);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    if (password_verify($pass, $hashed_password)) {
        // Password is correct, start a session and redirect
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $user;
        header("Location: login_success.html");
        exit();
    } else {
        // Password is incorrect
        header("Location: login.html?error=invalid_password");
        exit();
    }
} else {
    // Username does not exist
    header("Location: login.html?error=no_user");
    exit();
}

// Close connections
$stmt->close();
$conn->close();
?>
