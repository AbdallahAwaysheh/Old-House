<?php
if (isset($_GET['test_id'])) {
    include("../includes/connection2.php");
    $id = $_GET['test_id'];
    $sql = "UPDATE testimonials SET delete_status = 'yes' WHERE test_id ='$id'";
    if (mysqli_query($conn, $sql)) {
        session_start();
        $_SESSION["delete"] = "testimonial Deleted Successfully!";
        header("Location:testimonials_manage.php");
    } else {
        die("Something went wrong");
    }
} else {
    echo "testimonial does not exist";
}
