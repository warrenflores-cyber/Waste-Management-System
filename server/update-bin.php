<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $id = $data->id;
    $binHeight = isset($data->binHeight) ? (int)$data->binHeight : 100;
    
    // Default values
    $fillLevel = 0;
    $status = 'Normal';
    
    // Calculate fillLevel from distance if provided by ESP32, otherwise use raw fillLevel
    if (isset($data->distance)) {
        $distance = (float)$data->distance;
        $fillLevel = (($binHeight - $distance) / $binHeight) * 100;
        
        // Clamp between 0% and 100%
        if ($fillLevel < 0) $fillLevel = 0;
        if ($fillLevel > 100) $fillLevel = 100;
        $fillLevel = round($fillLevel);
        
        if ($fillLevel >= 90) {
            $status = 'Full';
        } else if ($fillLevel >= 70) {
            $status = 'Warning';
        } else {
            $status = 'Normal';
        }
    } else {
        $fillLevel = isset($data->fillLevel) ? (int)$data->fillLevel : 0;
        $status = isset($data->status) ? $data->status : 'Normal';
    }

    try {
        // Check if the bin exists to decide whether to update or insert it
        $stmt = $pdo->prepare("SELECT id FROM bins WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE bins SET fillLevel = ?, status = ?, lastUpdated = NOW() WHERE id = ?");
            $stmt->execute([$fillLevel, $status, $id]);
        } else {
            $location = isset($data->location) ? $data->location : "New Sensor Location";
            $stmt = $pdo->prepare("INSERT INTO bins (id, location, fillLevel, status, lastUpdated) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$id, $location, $fillLevel, $status]);
        }
        echo json_encode(['success' => true, 'message' => 'Bin updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing bin id']);
}
?>