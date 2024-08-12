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

// add testimonial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_test"])) {
    $test_cont = trim($_POST['Testimonial_Content']);
    $test_author = trim($_POST["Testimonial_Author"]);

    if (!empty($test_cont) && !empty($test_author)) {
        try {
            $sqlInsert = "INSERT INTO testimonials (test_content, author) VALUES (?, ?)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bind_param("ss", $test_cont, $test_author);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Testimonial added successfully!";
            } else {
                throw new Exception("Failed to add testimonial.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "Author name and content cannot be empty.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// fetch testimonials
$testimonials = [];
try {
    $sql = "SELECT test_id, test_content, author FROM testimonials WHERE delete_status = 'no' ORDER BY test_id";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $testimonials[] = $row;
        }
        $result->free();
    } else {
        throw new Exception("Failed to fetch testimonials.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['test_id'])) {
    $deleteSuccess = false;
    $testID = $_POST['test_id'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE testimonials SET delete_status = 'yes' WHERE test_id = ?");
        $stmt->bind_param("i", $testID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $deleteSuccess = true;
        $_SESSION['delete_success'] = true;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['delete_error'] = "Error deleting testimonial: " . $e->getMessage();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<?php if (isset($deleteSuccess)) : ?>
    <script>
        window.onload = function() {
            <?php if ($deleteSuccess) : ?>
                Swal.fire('Deleted!', 'The testimonial has been deleted successfully.', 'success');
            <?php else : ?>
                Swal.fire('Error!', '<?php echo $errorMessage; ?>', 'error');
            <?php endif; ?>
        }
    </script>
<?php endif; ?>

<?php include("../includes/header.php"); ?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Testimonial</strong> Form
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
                <label for="Testimonial_Content" class="form-control-label">Testimonial Content</label>
                <textarea type="text" id="Testimonial_Content" name="Testimonial_Content" class="form-control" rows="6" required></textarea>
            </div>
            <div class="form-group">
                <label for="Testimonial_Author" class="form-control-label">Testimonial Author</label>
                <input type="text" id="Testimonial_Author" name="Testimonial_Author" placeholder="Enter Author..." class="form-control" required>
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="add_test" value="Add Testimonial">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<div class="row overflow">
    <div class="col-md-12">
        <!-- DATA TABLE -->
        <h3 class="title-5 m-b-35">Testimonial List</h3>
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Testimonial ID</th>
                        <th>Testimonial Content</th>
                        <th>Testimonial Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($testimonials)) : ?>
                        <?php foreach ($testimonials as $testimonial) : ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($testimonial["test_id"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($testimonial["test_content"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($testimonial["author"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="update_test.php?test_id=<?= htmlspecialchars($testimonial['test_id'], ENT_QUOTES, 'UTF-8') ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form id='deleteForm<?= $testimonial['test_id']; ?>' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' style='display:inline;'>
                                            <input type='hidden' name='test_id' value='<?= $testimonial['test_id']; ?>'>
                                            <button type='button' onclick='confirmDelete(<?= $testimonial["test_id"]; ?>)' class="item" data-toggle="tooltip" data-placement="top" title="Delete">
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
                            <td colspan="4" style="text-align: center;">No Testimonial Found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- END DATA TABLE -->
    </div>
</div>

<script>
    function confirmDelete(testId) {
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
                window.location.href = "delete_test.php?test_id=" + testId;
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
                echo "Swal.fire('Deleted!', 'The testimonial has been deleted successfully.', 'success');";
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