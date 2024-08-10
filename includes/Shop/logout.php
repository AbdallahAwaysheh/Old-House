<?php
include("./user.inc.php");
$user = new User();

if (isset($_SERVER['HTTP_REFERER'])) {
    $user->logout();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
