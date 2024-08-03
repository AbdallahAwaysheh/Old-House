<?php
if (isset($_GET['cat_id'])) {
    include("../includes/connection.php");
    $id = $_GET['cat_id'];
    $sql = "UPDATE Categories SET delete_status = 'yes' WHERE cat_id ='$id'";
    if (mysqli_query($conn, $sql)) {
        session_start();
        $_SESSION["delete"] = "category Deleted Successfully!";
        header("Location:manage_category.php");
    } else {
        die("Something went wrong");
    }
} else {
    echo "category does not exist";
}
