<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow ESP32 / frontend cross-origin access
require '../db.php';

try {
    // Grab all bins and format the date so it perfectly matches JavaScript's Date parsing expectations
    $stmt = $pdo->query("SELECT id, location, fillLevel, status, DATE_FORMAT(lastUpdated, '%Y-%m-%dT%H:%i:%s.000Z') AS lastUpdated FROM bins ORDER BY id ASC");
    $bins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($bins);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>