<?php
session_start();
include("../includes/connection2.php");

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    echo "<div class='alert alert-{$type}'>{$message}</div>";
}

// Fetch users from the database
$sqlFetchUsers = "SELECT cus_id, cus_fname, cus_lname, cus_email, mobile, shippingAddress, shippingCity FROM customers WHERE delete_status = 'no'";
$result = $conn->query($sqlFetchUsers);

// Form processing for adding a customer
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

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Customer added successfully!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Something went wrong: " . $stmt->error;
        $_SESSION['flash_type'] = "error";
    }
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Something went wrong: " . $conn->error);
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cus_id'])) {
    $deleteSuccess = false;
    $cusID = $_POST['cus_id'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE customers SET delete_status = 'yes' WHERE cus_id = ?");
        $stmt->bind_param("i", $cusID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $deleteSuccess = true;
        $_SESSION['delete_success'] = true;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['delete_error'] = "Error deleting customer: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($deleteSuccess)) : ?>
    <script>
        window.onload = function() {
            <?php if ($deleteSuccess) : ?>
                Swal.fire('Deleted!', 'The customer has been deleted successfully.', 'success');
            <?php else : ?>
                Swal.fire('Error!', '<?php echo $errorMessage; ?>', 'error');
            <?php endif; ?>
        }
    </script>
<?php endif; ?>

<?php include("../includes/header.php"); ?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Customer</strong> Form
    </div>
    <div class="card-body card-block">
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
    </div>
</div>

<div class="row overflow">
    <div class="col-md-12">
        <h3 class="title-5 m-b-35">Manage Users</h3>
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
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
                                        <a href="edit_user.php?cus_id=<?php echo htmlspecialchars($row['cus_id'], ENT_QUOTES, 'UTF-8'); ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form id='deleteForm<?php echo $row['cus_id']; ?>' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' style='display:inline;'>
                                            <input type='hidden' name='cus_id' value='<?php echo $row['cus_id']; ?>'>
                                            <button type='button' onclick='confirmDelete(<?php echo $row["cus_id"]; ?>)' class="item" data-toggle="tooltip" data-placement="top" title="Delete">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </form>
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
    </div>
</div>

<script>
    function confirmDelete(userID) {
        Swal.fire({
            title: 'Are You Sure You Want To Delete',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set a flag in session storage to indicate deletion was confirmed
                sessionStorage.setItem('deleteConfirmed', 'true');
                document.getElementById('deleteForm' + userID).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Check if deletion was confirmed and successful
        if (sessionStorage.getItem('deleteConfirmed') === 'true') {
            sessionStorage.removeItem('deleteConfirmed');
            <?php
            if (isset($_SESSION['delete_success'])) {
                unset($_SESSION['delete_success']);
                echo "Swal.fire('Deleted!', 'The customer has been deleted successfully.', 'success');";
            } elseif (isset($_SESSION['delete_error'])) {
                $errorMessage = $_SESSION['delete_error'];
                unset($_SESSION['delete_error']);
                echo "Swal.fire('Error!', '" . addslashes($errorMessage) . "', 'error');";
            }
            ?>
        }
    });
</script>

<?php include("../includes/footer.php"); ?>