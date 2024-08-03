<?php
session_start();
include("../includes/connection2.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_cat"])) {
    $cat_name = trim($_POST["Category_Name"]);
    $cat_image = null;

    if (!empty($cat_name)) {
        // Handling file upload
        if (isset($_FILES['catImage']) && $_FILES['catImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileTmpPath = $_FILES['catImage']['tmp_name'];
            $fileName = basename($_FILES['catImage']['name']);
            $uploadFilePath = $uploadDir . $fileName;

            // Attempt to move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                $cat_image = $fileName;
            } else {
                $_SESSION['error'] = "Failed to move uploaded file.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            $_SESSION['error'] = "File upload error: " . $_FILES['catImage']['error'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        try {
            $sqlInsert = "INSERT INTO Category (cat_name, cat_img) VALUES (?, ?)";
            $stmt = $conn->prepare($sqlInsert);

            $stmt->bind_param("ss", $cat_name, $cat_image);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Category added successfully!";
            } else {
                throw new Exception("Failed to add category.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "Category name cannot be empty.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch categories
$categories = [];
try {
    $sql = "SELECT cat_id, cat_name FROM Category WHERE delete_status = 'no' ORDER BY cat_id ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $result->free();
    } else {
        throw new Exception("Failed to fetch categories.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

$conn->close();

include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Category</strong> Form
    </div>
    <div class="card-body card-block">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
        } elseif (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Category_Name" class="form-control-label">Category Name</label>
                <input type="text" id="Category_Name" name="Category_Name" placeholder="Enter Category..." class="form-control" required>
            </div>
            <div class="form-group">
                <label for="catImage" class="form-control-label">Category Image</label>
                <input type="file" id="catImage" name="catImage" class="form-control-file" required>
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="add_cat" value="Add Category">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- DATA TABLE -->
        <h3 class="title-5 m-b-35">Category List</h3>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $category) : ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($category["cat_id"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($category["cat_name"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="editCat.php?cat_id=<?= htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8') ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="item" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?= $category['cat_id']; ?>)">
                                            <i class="zmdi zmdi-delete"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="tr-shadow">
                            <td colspan="3" style="text-align: center;">No Category Found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- END DATA TABLE -->
    </div>
</div>
<script>
    function confirmDelete(catId) {
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
                window.location.href = "deleteCat.php?cat_id=" + catId;
            }
        });
    }
</script>

<?php include("../includes/footer.php"); ?>