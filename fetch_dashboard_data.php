<?php
$servername = "localhost"; // Replace with your database server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "tvote"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get total users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsersRow = $totalUsersResult->fetch_assoc();
$totalUsers = $totalUsersRow['total_users'];

// Query to get total votes
$totalVotesQuery = "SELECT COUNT(*) AS total_votes FROM votes"; // Replace with your votes table name
$totalVotesResult = $conn->query($totalVotesQuery);
$totalVotesRow = $totalVotesResult->fetch_assoc();
$totalVotes = $totalVotesRow['total_votes'];

// Query to get active votes
$activeVotesQuery = "SELECT COUNT(*) AS active_votes FROM votes WHERE end_time > NOW()"; // Replace with your votes table name and appropriate end time field
$activeVotesResult = $conn->query($activeVotesQuery);
$activeVotesRow = $activeVotesResult->fetch_assoc();
$activeVotes = $activeVotesRow['active_votes'];

// Query to get user data
$userDataQuery = "SELECT id, username, email, whatsapp, country_code, dob FROM users";
$userDataResult = $conn->query($userDataQuery);
$users = array();
while ($row = $userDataResult->fetch_assoc()) {
    $users[] = $row;
}

// Query to get vote data
$voteDataQuery = "SELECT id, title, description, begin_time, end_time, result_time FROM votes";
$voteDataResult = $conn->query($voteDataQuery);
$votes = array();
while ($row = $voteDataResult->fetch_assoc()) {
    $votes[] = $row;
}

$response = array(
    'totalUsers' => $totalUsers,
    'totalVotes' => $totalVotes,
    'activeVotes' => $activeVotes,
    'users' => $users,
    'votes' => $votes
);

echo json_encode($response);

$conn->close();
?>
