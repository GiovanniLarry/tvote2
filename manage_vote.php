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

        // Prepare and execute the query to get candidate details
        $stmt = $conn->prepare("SELECT * FROM candidates WHERE vote_id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $vote_id);
        $stmt->execute();
        $candidates_result = $stmt->get_result();
    } else {
        echo "Vote ID not found.";
        exit;
    }
} else {
    echo "No vote ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Manage Vote</title>
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

        /* Navigation styling */
        nav ul {
            list-style-type: none;
        }

        nav ul li {
            display: inline;
            margin: 0 10px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        /* Main content styling */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            background-color: #f4f4f4;
        }

        .vote-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .vote-container h2 {
            margin-bottom: 1rem;
        }

        .candidate {
            display: inline-block;
            width: 200px;
            margin: 1rem;
            padding: 1rem;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        .candidate img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .candidate h3 {
            margin: 0.5rem 0;
        }

        .candidate p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .actions {
            margin-top: 2rem;
        }

        .actions button {
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 1rem;
        }

        .actions .edit {
            background-color: #ffc107;
            color: #fff;
        }

        .actions .edit:hover {
            background-color: #e0a800;
        }

        .actions .delete {
            background-color: #dc3545;
            color: #fff;
        }

        .actions .delete:hover {
            background-color: #c82333;
        }

        .done {
            background-color: #007bff;
            color: #fff;
        }

        .done:hover {
            background-color: #0056b3;
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
        <nav>
            <ul>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="signup.html">Sign Up</a></li>
                <li><a href="login.html">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="vote-container">
            <h2><?php echo htmlspecialchars($vote['title']); ?></h2>
            <p><?php echo htmlspecialchars($vote['description']); ?></p>
            <p><strong>Begin Date:</strong> <?php echo htmlspecialchars($vote['begin_date']); ?></p>
            <p><strong>Begin Time:</strong> <?php echo htmlspecialchars($vote['begin_time']); ?></p>
            <p><strong>End Date:</strong> <?php echo htmlspecialchars($vote['end_date']); ?></p>
            <p><strong>End Time:</strong> <?php echo htmlspecialchars($vote['end_time']); ?></p>
            <p><strong>Result Date:</strong> <?php echo htmlspecialchars($vote['result_date']); ?></p>
            <p><strong>Result Time:</strong> <?php echo htmlspecialchars($vote['result_time']); ?></p>
            <p><strong>Referral Code:</strong> <?php echo htmlspecialchars($vote['referral_code']); ?></p>

            <div class="candidates-container">
                <?php if ($candidates_result->num_rows > 0): ?>
                    <?php while ($candidate = $candidates_result->fetch_assoc()): ?>
                        <div class="candidate">
                            <img src="uploads/<?php echo htmlspecialchars($candidate['picture']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                            <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($candidate['age']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($candidate['description']); ?></p>
                            <p><strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?></p>
                            <div class="actions">
                                <button class="edit" onclick="window.location.href='edit_candidate.php?candidate_id=<?php echo urlencode($candidate['id']); ?>&vote_id=<?php echo urlencode($vote_id); ?>'">Edit</button>
                                <button class="delete" onclick="if (confirm('Are you sure you want to delete this candidate?')) { window.location.href='delete_candidate.php?candidate_id=<?php echo urlencode($candidate['id']); ?>&vote_id=<?php echo urlencode($vote_id); ?>'; }">Delete</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No candidates found for this vote.</p>
                <?php endif; ?>
            </div>

            <div class="actions">
                <button class="edit" onclick="window.location.href='edit_vote.php?vote_id=<?php echo urlencode($vote_id); ?>'">Edit Vote</button>
                <button class="delete" onclick="if (confirm('Are you sure you want to delete this vote?')) { window.location.href='delete_vote.php?vote_id=<?php echo urlencode($vote_id); ?>'; }">Delete Vote</button>
                <button class="done" onclick="confirmDone()">Done</button>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        <p>Email: <a href="mailto:admin@tvote.com">admin@tvote.com</a></p>
    </footer>

    <script>
        function confirmDone() {
            if (confirm("Once you leave this page, you can no longer edit or modify your vote. Are you sure you want to proceed?")) {
                window.location.href = 'vote_details.php?vote_id=<?php echo urlencode($vote_id); ?>';
            }
        }
    </script>
</body>
</html>
