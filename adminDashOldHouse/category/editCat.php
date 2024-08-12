<?php
session_start(); // Start session to store feedback messages

include("../includes/connection2.php");

// Fetch the category details if cat_id is set in the URL
if (isset($_GET['cat_id'])) {
    $cat_id = $_GET['cat_id'];

    // Fetch the category details based on cat_id
    if ($stmt = $conn->prepare("SELECT cat_id, cat_name FROM Category WHERE cat_id = ?")) {
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Something went wrong: " . $conn->error);
    }
} else {
    // Redirect to a different page if cat_id is not set
    header("Location:manage_category.php");
    exit();
}

// Form processing for updating category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["edit_cat"])) {
    $cat_id = $_POST["Category_id"];
    $cat_name = $_POST["Category_Name"];

    if ($stmt = $conn->prepare("UPDATE Category SET cat_name = ? WHERE cat_id = ?")) {
        // Bind parameters
        $stmt->bind_param("si", $cat_name, $cat_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['message'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Something went wrong: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Something went wrong: " . $conn->error;
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?cat_id=" . $cat_id);
    exit();
}

include("../includes/header.php");
?>
<div class="d-flex flex-row-reverse">
    <a href="manage_category.php" class="btn btn-primary">Back</a>
</div>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Edit Category</strong> Form
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
        <form action="" method="post">
            <div class="form-group">
                <label for="Category_id" class="form-control-label">Category ID</label>
                <input type="text" id="Category_id" name="Category_id" value="<?php echo htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="Category_Name" class="form-control-label">Category Name</label>
                <input type="text" id="Category_Name" name="Category_Name" value="<?php echo htmlspecialchars($category['cat_name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Category Name..." class="form-control">
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="edit_cat" value="Edit Category">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>