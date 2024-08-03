<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="upload-script.php" method="post" enctype="multipart/form-data">
        Select image to upload:
        <input type="file" name="fileToUpload[]" id="fileToUpload" multiple="multiple">
        <input type="submit" value="Upload Image" name="submit">
    </form>
</body>

</html>

<?php

if (isset($_FILES['fileToUpload'])) {
    $total = count($_FILES['fileToUpload']['name']);
    $target_dir = "uploads/";
    $uploadOk = 1;

    // Check if image file is a actual image or fake image

    for ($i = 0; $i < $total; $i++) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
        if ($check !== false) {
            echo "<br>File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "<br>File is not an image.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error

        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"][$i]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
            echo "<br>The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"][$i])) . " has been uploaded.";
        } else {
            echo "<br>Sorry, there was an error uploading your file.";
        }
    }
}
?>