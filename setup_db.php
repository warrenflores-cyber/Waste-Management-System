<?php
require 'db.php';

try {
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL
    )");

    // Create collection_history table
    $pdo->exec("CREATE TABLE IF NOT EXISTS collection_history (
        id VARCHAR(50) PRIMARY KEY,
        date DATETIME,
        binId VARCHAR(50),
        location VARCHAR(255),
        actionTaken VARCHAR(100)
    )");

    // Create bins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bins (
        id VARCHAR(50) PRIMARY KEY,
        location VARCHAR(255),
        fillLevel INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'Normal',
        lastUpdated DATETIME
    )");

    // Add a default test bin
    $pdo->exec("INSERT IGNORE INTO bins (id, location, fillLevel, status, lastUpdated) VALUES ('BIN-TEST', 'Railway Server Connection Test', 50, 'Normal', NOW())");

    // Add default admin user (kung wala pa)
    $pdo->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@ecosync.com', 'admin123', 'Admin')");

    echo "<h1>Database Setup Complete!</h1><p>Tables are successfully created in the Railway database.</p><br><a href='login.php'>Click here to go to Login</a>";
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>