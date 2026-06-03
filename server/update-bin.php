<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$dataFile = 'data.json';
$bins = [];

if (file_exists($dataFile)) {
    $bins = json_decode(file_get_contents($dataFile), true);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$id = $input['id'];
$fillLevel = isset($input['fillLevel']) ? $input['fillLevel'] : 0;
$status = isset($input['status']) ? $input['status'] : "Normal";

$found = false;
foreach ($bins as &$bin) {
    if ($bin['id'] === $id) {
        $bin['fillLevel'] = (int)$fillLevel;
        $bin['status'] = $status;
        $bin['lastUpdated'] = date('Y-m-d\TH:i:s\Z');
        $found = true;
        break;
    }
}

if (!$found) {
    $bins[] = [
        "id" => $id,
        "location" => "New Sensor Location",
        "fillLevel" => (int)$fillLevel,
        "status" => $status,
        "lastUpdated" => date('Y-m-d\TH:i:s\Z')
    ];
}

// Save the updated array back to the JSON file
if (file_put_contents($dataFile, json_encode($bins, JSON_PRETTY_PRINT))) {
    echo json_encode(["success" => true, "message" => "Bin updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to write data"]);
}
?>