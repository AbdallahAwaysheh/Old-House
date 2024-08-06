<?php
session_start();

include("../includes/connection2.php");

// Fetch categories from the database for the dropdown
$sqlFetchCategories = "SELECT cat_id, cat_name FROM category";
if ($stmt = $conn->prepare($sqlFetchCategories)) {
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Something went wrong: " . $conn->error);
}

// Fetch product details if pro_id is set in the URL
if (isset($_GET['pro_id'])) {
    $pro_id = $_GET['pro_id'];

    $sqlFetchProduct = "SELECT p.pro_id, p.pro_name, p.pro_price, p.pro_desc, p.cat_id FROM products p WHERE p.pro_id = ?";
    if ($stmt = $conn->prepare($sqlFetchProduct)) {
        $stmt->bind_param('i', $pro_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Something went wrong: " . $conn->error);
    }
} else {
    // Redirect to a different page if pro_id is not set
    header("Location: manage_products.php");
    exit();
}

// Form processing for updating product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["edit_pro"])) {
    $pro_name = $_POST["pro_name"];
    $pro_price = $_POST["pro_price"];
    $pro_desc = $_POST["pro_desc"];
    $cat_id = $_POST["cat_id"];

    $sqlUpdate = "UPDATE products SET pro_name = ?, pro_price = ?, pro_desc = ?, cat_id = ? WHERE pro_id = ?";
    if ($stmt = $conn->prepare($sqlUpdate)) {
        // Bind parameters
        $stmt->bind_param('sdsii', $pro_name, $pro_price, $pro_desc, $cat_id, $pro_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['message'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Something went wrong: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Something went wrong: " . $conn->error;
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?pro_id=" . $pro_id);
    exit();
}

include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Edit Product</strong> Form
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
                <label for="pro_name" class="form-control-label">Product Name</label>
                <input type="text" id="pro_name" name="pro_name" value="<?php echo htmlspecialchars($product['pro_name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Product Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="pro_price" class="form-control-label">Product Price</label>
                <input type="number" id="pro_price" name="pro_price" value="<?php echo htmlspecialchars($product['pro_price'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Price..." class="form-control">
            </div>
            <div class="form-group">
                <label for="pro_desc" class="form-control-label">Product Description</label>
                <input type="text" id="pro_desc" name="pro_desc" value="<?php echo htmlspecialchars($product['pro_desc'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Description..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cat_id" class="form-control-label">Category</label>
                <select id="cat_id" name="cat_id" class="form-control">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php if ($category['cat_id'] == $product['cat_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['cat_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="edit_pro" value="Edit Product">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>