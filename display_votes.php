<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Votes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="images/logot.jpg" alt="tVote Logo">
        <h1>All Votes</h1>
    </header>
    <main>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "voting_system2";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Votes";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='vote'>";
                echo "<h2>" . $row["vote_name"] . "</h2>";
                echo "<p><strong>Description:</strong> " . $row["vote_description"] . "</p>";
                echo "<p><strong>Deadline:</strong> " . $row["vote_deadline"] . "</p>";
                echo "<a href='view_vote.php?vote_id=" . $row["vote_id"] . "'>View Candidates</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No votes found.</p>";
        }

        $conn->close();
        ?>
    </main>
</body>
</html>
