<?php
include("../includes/connection2.php");
if (isset($_GET['cus_id'])) {
    $id = $_GET['cus_id'];
    $sql = "UPDATE customers SET delete_status = 'yes' WHERE cus_id ='$id'";
    if (mysqli_query($conn, $sql)) {
        session_start();
        $_SESSION["delete"] = "cus_id Deleted Successfully!";
        header("Location:manage_user.php");
    } else {
        die("Something went wrong");
    }
} else {
    echo "user does not exist";
}
