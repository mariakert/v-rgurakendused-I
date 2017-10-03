<?php
session_start();
function upload_my_file($fileid) {
    include("db.php");
    echo "starting";
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    echo "<p>$target_file " . $target_file;
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    echo "<p>$imageFileType " . $imageFileType;
    $saved_file = $target_dir . $fileid . "." . $imageFileType;
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    echo "all is fine before checks";
    // Check if file already exists
    if (file_exists($target_file)) {
        if (unlink($target_file)) {
            echo "SUCCESS";
        } else {
            echo "FAILURE";
        }
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file(
            $_FILES["fileToUpload"]["tmp_name"], $saved_file)) {
            echo "<p>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            $id = $_SESSION['id'];
            $photo = "$fileid.$imageFileType";
            $sql = "update users set photo='$photo' where id='$id'";
            mysqli_query($connection, $sql);
        } else {
            echo "<p>Sorry, there was an error uploading your file.";
        }
    }
    header("Location: index.php");
}

upload_my_file($_SESSION['id']);
?>