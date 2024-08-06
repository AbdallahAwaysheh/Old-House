<?php
session_start();

include("../includes/connection2.php");

if (isset($_GET["test_id"])) {
    $test_id = $_GET["test_id"];
    $sql = 'SELECT test_id, test_content, author FROM testimonials WHERE delete_status = "no" AND test_id = ?';
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $testimonials = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Something went wrong: " . $conn->error);
    }
} else {
    header("Location: testimonials_manage.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["edit_test"])) {
    $test_cont = trim($_POST['Testimonial_Content']);
    $test_author = trim($_POST["Testimonial_Author"]);

    $sqlInsert = "UPDATE testimonials SET test_content = ?, author = ? WHERE test_id = ?";

    if ($stmt = $conn->prepare($sqlInsert)) {
        try {
            $stmt->bind_param("ssi", $test_cont, $test_author, $test_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Testimonial updated successfully!";
            } else {
                throw new Exception("Failed to update testimonial.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "Author name and content cannot be empty.";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?test_id=" . $test_id);
    exit();
}

include("../includes/header.php");
?>
<div class="card m-t-25">
    <div class="card-header">
        <strong>Edit Testimonial</strong> Form
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
                <label for="Testimonial_id" class="form-control-label">Testimonial ID</label>
                <input type="text" id="Testimonial_id" name="Testimonial_id" value="<?php echo htmlspecialchars($testimonials["test_id"], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="Testimonial_Content" class="form-control-label">Testimonial Content</label>
                <input type="text" id="Testimonial_Content" name="Testimonial_Content" value="<?php echo htmlspecialchars($testimonials["test_content"], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="Testimonial_Author" class="form-control-label">Testimonial Author</label>
                <input type="text" id="Testimonial_Author" name="Testimonial_Author" value="<?php echo htmlspecialchars($testimonials["author"], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required>
            </div>
            <input type="submit" class="btn btn-primary btn-sm" name="edit_test" value="Update Testimonial">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<?php
include("../includes/footer.php");
?>