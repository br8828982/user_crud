<?php
$host = "SG-purple-tango-7911-7753-mysql-master.servers.mongodirector.com"; // Your database host
$dbname = "abc"; // Your database name
$username = "sgroot"; // Your database username
$password = "111Ua5^Pum4aTJx4"; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}
