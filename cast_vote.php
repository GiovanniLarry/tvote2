<?php
// Include database connection
include 'db.php';

$referral_code = $_POST['referral_code'] ?? '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the referral code
    $stmt = $conn->prepare("SELECT * FROM votes WHERE referral_code = ?");
    if ($stmt === false) {
        $error_message = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $referral_code);
        $stmt->execute();
        $vote_result = $stmt->get_result();

        if ($vote_result->num_rows > 0) {
            // Fetch vote details
            $vote = $vote_result->fetch_assoc();
            $vote_id = $vote['id'];

            // Redirect to vote page with vote ID
            header("Location: vote.php?vote_id=" . urlencode($vote_id));
            exit;
        } else {
            $error_message = "Invalid referral code. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Cast Vote</title>
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

        .form-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 1rem;
        }

        form button:hover {
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
        <div class="form-container">
            <h2>Cast Your Vote</h2>
            <form action="cast_vote.php" method="post">
                <label for="referral_code">Referral Code</label>
                <input type="text" id="referral_code" name="referral_code" required placeholder="Enter the referral code">

                <button type="submit">Submit</button>
            </form>

            <?php if ($error_message): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        
    </footer>
</body>
</html>
