<?php
session_start();
include("../includes/connection2.php");

// Form processing for adding a category
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
    $sql = "SELECT cat_id, cat_name FROM Category WHERE delete_status = 'no' ORDER BY cat_id";
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

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_button'])) {
    // $deleteSuccess = false;
    $catID = $_POST['delete_button'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE Category SET delete_status = 'yes' WHERE cat_id = ?");
        $stmt->bind_param("i", $catID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        // $deleteSuccess = true;
        $_SESSION['delete_success'] = true;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['delete_error'] = "Error deleting category: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<?php include("../includes/header.php"); ?>

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

<div class="row overflow">
    <div class="col-md-12">
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
                                <td class=" d-flex  align-items-center">
                                    <div class="table-data-feature">
                                        <a href="editCat.php?cat_id=<?= htmlspecialchars($category['cat_id'], ENT_QUOTES, 'UTF-8') ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form id='deleteForm<?= $category['cat_id']; ?>' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' style='display:inline;'>
                                            <input type='hidden' name='delete_button' value='<?= $category['cat_id']; ?>'>
                                            <button type='button' onclick='confirmDelete(<?= $category["cat_id"]; ?>)' class="item" data-toggle="tooltip" data-placement="top" title="Delete">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </form>
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
</script>

<?php include("../includes/footer.php"); ?>