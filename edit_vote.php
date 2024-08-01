<?php
// Include database connection
include 'db.php';

// Fetch the vote ID from the query parameters
$vote_id = $_GET['vote_id'] ?? '';
$error_message = '';
$success_message = '';

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
    } else {
        $error_message = "Vote ID not found.";
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $title = $_POST['title'];
        $description = $_POST['description'];
        $begin_date = $_POST['begin_date'];
        $begin_time = $_POST['begin_time'];
        $end_date = $_POST['end_date'];
        $end_time = $_POST['end_time'];
        $result_date = $_POST['result_date'];
        $result_time = $_POST['result_time'];
        $referral_code = $_POST['referral_code'];

        // Prepare and execute the update query
        $stmt = $conn->prepare("
            UPDATE votes 
            SET title = ?, description = ?, begin_date = ?, begin_time = ?, end_date = ?, end_time = ?, result_date = ?, result_time = ?, referral_code = ? 
            WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Correct bind_param: 'ssssssssss' for 9 strings and 's' for 1 string
        $stmt->bind_param("ssssssssss", $title, $description, $begin_date, $begin_time, $end_date, $end_time, $result_date, $result_time, $referral_code, $vote_id);
        if ($stmt->execute()) {
            $success_message = 'Vote updated successfully.';
        } else {
            $error_message = 'Error updating vote: ' . $conn->error;
        }
    }
} else {
    $error_message = 'No vote ID provided.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Edit Vote</title>
    <style>
        /* Styling similar to previous examples */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .form-container {
            background: #fff;
            padding: 2rem;
            margin: 2rem auto;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 1rem;
        }
        .form-container .error-message, .form-container .success-message {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
        }
        .form-container .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-container .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        button {
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            background-color: #6c757d;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Vote</h1>
    </header>
    <main>
        <div class="form-container">
            <h2>Edit Vote</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif ($success_message): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <?php if ($vote): ?>
                <form action="edit_vote.php?vote_id=<?php echo urlencode($vote_id); ?>" method="post">
                    <label for="title">Vote Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($vote['title']); ?>" required>

                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($vote['description']); ?></textarea>

                    <label for="begin_date">Begin Date</label>
                    <input type="date" id="begin_date" name="begin_date" value="<?php echo htmlspecialchars($vote['begin_date']); ?>" required>

                    <label for="begin_time">Begin Time</label>
                    <input type="time" id="begin_time" name="begin_time" value="<?php echo htmlspecialchars($vote['begin_time']); ?>" required>

                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($vote['end_date']); ?>" required>

                    <label for="end_time">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="<?php echo htmlspecialchars($vote['end_time']); ?>" required>

                    <label for="result_date">Result Date</label>
                    <input type="date" id="result_date" name="result_date" value="<?php echo htmlspecialchars($vote['result_date']); ?>" required>

                    <label for="result_time">Result Time</label>
                    <input type="time" id="result_time" name="result_time" value="<?php echo htmlspecialchars($vote['result_time']); ?>" required>

                    <label for="referral_code">Referral Code</label>
                    <input type="text" id="referral_code" name="referral_code" value="<?php echo htmlspecialchars($vote['referral_code']); ?>" required>

                    <button type="submit">Update Vote</button>
                </form>
                <a class="back-button" href="manage_vote.php?vote_id=<?php echo urlencode($vote_id); ?>">Back to Manage Votes</a>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <!-- Same footer as before -->
    </footer>
</body>
</html>
