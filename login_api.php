<?php
// Tell the browser this file outputs JSON
header('Content-Type: application/json');

// Connect to the database
require 'db.php';

// Grab the JSON data sent by the JavaScript fetch() function
$data = json_decode(file_get_contents("php://input"));

if (isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = $data->password;

    // Securely search for the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Success! Send the user details back (without the password)
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } else {
        // Failed: No match found
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
}
?>