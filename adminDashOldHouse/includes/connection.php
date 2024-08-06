<?php

$host = "localhost";
$dbname = "E_commerce";
$user = "root";
$pass = "";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset to utf8mb4 (optional, but recommended)
    $conn->set_charset("utf8mb4");

    // Enable error reporting (similar to PDO::ERRMODE_EXCEPTION)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// If you need to close the connection later:
// $conn->close();
