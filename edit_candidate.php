<?php
// Include database connection
include 'db.php';

// Fetch the vote ID from the query parameters
$vote_id = $_GET['vote_id'] ?? '';
$error_message = '';
$success_message = '';

// Fetch existing candidates for the given vote_id
$candidates = [];
if ($vote_id) {
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE vote_id = ?");
    $stmt->bind_param("i", $vote_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update candidate details
    foreach ($_POST['candidates_id'] as $index => $id) {
        $name = htmlspecialchars($_POST['candidates_name'][$index]);
        $age = intval($_POST['candidates_age'][$index]);
        $description = htmlspecialchars($_POST['candidates_description'][$index]);
        $position = htmlspecialchars($_POST['candidates_position'][$index]);
        $picture = $_FILES['candidates_picture']['name'][$index];
        
        if ($picture) {
            // Handle file upload
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($picture);
            move_uploaded_file($_FILES['candidates_picture']['tmp_name'][$index], $target_file);
        } else {
            $target_file = $_POST['candidates_old_picture'][$index]; // Keep old picture if no new one is provided
        }
        
        $stmt = $conn->prepare("
            UPDATE candidates SET name = ?, age = ?, description = ?, position = ?, picture = ? WHERE id = ?
        ");
        $stmt->bind_param("sisssi", $name, $age, $description, $position, $target_file, $id);
        if (!$stmt->execute()) {
            $error_message = 'Failed to update candidate. Please try again.';
            break;
        }
    }

    if (!$error_message) {
        $success_message = 'Candidates updated successfully.';
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidates</title>
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
        .candidate-form {
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .candidate-form h4 {
            margin-bottom: 0.5rem;
        }
        .candidate-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .candidate-form input[type="text"],
        .candidate-form input[type="number"],
        .candidate-form textarea,
        .candidate-form input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .candidate-form textarea {
            resize: vertical;
            height: 100px;
        }
        .candidate-form img {
            display: block;
            margin-top: 0.5rem;
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h1>Edit Candidates</h1>
    </header>
    <main>
        <div class="form-container">
            <h2>Edit Candidates</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif ($success_message): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <form action="edit_candidate.php?vote_id=<?php echo urlencode($vote_id); ?>" method="post" enctype="multipart/form-data">
                <div id="candidates-container">
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="candidate-form">
                            <h4>Candidate <?php echo htmlspecialchars($candidate['id']); ?></h4>
                            <input type="hidden" name="candidates_id[]" value="<?php echo htmlspecialchars($candidate['id']); ?>">
                            <input type="hidden" name="candidates_old_picture[]" value="<?php echo htmlspecialchars($candidate['picture']); ?>">

                            <label for="candidates_name_<?php echo htmlspecialchars($candidate['id']); ?>">Name</label>
                            <input type="text" id="candidates_name_<?php echo htmlspecialchars($candidate['id']); ?>" name="candidates_name[]" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>

                            <label for="candidates_age_<?php echo htmlspecialchars($candidate['id']); ?>">Age</label>
                            <input type="number" id="candidates_age_<?php echo htmlspecialchars($candidate['id']); ?>" name="candidates_age[]" value="<?php echo htmlspecialchars($candidate['age']); ?>" required>

                            <label for="candidates_description_<?php echo htmlspecialchars($candidate['id']); ?>">Description</label>
                            <textarea id="candidates_description_<?php echo htmlspecialchars($candidate['id']); ?>" name="candidates_description[]" required><?php echo htmlspecialchars($candidate['description']); ?></textarea>

                            <label for="candidates_position_<?php echo htmlspecialchars($candidate['id']); ?>">Position</label>
                            <input type="text" id="candidates_position_<?php echo htmlspecialchars($candidate['id']); ?>" name="candidates_position[]" value="<?php echo htmlspecialchars($candidate['position']); ?>" required>

                            <label for="candidates_picture_<?php echo htmlspecialchars($candidate['id']); ?>">Picture</label>
                            <input type="file" id="candidates_picture_<?php echo htmlspecialchars($candidate['id']); ?>" name="candidates_picture[]" accept="image/*">
                            <?php if ($candidate['picture']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($candidate['picture']); ?>" alt="Candidate Picture">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit">Update Candidates</button>
            </form>
            <a class="back-button" href="manage_vote.php?vote_id=<?php echo urlencode($vote_id); ?>">Back to Manage Votes</a>
        </div>
    </main>
    <footer>
        <!-- Same footer as before -->
    </footer>
</body>
</html>
