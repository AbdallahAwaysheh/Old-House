<?php
session_start(); // Start session to store feedback messages

include("../includes/connection2.php");

// Fetch categories from the database
$sqlFetchCategories = "SELECT cat_id, cat_name FROM category WHERE delete_status = 'no'";
$result = $conn->query($sqlFetchCategories);

if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Something went wrong: " . $conn->error);
}

// Form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_pro"])) {
    $pro_name = $_POST["pro_name"];
    $pro_price = $_POST["pro_price"];
    $pro_desc = $_POST["pro_desc"];
    $cat_id = $_POST["cat_id"];
    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];
    $error_message = '';

    if ($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext != 'jpg' && $ext != 'png' && $ext != 'jpeg' && $ext != 'gif') {
            $error_message .= 'You must upload a jpg, jpeg, gif, or png file.<br>';
        }
    }

    if ($error_message == '') {
        $sqlInsert = "INSERT INTO products (pro_name, pro_price, pro_desc, cat_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("sdss", $pro_name, $pro_price, $pro_desc, $cat_id);

            // Execute the statement
            if ($stmt->execute()) {
                $product_id = $stmt->insert_id; // Get the last inserted product ID

                // Handle the main photo upload
                if ($path != '') {
                    $upload_dir = '../uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $final_name = 'product_' . $product_id . '.' . $ext;
                    $upload_path = $upload_dir . $final_name;

                    if (move_uploaded_file($path_tmp, $upload_path)) {
                        // Update the product with the main photo path
                        $sqlUpdatePhoto = "UPDATE products SET pro_main_photo = ? WHERE pro_id = ?";
                        $stmtUpdate = $conn->prepare($sqlUpdatePhoto);
                        $stmtUpdate->bind_param("si", $final_name, $product_id);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                    } else {
                        $_SESSION['error'] = "Failed to upload main photo.";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                }

                // Handle other photos upload
                if (isset($_FILES['photo']['name']) && !empty($_FILES['photo']['name'][0])) {
                    $upload_dir = '../uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    foreach ($_FILES['photo']['name'] as $key => $value) {
                        $photo_name = $_FILES['photo']['name'][$key];
                        $photo_tmp = $_FILES['photo']['tmp_name'][$key];
                        $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);

                        if (in_array($photo_ext, ['jpg', 'png', 'jpeg', 'gif'])) {
                            $final_photo_name = 'product_' . $product_id . '_' . $key . '.' . $photo_ext;
                            $upload_path = $upload_dir . $final_photo_name;

                            if (move_uploaded_file($photo_tmp, $upload_path)) {
                                // Insert the photo path into the img_pro table
                                $sqlInsertPhoto = "INSERT INTO img_pro (pro_id, img_path) VALUES (?, ?)";
                                $stmtInsertPhoto = $conn->prepare($sqlInsertPhoto);
                                $stmtInsertPhoto->bind_param("is", $product_id, $final_photo_name);
                                $stmtInsertPhoto->execute();
                                $stmtInsertPhoto->close();
                            } else {
                                $_SESSION['error'] = "Failed to upload one or more additional photos.";
                                header("Location: " . $_SERVER['PHP_SELF']);
                                exit();
                            }
                        }
                    }
                }

                $_SESSION['message'] = "Product added successfully!";
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
    } else {
        $_SESSION['error'] = $error_message;
    }
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_button'])) {
    $catID = $_POST['delete_button'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE Category SET delete_status = 'yes' WHERE cat_id = ?");
        $stmt->bind_param("i", $catID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $_SESSION['delete_success'] = true;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['delete_error'] = "Error deleting category: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<?php include("../includes/header.php"); ?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Product</strong> Form
    </div>
    <div class="card-body card-block">
        <?php
        // Display messages
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
        } elseif (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="pro_name" class="form-control-label">Product Name</label>
                <input type="text" id="pro_name" name="pro_name" placeholder="Enter Product Name..." class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pro_price" class="form-control-label">Product Price</label>
                <input type="number" id="pro_price" name="pro_price" placeholder="Enter Price..." class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pro_desc" class="form-control-label">Product Description</label>
                <textarea id="pro_desc" name="pro_desc" placeholder="Enter Description..." class="form-control" rows="6" required></textarea>
            </div>
            <div class="form-group">
                <label for="cat_id" class="form-control-label">Category</label>
                <select id="cat_id" name="cat_id" class="form-control" required>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category['cat_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="p_featured_photo" class="col-sm-3 control-label">Main Photo</label>
                <div class="col-sm-4" style="padding-top:4px;">
                    <input type="file" name="p_featured_photo" required>
                </div>
            </div>
            <div class="form-group">
                <label for="other_photos" class="col-sm-3 control-label">Other Photos</label>
                <div class="col-sm-4" style="padding-top:4px;">
                    <table id="ProductTable" style="width:100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="upload-btn">
                                        <input type="file" name="photo[]" style="margin-bottom:5px;">
                                    </div>
                                </td>
                                <td style="width:28px;"><a href="javascript:void(0);" class="Delete btn btn-danger btn-xs">X</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-2">
                    <input type="button" id="btnAddNew" value="Add Item" style="margin-top: 5px;margin-bottom:10px;border:0;color: #fff;font-size: 14px;border-radius:3px;" class="btn btn-warning btn-xs">
                </div>
            </div>

            <input type="submit" class="btn btn-primary btn-sm" name="add_pro" value="Add Product">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>

    </div>
</div>

<div class="row overflow">
    <div class="col-md-12">

        <h3 class="title-5 m-b-35">Data Table</h3>
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Description</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.pro_id, p.pro_name, p.pro_price, p.pro_desc, c.cat_name 
                            FROM products p 
                            INNER JOIN category c ON p.cat_id = c.cat_id 
                            WHERE p.delete_status = 'no'";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr class="tr-shadow">
                                <td><?php echo htmlspecialchars($row["pro_id"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row["pro_name"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row["pro_price"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row["pro_desc"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row["cat_name"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="editPro.php?pro_id=<?php echo htmlspecialchars($row['pro_id'], ENT_QUOTES, 'UTF-8'); ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form id='deleteForm<?php echo $row['pro_id']; ?>' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' style='display:inline;'>
                                            <input type='hidden' name='cus_id' value='<?php echo $row['pro_id']; ?>'>
                                            <button type='button' onclick='confirmDelete(<?php echo $row["pro_id"]; ?>)' class='btn btn-danger btn-sm'>
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
                        <td colspan='6' style='text-align: center;'>No Product Found</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    function confirmDelete(catID) {
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
                sessionStorage.setItem('deleteConfirmed', 'true');
                document.getElementById('deleteForm' + catID).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (sessionStorage.getItem('deleteConfirmed') === 'true') {
            sessionStorage.removeItem('deleteConfirmed');
            <?php
            if (isset($_SESSION['delete_success'])) {
                unset($_SESSION['delete_success']);
                echo "Swal.fire('Deleted!', 'The category has been deleted successfully.', 'success');";
            } elseif (isset($_SESSION['delete_error'])) {
                $errorMessage = $_SESSION['delete_error'];
                unset($_SESSION['delete_error']);
                echo "Swal.fire('Error!', '" . addslashes($errorMessage) . "', 'error');";
            }
            ?>
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Add new row
        document.getElementById("btnAddNew").addEventListener("click", function() {
            var newRow = document.createElement("tr");

            newRow.innerHTML = `
            <td>
                <div class="upload-btn">
                    <input type="file" name="photo[]" style="margin-bottom:15px;">
                </div>
            </td>
            <td style="width:28px;">
                <a href="javascript:void(0);" class="Delete btn btn-danger btn-xs">X</a>
            </td>
        `;

            document.querySelector("#ProductTable tbody").appendChild(newRow);
        });

        // Delegate event for dynamically added delete buttons
        document.querySelector("#ProductTable").addEventListener("click", function(event) {
            if (event.target && event.target.matches("a.Delete")) {
                var row = event.target.closest("tr");
                row.style.transition = "opacity 0.5s";
                row.style.opacity = 0;
                setTimeout(function() {
                    row.remove();
                }, 500);
                event.preventDefault();
            }
        });
    });
</script>

<?php
$conn->close();
include("../includes/footer.php"); ?>