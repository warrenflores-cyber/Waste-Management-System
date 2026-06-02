<?php
$host = getenv('MYSQLHOST') ?: 'acela.proxy.rlwy.net';
$port = getenv('MYSQLPORT') ?: '55553';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'nZtDOmqKSaGsBQJNEdfpAFQdWjDRAzHv';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    // Tell PDO to throw errors so we can catch them
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>