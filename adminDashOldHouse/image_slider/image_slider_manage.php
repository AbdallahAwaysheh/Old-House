<?php
session_start(); // Start session to store feedback messages

include("../includes/connection2.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_slide"])) {
    // Upload slider images
    $valid = 1;
    $error_message = '';

    foreach ($_FILES['photo']['name'] as $key => $val) {
        $photo_name = $_FILES['photo']['name'][$key];
        $photo_tmp = $_FILES['photo']['tmp_name'][$key];

        if ($photo_name != '') {
            $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
            $file_name = basename($photo_name, '.' . $ext);
            $photo_name_new = $file_name . '_' . time() . '.' . $ext;
            $target = '../uploads/' . $photo_name_new;

            if ($ext != 'jpg' && $ext != 'png' && $ext != 'jpeg' && $ext != 'gif') {
                $valid = 0;
                $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
            }

            if ($valid == 1) {
                if (move_uploaded_file($photo_tmp, $target)) {
                    $sqlInsertPhoto = "INSERT INTO img_slider (img_path) VALUES (?)";
                    $stmtPhoto = $conn->prepare($sqlInsertPhoto);
                    $stmtPhoto->bind_param("s", $photo_name_new);
                    $stmtPhoto->execute();
                    $stmtPhoto->close();
                    $_SESSION['message'] = "Images uploaded successfully!";
                } else {
                    $error_message .= 'Failed to upload photo ' . $photo_name . '<br>';
                    $valid = 0;
                }
            }
        }
    }

    if ($valid == 0) {
        $_SESSION['error'] = $error_message;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


include("../includes/header.php");
?>

<div class="card m-t-25">
    <div class="card-header">
        <strong>Image Slider</strong> Form
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
                <label for="" class="col-sm-3 control-label">Slider's Images</label>
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
            <input type="submit" class="btn btn-primary btn-sm" name="add_slide" value="Add Image">
            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </form>
    </div>
</div>

<script>
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
include("../includes/footer.php");
?>