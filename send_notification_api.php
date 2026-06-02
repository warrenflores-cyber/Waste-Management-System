<?php
// Tell the browser this file outputs JSON
header('Content-Type: application/json');

// Connect to the database
require 'db.php';

// Get the JSON payload
$data = json_decode(file_get_contents("php://input"));

if (isset($data->subject) && isset($data->message)) {
    try {
        // If a real email is provided, use it. Otherwise, fallback to the database users!
        if (isset($data->email) && !empty($data->email) && $data->email !== 'admin@example.com') {
            $to = $data->email;
        } else {
            $stmt = $pdo->query("SELECT email FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($users) === 0) {
                echo json_encode(['success' => false, 'message' => 'No users found to notify.']);
                exit;
            }
            $emails = array_map(function($user) { return $user['email']; }, $users);
            $to = implode(',', $emails);
        }

        // PASTE YOUR GOOGLE APPS SCRIPT WEB APP URL HERE
        $gasUrl = "https://script.google.com/macros/s/AKfycbylbxjlE1pKWb0VMIf6YkFvTeMlu1Nd7lDVgFn3VxXNPujGE7at4VF5iC62hYAg5Afllw/exec";
        
        $payload = json_encode([
            'email' => $to,
            'subject' => $data->subject,
            'message' => $data->message
        ]);

        $ch = curl_init($gasUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        // Bypass SSL verification for local XAMPP testing to prevent cURL connection drops
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            echo json_encode(['success' => false, 'message' => "cURL Error: " . $error]);
        } else {
            $gasResponse = json_decode($response);
            $gasMessage = isset($gasResponse->message) ? $gasResponse->message : "Sent to " . $to;
            echo json_encode(['success' => true, 'message' => "Google says: " . $gasMessage]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing email parameters.']);
}
?>