<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "create_vote";
$vote_id = $_GET['vote_id'];
$user_id = 1; // Replace with actual user ID from session or authentication

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_vote = "SELECT vote_name, vote_deadline FROM Votes WHERE vote_id = ?";
$stmt_vote = $conn->prepare($sql_vote);
$stmt_vote->bind_param("i", $vote_id);
$stmt_vote->execute();
$result_vote = $stmt_vote->get_result();
$vote = $result_vote->fetch_assoc();
$stmt_vote->close();

$sql_candidates = "SELECT c.candidate_id AS id, c.candidate_name AS name, c.candidate_age AS age, 
                   c.candidate_position AS position, c.candidate_description AS description, 
                   c.candidate_image AS image, 
                   CASE WHEN uv.user_id IS NOT NULL THEN 1 ELSE 0 END AS voted
                   FROM Candidates c
                   LEFT JOIN UserVotes uv ON c.candidate_id = uv.candidate_id AND uv.user_id = ?
                   WHERE c.vote_id = ?";
$stmt_candidates = $conn->prepare($sql_candidates);
$stmt_candidates->bind_param("ii", $user_id, $vote_id);
$stmt_candidates->execute();
$result_candidates = $stmt_candidates->get_result();

$candidates = [];
while ($row = $result_candidates->fetch_assoc()) {
    $row['image'] = base64_encode($row['image']);
    $candidates[] = $row;
}
$stmt_candidates->close();

$response = [
    'vote_name' => $vote['vote_name'],
    'vote_deadline' => $vote['vote_deadline'],
    'candidates' => $candidates
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
