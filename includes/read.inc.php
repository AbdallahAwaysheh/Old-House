<?php
include("./connect.php");

$sql = "SELECT * FROM categories;";

$result = $conn->query($sql);

$categorie = $result->fetch_assoc();
