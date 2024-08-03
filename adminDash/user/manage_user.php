<?php
session_start();
include("../includes/connection2.php");

// Fetch users from the database
$sqlFetchUsers = "SELECT cus_id, cus_fname, cus_lname, cus_email, mobile, shippingAddress, shippingCity FROM customers WHERE delete_status = 'no'";
$result = $conn->query($sqlFetchUsers);

if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Something went wrong: " . $conn->error);
}

// Form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_Customer"])) {
    $cus_fname = $_POST["cus_fname"];
    $cus_lname = $_POST["cus_lname"];
    $cus_email = $_POST["cus_email"];
    $mobile = $_POST["mobile"];
    $shippingAddress = $_POST["shippingAddress"];
    $shippingCity = $_POST["shippingCity"];
    $cus_pass = $_POST["cus_pass"];

    $sqlInsert = "INSERT INTO customers (cus_fname, cus_lname, cus_email, mobile, shippingAddress, shippingCity, cus_pass) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sssssss", $cus_fname, $cus_lname, $cus_email, $mobile, $shippingAddress, $shippingCity, $cus_pass);

        // Execute the statement
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['message'] = "Customer added successfully!";
        } else {
            $_SESSION['error'] = "Something went wrong: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Something went wrong: " . $conn->error;
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Customer</strong> Form
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
                <label for="cus_fname" class="form-control-label">User First Name</label>
                <input type="text" id="cus_fname" name="cus_fname" placeholder="Enter User First Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cus_lname" class="form-control-label">User Last Name</label>
                <input type="text" id="cus_lname" name="cus_lname" placeholder="Enter User Last Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cus_email" class="form-control-label">User Email</label>
                <input type="text" id="cus_email" name="cus_email" placeholder="Enter User Email..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cus_pass" class="form-control-label">User Password</label>
                <input type="text" id="cus_pass" name="cus_pass" placeholder="Enter User Password..." class="form-control">
            </div>
            <div class="form-group">
                <label for="mobile" class="form-control-label">User Mobile</label>
                <input type="text" id="mobile" name="mobile" placeholder="Enter User Mobile..." class="form-control">
            </div>
            <div class="form-group">
                <label for="shippingAddress" class="form-control-label">Shipping Address</label>
                <input type="text" id="shippingAddress" name="shippingAddress" placeholder="Enter User Shipping Address..." class="form-control">
            </div>
            <div class="form-group">
                <label for="shippingCity" class="form-control-label">Shipping City</label>
                <input type="text" id="shippingCity" name="shippingCity" placeholder="Enter User Shipping City..." class="form-control">
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="add_Customer" value="Add Customer">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
        <!-- end form -->
    </div>
</div>

<div class="row overflow">
    <div class="col-md-12">
        <!-- DATA TABLE -->
        <h3 class="title-5 m-b-35">Manage Users</h3>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User First Name</th>
                        <th>User Last Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <?php
                    if ($users) {
                        foreach ($users as $row) {
                    ?>
                            <tr class="tr-shadow">
                                <td><?php echo $row["cus_id"] ?></td>
                                <td><?php echo $row["cus_fname"] ?></td>
                                <td><?php echo $row["cus_lname"] ?></td>
                                <td><?php echo $row["cus_email"] ?></td>
                                <td><?php echo $row["mobile"] ?></td>
                                <td><?php echo $row["shippingAddress"] ?></td>
                                <td><?php echo $row["shippingCity"] ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="edit_user.php?cus_id=<?php echo htmlspecialchars($row['cus_id'], ENT_QUOTES, 'UTF-8'); ?>" class="item">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="item" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?php echo $row['cus_id']; ?>)">
                                            <i class="zmdi zmdi-delete"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                    <?php
                        }
                    } else {
                        echo "<tr class='tr-shadow'>
                        <td colspan='8' style='text-align: center;'>No users found</td>
                        </tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <!-- END DATA TABLE -->
    </div>
</div>
<script>
    function confirmDelete(userID) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the delete page
                window.location.href = "delete_user.php?cus_id=" + userID;
            }
        });
    }
</script>
<?php
include("../includes/footer.php");
?>