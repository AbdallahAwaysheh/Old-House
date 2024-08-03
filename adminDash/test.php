<?php

if(isset($_POST['add_pro'])){
    $file=$_FILES['productImages'];
    print_r($file);
}
// session_start();
// include("../includes/connection2.php");

// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pro_id"]) && !empty($_FILES['productImages']['name'][0])) {
//     $productId = $_POST["pro_id"];
//     $uploadDir = '../uploads/'; // Ensure this directory is writable and correctly set

//     foreach ($_FILES['productImages']['name'] as $key => $name) {
//         $tmpName = $_FILES['productImages']['tmp_name'][$key];
//         $uploadFile = $uploadDir . basename($name);

//         // Validate file upload
//         if (move_uploaded_file($tmpName, $uploadFile)) {
//             // Use MySQLi to insert image info into the database
//             $stmt = $conn->prepare("INSERT INTO img_pro (img_path, pro_id) VALUES (?, ?)");
//             if ($stmt) {
//                 $stmt->bind_param('si', $name, $productId);
//                 if (!$stmt->execute()) {
//                     // Handle potential database errors
//                     echo "Database error: " . htmlspecialchars($stmt->error);
//                 }
//                 $stmt->close();
//             } else {
//                 // Handle potential prepare statement errors
//                 echo "Database error: " . htmlspecialchars($conn->error);
//             }
//         } else {
//             // Handle file upload errors
//             echo "Failed to upload file: " . htmlspecialchars($name);
//         }
//     }

//     exit();
// }

// include("../includes/header.php");
?>

<!-- Your form and HTML content here -->

<?php include("../includes/footer.php"); ?>






<!-- Modal -->
<!-- Edit Form Modal Start -->
<!-- <div class="modal fade" id="addImageForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addImageForm" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFormLabel">add image Product Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">


                            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" class="forms-sample">
                                <input type="hidden" name="productId" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                <div class="form-group">
                                    <label for="productImage">Product Image</label>
                                    <input type="file" id="productImage" name="img[]" class="file-upload-default" multiple>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-gradient-primary" type="button">Upload</button>
                                        </span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Edit Form Modal End -->
<!-- <script>
    document.querySelector('.file-upload-browse').addEventListener('click', function() {
        document.querySelector('.file-upload-default').click();
    });

    document.querySelector('.file-upload-default').addEventListener('change', function() {
        var fileName = this.value.split('\\').pop();
        document.querySelector('.file-upload-info').value = fileName;
    });
</script> -->


<?php
session_start(); // Start session to store feedback messages

include("../includes/connection.php");

// Fetch categories from the database
$sqlFetchCategories = "SELECT cat_id, cat_name FROM Categories WHERE delete_status = 'no'";
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
    $uploadedFiles = [];
    $uploadDir = '../uploads/';

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert product into Products table
        $sqlInsertProduct = "INSERT INTO Products (pro_name, pro_price, pro_desc, cat_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsertProduct);
        $stmt->bind_param("sdss", $pro_name, $pro_price, $pro_desc, $cat_id);

        if ($stmt->execute()) {
            // Get the inserted product ID
            $productId = $stmt->insert_id;
            $stmt->close();

            // Handling file upload
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (isset($_FILES['productImages'])) {
                $files = $_FILES['productImages'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                for ($i = 0; $i < count($files['name']); $i++) {
                    $fileTmpPath = $files['tmp_name'][$i];
                    $fileName = basename($files['name'][$i]);
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $uploadFilePath = $uploadDir . $fileName;

                    if (in_array($fileExtension, $allowedExtensions)) {
                        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                            $uploadedFiles[] = $fileName;

                            // Insert image path into img_pro table
                            $sqlInsertImage = "INSERT INTO img_pro (img_path, pro_id) VALUES (?, ?)";
                            $stmt = $conn->prepare($sqlInsertImage);
                            $stmt->bind_param("si", $fileName, $productId);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            throw new Exception("Failed to move uploaded file: $fileName.");
                        }
                    } else {
                        throw new Exception("Invalid file type for file: $fileName. Only JPG, JPEG, PNG, and GIF files are allowed.");
                    }
                }
            } else {
                throw new Exception("No files uploaded.");
            }

            // Commit transaction
            $conn->commit();
            $_SESSION['message'] = "Product and images added successfully!";
        } else {
            throw new Exception("Failed to add product: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Product</strong> Form
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
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="pro_name" class="form-control-label">Product Name</label>
                <input type="text" id="pro_name" name="pro_name" placeholder="Enter Product Name..." class="form-control">
            </div>
            <div class="form-group">
                <label for="pro_price" class="form-control-label">Product Price</label>
                <input type="number" id="pro_price" name="pro_price" placeholder="Enter Price..." class="form-control">
            </div>
            <div class="form-group">
                <label for="pro_desc" class="form-control-label">Product Description</label>
                <input type="text" id="pro_desc" name="pro_desc" placeholder="Enter Description..." class="form-control">
            </div>
            <div class="form-group">
                <label for="cat_id" class="form-control-label">Category</label>
                <select id="cat_id" name="cat_id" class="form-control">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category['cat_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="productImages" class="form-control-label">Product Images</label>
                <input type="file" id="productImages" name="productImages[]" class="form-control-file" multiple required>
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="add_pro" value="Add Product">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- DATA TABLE -->
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.pro_id, p.pro_name, p.pro_price, p.pro_desc, c.cat_name 
                            FROM Products p 
                            INNER JOIN Categories c ON p.cat_id = c.cat_id 
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
                                        <a href="editPro.php?pro_id=<?php echo htmlspecialchars($row['pro_id'], ENT_QUOTES, 'UTF-8'); ?>" class="item">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="item" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?php echo $row['pro_id']; ?>)">
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
                        <td colspan='6' style='text-align: center;'>No Product Found</td>
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
    function confirmDelete(proId) {
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
                window.location.href = "deletePro.php?pro_id=" + proId;
            }
        });
    }
</script>

<?php include("../includes/footer.php"); ?>