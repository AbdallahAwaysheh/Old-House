<?php
$hostname = "localhost";
$dbname = "e-commerce";
$username = "root";
$password = "";


$conn = new mysqli($hostname, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
