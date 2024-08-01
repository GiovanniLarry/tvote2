<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>tVote - Sign In</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .content-container {
            text-align: center;
            margin-top: 50px;
        }
        .content-container img {
            display: block;
            margin: 0 auto;
            max-width: 300px;
            width: 100%;
            height: auto;
            border-radius: 50%;
            background-color: transparent;
        }
        .content-container button {
            background-color: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }
        .content-container button:hover {
            background-color: darkblue;
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
            
        </nav>
    </header>
    <main>
        <div class="content-container">
            <h2>Well Done You Have Successfully Created A Vote</h2>
            <img src="images/bluetick.jpg" alt="Sign In Image">
            <button onclick="window.location.href='manage_vote.php'">View Vote</button>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 tVote. All rights reserved.</p>
        <p>Location: Douala, Cameroon</p>
        
    </footer>
    <script src="script.js"></script>
</body>
</html>
