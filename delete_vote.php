<?php
// Include database connection
include 'db.php';

// Fetch the vote ID from the query parameters
$vote_id = $_GET['vote_id'] ?? '';

if ($vote_id) {
    // Prepare and execute the query to delete the vote
    $stmt = $conn->prepare("DELETE FROM votes WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $vote_id);
    if ($stmt->execute()) {
        echo "Vote deleted successfully.";
        // Redirect to a different page after deletion, e.g., list of votes
        header("Location: list_votes.php");
        exit;
    } else {
        echo "Error deleting vote: " . $conn->error;
    }
} else {
    echo "No vote ID provided.";
    exit;
}
?>
