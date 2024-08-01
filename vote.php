<?php
// Include database connection
include 'db.php';

$vote_id = $_GET['vote_id'] ?? '';
$candidates_result = null;
$vote = null;
$error_message = '';

if ($vote_id) {
    // Prepare and execute the query to get vote details
    $stmt = $conn->prepare("SELECT * FROM votes WHERE id = ?");
    if ($stmt === false) {
        $error_message = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $vote_id);
        $stmt->execute();
        $vote_result = $stmt->get_result();

        if ($vote_result->num_rows > 0) {
            $vote = $vote_result->fetch_assoc();

            // Prepare and execute the query to get candidate details
            $stmt = $conn->prepare("SELECT * FROM candidates WHERE vote_id = ?");
            if ($stmt === false) {
                $error_message = "Error preparing statement: " . $conn->error;
            } else {
                $stmt->bind_param("s", $vote_id);
                $stmt->execute();
                $candidates_result = $stmt->get_result();
            }
        } else {
            $error_message = "No vote found with this ID.";
        }
    }
} else {
    $error_message = "No vote ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Vote</title>
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

        .vote-form button {
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        .vote-form button:hover {
            background-color: #0056b3;
        }

        footer {
            background: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
    <script>
        function confirmVote(candidateName, form) {
            if (confirm(`Are you sure you want to vote for ${candidateName}? Once you vote, you cannot change it.`)) {
                form.action = 'submit_vote.php'; // Change the action to result.php
                form.submit(); // Submit the form
            } else {
                return false; // Prevent form submission
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="tVote Logo">
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
            <?php if ($error_message): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php else: ?>
                <h2><?php echo htmlspecialchars($vote['title']); ?></h2>
                <p><?php echo htmlspecialchars($vote['description']); ?></p>
                
                <?php if ($candidates_result && $candidates_result->num_rows > 0): ?>
                    <?php while ($candidate = $candidates_result->fetch_assoc()): ?>
                        <div class="candidate">
                            <img src="uploads/<?php echo htmlspecialchars($candidate['picture']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                            <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($candidate['age']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($candidate['description']); ?></p>
                            <p><strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?></p>
                            <form method="post" class="vote-form" onsubmit="return confirmVote('<?php echo htmlspecialchars($candidate['name']); ?>', this)">
                                <input type="hidden" name="vote_id" value="<?php echo htmlspecialchars($vote_id); ?>">
                                <input type="hidden" name="candidate_id" value="<?php echo htmlspecialchars($candidate['id']); ?>">
                                <button type="submit">Vote</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No candidates available for this vote.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        
    </footer>
</body>
</html>
