<?php
session_start(); // Start session to store feedback messages

include("../includes/connection2.php");

// Fetch the category details if cat_id is set in the URL
if (isset($_GET['cus_id'])) {
    $cus_id = $_GET['cus_id'];

    // Fetch the category details based on cat_id
    if ($stmt = $conn->prepare("SELECT cus_id,cus_fname,cus_lname,cus_email,mobile,shippingAddress,shippingCity from customers WHERE cus_id= ?")) {
        $stmt->bind_param("i", $cus_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Something went wrong: " . $conn->error);
    }
} else {
    // Redirect to a different page if cat_id is not set
    header("Location:manage_user.php");
    exit();
}

// Form processing for updating category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["edit_user"])) {
    $cus_id = $_POST["cus_id"];
    $cus_fname = $_POST["cus_fname"];
    $cus_lname = $_POST["cus_lname"];
    $cus_email = $_POST["cus_email"];
    $mobile = $_POST["mobile"];
    $shippingAddress = $_POST["shippingAddress"];
    $shippingCity = $_POST["shippingCity"];
    $cus_pass = $_POST["cus_pass"];

    $sqlUpdate = "UPDATE customers SET cus_fname = ?, cus_lname = ?,cus_email = ?, mobile = ?, shippingAddress = ?,shippingCity = ?, cus_pass= ? WHERE cus_id= ?";
    if ($stmt = $conn->prepare($sqlUpdate)) {
        // Bind parameters
        $stmt->bind_param("sssisssi", $cus_fname, $cus_lname, $cus_email, $mobile, $shippingAddress, $shippingCity, $cus_pass, $cus_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['message'] = "user updated successfully!";
        } else {
            $_SESSION['error'] = "Something went wrong: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Something went wrong: " . $conn->error;
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?cus_id=" . $cus_id);
    exit();
}

include("../includes/header.php");
?>
<div class="d-flex flex-row-reverse">
    <a href="manage_user.php" class="btn btn-primary">Back</a>
</div>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Edit Customer</strong> Form
    </div>
    <div class="card-body card-block">
        <?php
        // Display messages
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        } elseif (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <!-- start form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="cus_id" class="form-control-label">User ID</label>
                <input type="text" id="cus_id" name="cus_id" value="<?php echo htmlspecialchars($user['cus_id'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="cus_fname" class="form-control-label">User First Name</label>
                <input type="text" id="cus_fname" name="cus_fname" value="<?php echo htmlspecialchars($user['cus_fname'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User First Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cus_lname" class="form-control-label">User Last Name</label>
                <input type="text" id="cus_lname" name="cus_lname" value="<?php echo htmlspecialchars($user['cus_lname'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User Last Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cus_email" class="form-control-label">User Email</label>
                <input type="text" id="cus_email" name="cus_email" value="<?php echo htmlspecialchars($user['cus_email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User Email..." class="form-control">
            </div>

            <div class="form-group">
                <label for="mobile" class="form-control-label">User Mobile</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User Mobile..." class="form-control">
            </div>
            <div class="form-group">
                <label for="shippingAddress" class="form-control-label">Shipping Address</label>
                <input type="text" id="shippingAddress" name="shippingAddress" value="<?php echo htmlspecialchars($user['shippingAddress'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User Shipping Address..." class="form-control">
            </div>
            <div class="form-group">
                <label for="shippingCity" class="form-control-label">Shipping City</label>
                <input type="text" id="shippingCity" name="shippingCity" value="<?php echo htmlspecialchars($user['shippingCity'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter User Shipping City..." class="form-control">
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="edit_user" value="Edit User">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
        <!-- end form -->
    </div>
</div>

<?php include("../includes/footer.php"); ?>