<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voting_system2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Debugging: Check received POST data
echo '<pre>';
print_r($_POST);
print_r($_FILES);
echo '</pre>';

// Function to generate a random alphanumeric ID
function generate_unique_id($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Check if necessary POST data is set
if (isset($_POST['voteName'], $_POST['voteDescription'], $_POST['voteDeadline'], $_FILES['candidateImages'])) {
    // Prepare and bind
    $stmt_vote = $conn->prepare("INSERT INTO Votes (vote_id, vote_name, vote_description, vote_deadline, user_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_vote) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt_vote->bind_param("ssssi", $vote_id, $voteName, $voteDescription, $voteDeadline, $user_id);

    // Generate unique vote ID
    $vote_id = generate_unique_id();

    // Set parameters and execute
    $voteName = $_POST['voteName'];
    $voteDescription = $_POST['voteDescription'];
    $voteDeadline = $_POST['voteDeadline'];
    $stmt_vote->execute();
    $stmt_vote->close();

    // Prepare and bind for candidates
    $stmt_candidate = $conn->prepare("INSERT INTO Candidates (vote_id, candidate_name, candidate_age, candidate_position, candidate_description, candidate_image) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt_candidate) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt_candidate->bind_param("isisss", $vote_id, $candidateName, $candidateAge, $candidatePosition, $candidateDescription, $candidateImage);

    $targetDir = "uploads/";

    foreach ($_FILES['candidateImages']['tmp_name'] as $index => $tmp_name) {
        $candidateName = $_POST['candidateNames'][$index];
        $candidateAge = $_POST['candidateAges'][$index];
        $candidatePosition = $_POST['candidatePositions'][$index];
        $candidateDescription = $_POST['candidateDescriptions'][$index];

        // Handle the image upload
        $candidateImageName = basename($_FILES["candidateImages"]["name"][$index]);
        $targetFilePath = $targetDir . $candidateImageName;
        move_uploaded_file($_FILES["candidateImages"]["tmp_name"][$index], $targetFilePath);

        $candidateImage = file_get_contents($targetFilePath);
        $candidateImage = base64_encode($candidateImage); // Encode image to store as base64

        $stmt_candidate->execute();
    }

    $stmt_candidate->close();
    $conn->close();
} else {
    die("Required data not received.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote Created</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="images/logot.jpg" alt="tVote Logo">
        <h1>Vote Created Successfully</h1>
    </header>
    <main>
        <p>Your vote has been created successfully. Your unique vote ID is: <strong><?php echo $vote_id; ?></strong></p>
        <p>Please save this ID for future reference.</p>
        <a href="display_votes.php">View Votes</a>
    </main>
</body>
</html>
