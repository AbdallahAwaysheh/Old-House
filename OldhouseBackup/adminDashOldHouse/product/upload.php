<?php
session_start(); // Start session to store feedback messages

include("../includes/connection2.php");

// Form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_pro"])) {
    $pro_name = $_POST["pro_name"];
    $pro_price = $_POST["pro_price"];
    $pro_desc = $_POST["pro_desc"];
    $cat_id = $_POST["cat_id"];
    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];

    // Validate and upload main photo
    $valid = 1;
    $error_message = '';

    if ($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $file_name = basename($path, '.' . $ext);
        $main_photo_name = $file_name . '_' . time() . '.' . $ext;
        $target = '../uploads/' . $main_photo_name;

        if ($ext != 'jpg' && $ext != 'png' && $ext != 'jpeg' && $ext != 'gif') {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }

        if ($valid == 1) {
            move_uploaded_file($path_tmp, $target);
        }
    }

    if ($valid == 1) {
        $sqlInsert = "INSERT INTO products (pro_name, pro_price, pro_desc, cat_id, pro_main_photo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("sdsss", $pro_name, $pro_price, $pro_desc, $cat_id, $main_photo_name);

            // Execute the statement
            if ($stmt->execute()) {
                $product_id = $stmt->insert_id;

                // Upload other photos
                foreach ($_FILES['photo']['name'] as $key => $val) {
                    $photo_name = $_FILES['photo']['name'][$key];
                    $photo_tmp = $_FILES['photo']['tmp_name'][$key];

                    if ($photo_name != '') {
                        $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
                        $file_name = basename($photo_name, '.' . $ext);
                        $photo_name_new = $file_name . '_' . time() . '.' . $ext;
                        $target = '../uploads/' . $photo_name_new;

                        if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
                            move_uploaded_file($photo_tmp, $target);

                            $sqlInsertPhoto = "INSERT INTO img_pro (pro_id, img_path) VALUES (?, ?)";
                            $stmtPhoto = $conn->prepare($sqlInsertPhoto);
                            $stmtPhoto->bind_param("is", $product_id, $photo_name_new);
                            $stmtPhoto->execute();
                            $stmtPhoto->close();
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
    } else {
        $_SESSION['error'] = $error_message;
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include("../includes/header.php");
?>

<!-- Display messages -->
<div class="card m-t-25">
    <div class="card-header">
        <strong>Add Product</strong> Form
    </div>
    <div class="card-body card-block">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        } elseif (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>