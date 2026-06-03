<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->location)) {
    try {
        $stmt = $pdo->prepare("UPDATE bins SET location = ?, lastUpdated = NOW() WHERE id = ?");
        $stmt->execute([$data->location, $data->id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Bin updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Bin not found or no changes made']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>