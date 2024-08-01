<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $preferredContactMethod = htmlspecialchars($_POST['preferredContactMethod']);

    // Validate email and phone number
    if (!isset($_SESSION['signup_email']) || !isset($_SESSION['signup_phone'])) {
        echo "<script>alert('Session data is missing. Please sign up first.'); window.history.back();</script>";
        exit;
    }

    $signupEmail = $_SESSION['signup_email'];
    $signupPhone = $_SESSION['signup_phone'];

    if ($email !== $signupEmail || $phone !== $signupPhone) {
        echo "<script>alert('Email or Phone number does not match the one used during signup.'); window.history.back();</script>";
        exit;
    }

    // Database connection
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname = "tvote";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, phone, subject, message, preferred_contact_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $phone, $subject, $message, $preferredContactMethod);

    // Execute the query
    if ($stmt->execute()) {
        // Display success message
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Contact Form Submission</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f0f0f0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .message-container {
                    background-color: white;
                    padding: 2em;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .message-container h2 {
                    color: #333;
                }
                .message-container p {
                    color: #555;
                }
            </style>
        </head>
        <body>
            <div class='message-container'>
                <h2>Thank you for contacting us!</h2>
                <p>We have received your message and will get back to you shortly.</p>
            </div>
        </body>
        </html>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
