<?php
// Include database connection
include 'db.php';
session_start();

// Initialize message
$message = '';

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'] ?? '';

// Check if required POST parameters are set
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vote_id = $_POST['vote_id'] ?? '';
    $candidate_id = $_POST['candidate_id'] ?? '';

    // Validate input
    if ($vote_id && $candidate_id && $user_id) {
        // Check if the user has already voted in this vote
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM vote_cast WHERE vote_id = ? AND user_id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $vote_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            // User has already voted in this vote
            $message = "You have already voted in this vote.";
        } else {
            // Insert the vote into the database
            $stmt = $conn->prepare("INSERT INTO vote_cast (vote_id, candidate_id, user_id) VALUES (?, ?, ?)");
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("sss", $vote_id, $candidate_id, $user_id);
            if ($stmt->execute()) {
                $message = "Your vote has been successfully recorded.";
            } else {
                $message = "Error recording your vote: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $message = "Vote ID, Candidate ID, or User ID is missing.";
    }
} else {
    $message = "Invalid request method.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Vote Submitted</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            color: #fff;
            width: 100%;
            padding: 1rem;
            text-align: center;
        }

        
        header .logo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 50%; /* Makes the logo circular */
            background-color: transparent; /* Ensures background is transparent */
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .vote-submission-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 1rem;
        }

        a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        a:hover {
            background-color: #555;
        }

        footer {
            background: #333;
            color: #fff;
            width: 100%;
            padding: 1rem;
            text-align: center;
        }

        footer a {
            color: #fff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
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
        <div class="vote-submission-container">
            <h2>Thank you for voting!</h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="index.html">Return to Home</a>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        
    </footer>
</body>
</html>
