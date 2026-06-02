<?php
header('Content-Type: application/json');
require 'db.php';

try {
    // Intentionally excluding the password hash from the select query for security
    $stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $users]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>