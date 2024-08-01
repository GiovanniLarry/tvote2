<?php
// users.php
include 'db_config.php';

// Fetch Users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, whatsapp, country_code, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['username'], $data['email'], $data['whatsapp'], $data['country_code'], $data['dob'], $data['password']]);
    echo json_encode(['status' => 'User added successfully']);
}
?>
