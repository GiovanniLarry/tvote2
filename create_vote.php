<?php
include('db.php');

$error_message = '';

// Set the time zone to Cameroon
date_default_timezone_set('Africa/Douala');

// Set default values for begin_date and begin_time
$default_begin_date = date('Y-m-d');
$default_begin_time = date('H:i');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $referral_code = htmlspecialchars($_POST['referral_code']);

    // Check title length
    if (strlen($title) > 50) {
        $error_message = 'Vote title should not exceed 50 words.';
    } 
    // Check description length
    elseif (strlen($description) > 150) {
        $error_message = 'Vote description should not exceed 150 words.';
    } 
    // Check for duplicate referral code
    else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM votes WHERE referral_code = ?");
        $stmt->bind_param("s", $referral_code);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $error_message = 'Referral code already in use. Please choose another one.';
        } else {
            // Validate times
            $begin_date_time = strtotime($_POST['begin_date'] . ' ' . $_POST['begin_time']);
            $end_date_time = strtotime($_POST['end_date'] . ' ' . $_POST['end_time']);
            $result_date_time = strtotime($_POST['result_date'] . ' ' . $_POST['result_time']);
            $ten_minutes = 10 * 60; // 10 minutes
            $seventy_two_hours = 72 * 60 * 60; // 72 hours

            if ($end_date_time < $begin_date_time + $ten_minutes) {
                $error_message = 'End time must be at least 10 minutes after the begin time.';
            } elseif ($result_date_time < $end_date_time) {
                $error_message = 'Result time must be the same or after the end time.';
            } elseif ($result_date_time > $end_date_time + $seventy_two_hours) {
                $error_message = 'Result time must not be more than 72 hours after the end time.';
            } else {
                // Insert vote details
                $stmt = $conn->prepare("
                    INSERT INTO votes (title, description, begin_date, begin_time, end_date, end_time, result_date, result_time, referral_code)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssssssss", $title, $description, $_POST['begin_date'], $_POST['begin_time'], $_POST['end_date'], $_POST['end_time'], $_POST['result_date'], $_POST['result_time'], $referral_code);

                if ($stmt->execute()) {
                    $vote_id = $stmt->insert_id; // Get the inserted vote ID
                    
                    // Process candidates
                    foreach ($_POST['candidates_name'] as $index => $name) {
                        if (empty($name) || empty($_POST['candidates_age'][$index]) || empty($_POST['candidates_description'][$index]) || empty($_POST['candidates_position'][$index])) {
                            $error_message = 'All candidate fields must be filled out.';
                            break;
                        }

                        $picture = $_FILES['candidates_picture']['name'][$index];
                        $age = $_POST['candidates_age'][$index];
                        $description = $_POST['candidates_description'][$index];
                        $position = $_POST['candidates_position'][$index];
                        
                        if ($picture) {
                            // Handle file upload
                            $target_dir = "uploads/";
                            $target_file = $target_dir . basename($picture);
                            move_uploaded_file($_FILES['candidates_picture']['tmp_name'][$index], $target_file);
                        } else {
                            $target_file = ''; // Default if no picture is provided
                        }
                        
                        $stmt = $conn->prepare("
                            INSERT INTO candidates (vote_id, name, age, description, position, picture)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("isisis", $vote_id, $name, $age, $description, $position, $target_file);
                        $stmt->execute();
                    }

                    if (!$error_message) {
                        $stmt->close();
                        $conn->close();
                        header("Location: create_vote_done.php"); // Redirect to a success page
                        exit;
                    }
                } else {
                    $error_message = 'Failed to create vote. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tVote - Create Vote</title>
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
            border-radius: 50%;
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
            justify-content: center;
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
            max-width: 800px;
            text-align: center;
        }

        /* Form styling */
        form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        form input[type="text"],
        form textarea,
        form input[type="date"],
        form input[type="time"],
        form input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form textarea {
            resize: vertical;
            height: 100px;
        }

        form button {
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        /* Footer styling */
        footer {
            background: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
        }

        .candidates-section {
            margin-top: 2rem;
        }

        .candidate-form {
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: left;
        }

        .candidate-form:not(:last-of-type) {
            margin-bottom: 1rem;
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
                
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Create a New Vote</h2>
            <?php if ($error_message) : ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <label for="title">Vote Title:</label>
                <input type="text" id="title" name="title" required maxlength="50" value="<?= htmlspecialchars($title ?? '') ?>">

                <label for="description">Vote Description:</label>
                <textarea id="description" name="description" required maxlength="150"><?= htmlspecialchars($description ?? '') ?></textarea>

                <label for="referral_code">Referral Code:</label>
                <input type="text" id="referral_code" name="referral_code" required value="<?= htmlspecialchars($referral_code ?? '') ?>">

                <label for="begin_date">Begin Date:</label>
                <input type="date" id="begin_date" name="begin_date" required value="<?= htmlspecialchars($default_begin_date) ?>">

                <label for="begin_time">Begin Time:</label>
                <input type="time" id="begin_time" name="begin_time" required value="<?= htmlspecialchars($default_begin_time) ?>">

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>

                <label for="result_date">Result Date:</label>
                <input type="date" id="result_date" name="result_date" required>

                <label for="result_time">Result Time:</label>
                <input type="time" id="result_time" name="result_time" required>

                <div class="candidates-section">
                    <h3>Candidate Details:</h3>
                    <div id="candidates-container">
                        <div class="candidate-form">
                            <label for="candidates_name[]">Name:</label>
                            <input type="text" id="candidates_name[]" name="candidates_name[]" required>

                            <label for="candidates_age[]">Age:</label>
                            <input type="number" id="candidates_age[]" name="candidates_age[]" required min="10" max="80">

                            <label for="candidates_description[]">Description:</label>
                            <textarea id="candidates_description[]" name="candidates_description[]" required></textarea>

                            <label for="candidates_position[]">Position:</label>
                            <input type="text" id="candidates_position[]" name="candidates_position[]" required>

                            <label for="candidates_picture[]">Picture:</label>
                            <input type="file" id="candidates_picture[]" name="candidates_picture[]">
                        </div>
                    </div>
                    <button type="button" onclick="addCandidate()">Add Another Candidate</button>
                </div>

                <button type="submit">Create Vote</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
    </footer>

    <script>
        function addCandidate() {
            const candidatesContainer = document.getElementById('candidates-container');
            const candidateForm = document.createElement('div');
            candidateForm.classList.add('candidate-form');

            candidateForm.innerHTML = `
                <label for="candidates_name[]">Name:</label>
                <input type="text" id="candidates_name[]" name="candidates_name[]" required>

                <label for="candidates_age[]">Age:</label>
                <input type="number" id="candidates_age[]" name="candidates_age[]" required min="10" max="80">

                <label for="candidates_description[]">Description:</label>
                <textarea id="candidates_description[]" name="candidates_description[]" required></textarea>

                <label for="candidates_position[]">Position:</label>
                <input type="text" id="candidates_position[]" name="candidates_position[]" required>

                <label for="candidates_picture[]">Picture:</label>
                <input type="file" id="candidates_picture[]" name="candidates_picture[]">
            `;

            candidatesContainer.appendChild(candidateForm);
        }

        // Validation logic
        const form = document.querySelector('form');
        const beginDateInput = document.getElementById('begin_date');
        const beginTimeInput = document.getElementById('begin_time');
        const endDateInput = document.getElementById('end_date');
        const endTimeInput = document.getElementById('end_time');
        const resultDateInput = document.getElementById('result_date');
        const resultTimeInput = document.getElementById('result_time');

        form.addEventListener('submit', function(event) {
            const beginDateTime = new Date(`${beginDateInput.value}T${beginTimeInput.value}`);
            const endDateTime = new Date(`${endDateInput.value}T${endTimeInput.value}`);
            const resultDateTime = new Date(`${resultDateInput.value}T${resultTimeInput.value}`);
            const tenMinutes = 10 * 60 * 1000;
            const seventyTwoHours = 72 * 60 * 60 * 1000;

            if (endDateTime < beginDateTime.getTime() + tenMinutes) {
                alert('End time must be at least 10 minutes after the begin time.');
                event.preventDefault();
            } else if (resultDateTime < endDateTime) {
                alert('Result time must be the same or after the end time.');
                event.preventDefault();
            } else if (resultDateTime > endDateTime.getTime() + seventyTwoHours) {
                alert('Result time must not be more than 72 hours after the end time.');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
