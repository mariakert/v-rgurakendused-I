<?php
include("db.php");
include("session.php");
include("entities.php");
session_start();
$id = $_SESSION['id'];
$text = "";

if (isset($_POST['textArea'])) {
    $text = mysqli_real_escape_string($connection, toHTMLEntities($_POST['textArea']));
}

if (mysqli_query($connection, "UPDATE users SET text='$text' WHERE id='$id'")) {
    echo "Records were updated successfully.";
} else {
    echo mysqli_error($connection);
}

header("Location: index.php");
?>