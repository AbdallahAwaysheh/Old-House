<?php
$hostname = "localhost";
$dbname = "oldhouse";
$username = "root";
$password = "";

try {
    $conn = new mysqli($hostname, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
