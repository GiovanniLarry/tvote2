<?php
$host = 'localhost'; // Database host
$db = 'tvote'; // Database name
$user = 'root'; // Database user
$pass = ''; // Database password

// Create a new PDO instance
try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
