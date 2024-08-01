<?php
// Include database connection
include 'db.php';

// Fetch the vote ID from the query parameters
$vote_id = $_GET['vote_id'] ?? '';

if ($vote_id) {
    // Prepare and execute the query to get vote details
    $stmt = $conn->prepare("SELECT * FROM votes WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $vote_id);
    $stmt->execute();
    $vote_result = $stmt->get_result();

    if ($vote_result->num_rows > 0) {
        $vote = $vote_result->fetch_assoc();

        // Prepare and execute the query to get candidate details and their votes
        $stmt = $conn->prepare("
            SELECT candidates.id, candidates.name, candidates.picture, 
                   COUNT(vote_cast.candidate_id) AS votes_count
            FROM candidates
            LEFT JOIN vote_cast ON candidates.id = vote_cast.candidate_id
            WHERE candidates.vote_id = ?
            GROUP BY candidates.id, candidates.name, candidates.picture
        ");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $vote_id);
        $stmt->execute();
        $candidates_result = $stmt->get_result();

        // Prepare and execute the query to get the total number of voters
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT vote_id) AS total_voters FROM vote_cast WHERE vote_id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $vote_id);
        $stmt->execute();
        $total_voters_result = $stmt->get_result();
        $total_voters = $total_voters_result->fetch_assoc()['total_voters'];
    } else {
        echo "Vote ID not found.";
        exit;
    }
} else {
    echo "No vote ID provided.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - View Results</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Ensure the body takes the full height of the viewport */
        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        /* Flexbox layout for centering content */
        body {
            display: flex;
            flex-direction: column;
        }

        /* Header styling */
        header {
            background: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        header .logo img {
            height: 50px;
        }
        header .logo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 50%; /* Makes the logo circular */
            background-color: transparent; /* Ensures background is transparent */
        }


        /* Main content styling */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: #f4f4f4;
        }

        .results-container {
            width: 100%;
            max-width: 800px;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .results-container h2 {
            margin-bottom: 1rem;
        }

        .candidate {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .candidate img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .candidate p {
            margin: 0;
        }

        footer {
            background: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logot.jpg" alt="tVote Logo">

            <h1>tVote</h1>
        </div>

    </header>
    <main>
        <div class="results-container">
            <h2>Election Results for "<?php echo htmlspecialchars($vote['title']); ?>"</h2>
            <p><strong>Total Voters:</strong> <?php echo htmlspecialchars($total_voters); ?></p>
            <?php if ($candidates_result->num_rows > 0): ?>
                <?php while ($candidate = $candidates_result->fetch_assoc()): ?>
                    <div class="candidate">
                        <img src="uploads/<?php echo htmlspecialchars($candidate['picture']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                        <div>
                            <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                            <p><strong>Votes:</strong> <?php echo htmlspecialchars($candidate['votes_count']); ?></p>
                            <?php if ($total_voters > 0): ?>
                                <p><strong>Percentage:</strong> <?php echo number_format(($candidate['votes_count'] / $total_voters) * 100, 2); ?>%</p>
                            <?php else: ?>
                                <p><strong>Percentage:</strong> N/A</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No candidates found for this vote.</p>
            <?php endif; ?>
        </div>

    </main>
    <center> <li><button><a href="index.html">Home</a></li></button></center>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        
    </footer>
</body>
</html>
