<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $whatsapp = htmlspecialchars($_POST['whatsapp']);
    $country_code = htmlspecialchars($_POST['country_code']);
    $dob = htmlspecialchars($_POST['dob']);
    $password = htmlspecialchars($_POST['password']);

    // Validate the WhatsApp number
    $countryValidationRules = [
        "+237" => ["length" => 9, "start" => "6"],
        "+234" => ["length" => 10],
        "+235" => ["length" => 8],
        "+225" => ["length" => 8],
        "+233" => ["length" => 9],
        "+241" => ["length" => 7],
        "+27" => ["length" => 9],
        "+228" => ["length" => 8],
        "+229" => ["length" => 8],
        "+221" => ["length" => 9],
        "+236" => ["length" => 8],
        "+240" => ["length" => 9],
        "+243" => ["length" => 9],
        "+33" => ["length" => 9],
        "+1" => ["length" => 10],
        "+32" => ["length" => 9],
        "+86" => ["length" => 11],
        "+44" => ["length" => 10],
        "+41" => ["length" => 9],
        "+255" => ["length" => 9],
        "+91" => ["length" => 10],
        // Add more country codes if needed
    ];

    if (isset($countryValidationRules[$country_code])) {
        $rule = $countryValidationRules[$country_code];
        if (strlen($whatsapp) !== $rule['length'] || (isset($rule['start']) && !str_starts_with($whatsapp, $rule['start']))) {
            die("Invalid WhatsApp number for the selected country code.");
        }
    } else {
        die("Invalid country code.");
    }

    // Check if username, email, or WhatsApp number already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ? OR whatsapp = ?");
    $stmt->bind_param("sss", $username, $email, $whatsapp);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        die("Username, email, or WhatsApp number already exists.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert user data into the database
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, whatsapp, country_code, dob, password, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssssss", $username, $email, $whatsapp, $country_code, $dob, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get the inserted user ID
        $stmt->close();
        $conn->close();
        header("Location: signin_success.html"); // Redirect to a success page
        exit;
    } else {
        die("Failed to create account. Please try again.");
    }
}
?>
