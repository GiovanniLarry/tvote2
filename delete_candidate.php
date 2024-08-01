<?php
// Include database connection
include 'db.php';

// Fetch the candidate ID and vote ID from the query parameters
$candidate_id = $_GET['candidate_id'] ?? '';
$vote_id = $_GET['vote_id'] ?? '';

if ($candidate_id && $vote_id) {
    // Prepare and execute the query to delete the candidate
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ? AND vote_id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $candidate_id, $vote_id);
    if ($stmt->execute()) {
        echo "Candidate deleted successfully.";
    } else {
        echo "Error deleting candidate: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid candidate ID or vote ID.";
}

// Redirect back to the vote management page
header("Location: manage_vote.php?vote_id=" . urlencode($vote_id));
exit;
?>
