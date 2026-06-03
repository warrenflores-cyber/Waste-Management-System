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
$location = isset($input['location']) ? $input['location'] : "";

$found = false;
foreach ($bins as &$bin) {
    if ($bin['id'] === $id) {
        if ($location) $bin['location'] = $location;
        $found = true;
        break;
    }
}

if ($found && file_put_contents($dataFile, json_encode($bins, JSON_PRETTY_PRINT))) {
    echo json_encode(["success" => true, "message" => "Location updated"]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Bin not found or failed to update"]);
}
?>