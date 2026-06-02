<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->name) && isset($data->email) && isset($data->role)) {
    try {
        // If password is not empty, update the password as well
        if (!empty($data->password)) {
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->execute([$data->name, $data->email, $data->role, $hashedPassword, $data->id]);
        } else {
            // Keep the old password if it was left blank
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$data->name, $data->email, $data->role, $data->id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'User updated successfully!']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
}
?>