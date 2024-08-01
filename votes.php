<?php
// votes.php
include 'db_config.php';

// Fetch Votes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM votes");
    $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($votes);
}

// Add Vote
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO votes (title, description, begin_date, begin_time, end_date, end_time, result_date, result_time, referral_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['title'], $data['description'], $data['begin_date'], $data['begin_time'], $data['end_date'], $data['end_time'], $data['result_date'], $data['result_time'], $data['referral_code']]);
    echo json_encode(['status' => 'Vote added successfully']);
}
?>
