<?php
// Include database connection
include 'db.php';

// Initialize variables
$referral_code = '';
$message = '';
$time_left = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $referral_code = $_POST['referral_code'] ?? '';

    // Validate referral code
    if ($referral_code) {
        // Prepare and execute the query to check the referral code
        $stmt = $conn->prepare("SELECT * FROM votes WHERE referral_code = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $referral_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $vote = $result->fetch_assoc();
            $current_time = new DateTime();
            $end_time = new DateTime($vote['end_time']); // Assuming 'end_time' is the column name

            if ($current_time >= $end_time) {
                // Redirect to results page
                header("Location: view_results.php?vote_id=" . urlencode($vote['id']));
                exit;
            } else {
                $interval = $end_time->diff($current_time);
                $time_left = $interval->format('%d days %h hours %i minutes %s seconds');
                $message = "The vote is still ongoing. Please come back after the end time to see the results.";
            }

        } else {
            $message = "Invalid referral code. Please try again.";
        }

        $stmt->close();
    } else {
        $message = "Please enter a referral code.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Enter Referral Code</title>
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
            height: 50px;
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
            width: 100%;
        }

        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 1rem;
        }

        input[type="text"] {
            width: calc(100% - 2rem);
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        footer {
            background: #333;
            color: #fff;
            width: 100%;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="tVote Logo">
            <h1>tVote</h1>
        </div>
    </header>
    <main>
        <div class="container">
            <h2>Enter Referral Code</h2>
            <form action="enter_referal_code.php" method="POST">
                <input type="text" name="referral_code" placeholder="Enter referral code" required>
                <button type="submit">Submit</button>
            </form>
            <p><?php echo htmlspecialchars($message); ?></p>
            <?php if ($time_left): ?>
                <p>Time left until results are available: <?php echo htmlspecialchars($time_left); ?></p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Douala, Cameroon</p>
        
    </footer>
</body>
</html>
