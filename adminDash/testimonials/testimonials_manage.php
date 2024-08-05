<?php
session_start();
include("../includes/connection2.php");

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
                $_SESSION['message'] = "testimonial added successfully!";
            } else {
                throw new Exception("Failed to add testimonial.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "author name and content cannot be empty.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// fetch testimonial
$testimonials = [];
try {
    $sql = "select test_id,test_content,author from testimonials WHERE  delete_status = 'no' order by test_id ";
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
$conn->close();

include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Add testimonial</strong> Form
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
                <input type="text" id="Testimonial_Content" name="Testimonial_Content" placeholder="Enter Content..." class="form-control" required>
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
                        <?php foreach ($testimonials  as $testimonial) : ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($testimonial["test_id"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($testimonial["test_content"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($testimonial["author"], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="update_test.php?test_id=<?= htmlspecialchars($testimonial['test_id'], ENT_QUOTES, 'UTF-8') ?>" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="item" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?= $testimonial['test_id']; ?>)">
                                            <i class="zmdi zmdi-delete"></i>
                                        </a>
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
                window.location.href = "delete_test.php?test_id=" + catId;
            }
        });
    }
</script>
<?php
include("../includes/footer.php");
?>