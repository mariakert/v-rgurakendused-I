<?php
include("db.php");
if (empty($_SESSION)) {
    session_start();
}

if (empty($_SESSION['id'])) {
    header("Location: index.php");
    exit;
} else {
    $id = $_SESSION['id'];
    $query = mysqli_query($connection, "select * from users where id=$id");
    $user_from_db = mysqli_fetch_assoc($query);
}
?>