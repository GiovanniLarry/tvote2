<?php
// analytics.php
include 'db_config.php';

// Fetch User Activity
if (isset($_GET['type']) && $_GET['type'] === 'user_activity') {
    $stmt = $pdo->query("SELECT u.username, l.action, l.timestamp FROM user_activity_logs l JOIN users u ON l.user_id = u.id ORDER BY l.timestamp DESC");
    $activity_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($activity_logs);
}

// Fetch Vote Stats
if (isset($_GET['type']) && $_GET['type'] === 'vote_stats') {
    $stmt = $pdo->query("SELECT v.title, s.total_votes FROM votes v JOIN vote_stats s ON v.id = s.vote_id");
    $vote_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($vote_stats);
}
?>
