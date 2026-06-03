<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dataFile = 'data.json';

if (!file_exists($dataFile)) {
    // Create a default test bin to verify it's working
    $defaultData = [[
        "id" => "BIN-TEST",
        "location" => "Railway PHP Server Test",
        "fillLevel" => 50,
        "status" => "Normal",
        "lastUpdated" => date('Y-m-d\TH:i:s\Z')
    ]];
    file_put_contents($dataFile, json_encode($defaultData, JSON_PRETTY_PRINT));
}

echo file_get_contents($dataFile);
?>