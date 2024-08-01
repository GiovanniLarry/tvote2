<?php
include('db.php');

// Fetch the vote ID from the query parameters
$vote_id = $_GET['vote_id'] ?? '';

if (!$vote_id) {
    echo "No vote ID provided.";
    exit;
}

// Fetch vote details
$stmt = $conn->prepare("SELECT * FROM votes WHERE id = ?");
$stmt->bind_param("s", $vote_id);
$stmt->execute();
$vote_result = $stmt->get_result();

if ($vote_result->num_rows === 0) {
    echo "Vote not found.";
    exit;
}

$vote = $vote_result->fetch_assoc();

// Fetch candidates details
$stmt = $conn->prepare("SELECT * FROM candidates WHERE vote_id = ?");
$stmt->bind_param("i", $vote_id);
$stmt->execute();
$candidates_result = $stmt->get_result();

$candidates = [];
while ($candidate = $candidates_result->fetch_assoc()) {
    $candidates[] = $candidate;
}

$stmt->close();
$conn->close();

// Calculate time remaining
$now = new DateTime();
$end_date = new DateTime($vote['end_date'] . ' ' . $vote['end_time']);
$remaining_time = $end_date->diff($now);

$time_remaining = $remaining_time->format('%d days %h hours %i minutes %s seconds');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Details</title>
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


        /* Marquee styling */
        marquee {
            font-weight: bold;
            color: pink;
            background: #333;
            padding: 1rem;
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

        .vote-details, .candidates-details {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            margin-bottom: 2rem;
        }

        h2, h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        p {
            margin-bottom: 0.5rem;
        }

        .candidates-details ul {
            list-style-type: none;
            padding: 0;
        }

        .candidates-details li {
            border-bottom: 1px solid #ddd;
            padding: 1rem 0;
        }

        .candidates-details img {
            border-radius: 4px;
            max-width: 200px;
            height: auto;
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

    <marquee>Share your referral code with voters who are eligible!</marquee>
    
    <main>
        <div class="vote-details">
            <h2>Vote Details</h2>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($vote['title']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($vote['description']); ?></p>
            <p><strong>Begin Date:</strong> <?php echo htmlspecialchars($vote['begin_date']); ?></p>
            <p><strong>Begin Time:</strong> <?php echo htmlspecialchars($vote['begin_time']); ?></p>
            <p><strong>End Date:</strong> <?php echo htmlspecialchars($vote['end_date']); ?></p>
            <p><strong>End Time:</strong> <?php echo htmlspecialchars($vote['end_time']); ?></p>
            <p><strong>Result Date:</strong> <?php echo htmlspecialchars($vote['result_date']); ?></p>
            <p><strong>Result Time:</strong> <?php echo htmlspecialchars($vote['result_time']); ?></p>
            <p><strong>Referral Code:</strong> <?php echo htmlspecialchars($vote['referral_code']); ?></p>
            <p><strong>Time Remaining:</strong> <?php echo $time_remaining; ?></p>
        </div>
        
        <div class="candidates-details">
            <h3>Candidates</h3>
            <?php if (count($candidates) > 0): ?>
                <ul>
                    <?php foreach ($candidates as $candidate): ?>
                        <li>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($candidate['name']); ?></p>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($candidate['age']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($candidate['description']); ?></p>
                            <p><strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?></p>
                            <?php if ($candidate['picture']): ?>
                                <p><strong>Picture:</strong></p>
                                <img src="<?php echo htmlspecialchars($candidate['picture']); ?>" alt="Candidate Picture">
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No candidates found for this vote.</p>
            <?php endif; ?>
            <button onclick="window.location.href='index.html'">Back Home</button>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
       
    </footer>
</body>
</html>
