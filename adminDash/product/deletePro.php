<?php
if (isset($_GET['pro_id'])) {
    include("../includes/connection2.php");
    $id = $_GET['pro_id'];
    $sql = "UPDATE products SET delete_status = 'yes' WHERE pro_id ='$id'";
    if (mysqli_query($conn, $sql)) {
        session_start();
        $_SESSION["delete"] = "product Deleted Successfully!";
        header("Location:manage_product.php");
    } else {
        die("Something went wrong");
    }
} else {
    echo "product does not exist";
}
