<?php
// Tell the browser this file outputs JSON
header('Content-Type: application/json');

// Connect to the database
require 'db.php';

// Get the JSON payload
$data = json_decode(file_get_contents("php://input"));

if (isset($data->name) && isset($data->email) && isset($data->password) && isset($data->role)) {
    try {
        // Check if the user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data->email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
            exit;
        }

        // Insert the new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->name, $data->email, $data->password, $data->role]);
        
        echo json_encode(['success' => true, 'message' => 'User added successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing user details.']);
}
?>