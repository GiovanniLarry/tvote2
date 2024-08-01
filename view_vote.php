<?php
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

// Initialize variables
$vote_id = null;

// Check if vote_id is set and valid
if (isset($_GET['vote_id']) && is_numeric($_GET['vote_id'])) {
    $vote_id = $_GET['vote_id'];

    // Retrieve vote details
    $sqlVote = "SELECT * FROM Votes WHERE vote_id = ?";
    $stmtVote = $conn->prepare($sqlVote);
    $stmtVote->bind_param("i", $vote_id);
    $stmtVote->execute();
    $resultVote = $stmtVote->get_result();

    if ($resultVote->num_rows > 0) {
        $vote = $resultVote->fetch_assoc();

        // Retrieve candidates for this vote
        $sqlCandidates = "SELECT * FROM Candidates WHERE vote_id = ?";
        $stmtCandidates = $conn->prepare($sqlCandidates);
        $stmtCandidates->bind_param("i", $vote_id);
        $stmtCandidates->execute();
        $resultCandidates = $stmtCandidates->get_result();
    } else {
        echo "No vote found.";
        exit();
    }
} else {
    echo "No vote selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($vote['vote_name']) ? htmlspecialchars($vote['vote_name']) : 'Vote Details'; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="images/logot.jpg" alt="tVote Logo">
        <h1><?php echo isset($vote['vote_name']) ? htmlspecialchars($vote['vote_name']) : 'Vote Details'; ?></h1>
    </header>

    <main>
        <div class="vote-details">
            <?php if (isset($vote['vote_description'])): ?>
            <h2><?php echo htmlspecialchars($vote['vote_description']); ?></h2>
            <p>Deadline: <?php echo date('Y-m-d H:i', strtotime($vote['vote_deadline'])); ?></p>
            <?php endif; ?>

            <h2>Candidates</h2>
            <?php
            if (isset($resultCandidates) && $resultCandidates->num_rows > 0) {
                while ($candidate = $resultCandidates->fetch_assoc()) {
            ?>
            <div class="candidate">
                <h3><?php echo htmlspecialchars($candidate['candidate_name']); ?></h3>
                <p>Age: <?php echo htmlspecialchars($candidate['candidate_age']); ?></p>
                <p>Position: <?php echo htmlspecialchars($candidate['candidate_position']); ?></p>
                <p>Description: <?php echo htmlspecialchars($candidate['candidate_description']); ?></p>
                <?php if (!empty($candidate['candidate_image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($candidate['candidate_image']); ?>" alt="Candidate Image">
                <?php endif; ?>
                <form action="vote.php" method="post">
                    <input type="hidden" name="candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
                    <input type="hidden" name="vote_id" value="<?php echo $vote_id; ?>">
                    <button type="submit">Vote</button>
                </form>
            </div>
            <?php
                }
            } else {
                echo "<p>No candidates found for this vote.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>

<?php
// Close prepared statements and database connection
if (isset($stmtVote)) {
    $stmtVote->close();
}
if (isset($stmtCandidates)) {
    $stmtCandidates->close();
}
$conn->close();
?>
