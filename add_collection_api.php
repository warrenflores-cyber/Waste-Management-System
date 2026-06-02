<?php
// Tell the browser this file outputs JSON
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->binId) && isset($data->location) && isset($data->actionTaken)) {
    try {
        $id = 'COL-' . time();
        $date = date('Y-m-d H:i:s');
        
        $stmt = $pdo->prepare("INSERT INTO collection_history (id, date, binId, location, actionTaken) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $date, $data->binId, $data->location, $data->actionTaken]);
        
        echo json_encode(['success' => true, 'id' => $id, 'date' => $date]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing collection details.']);
}
?>