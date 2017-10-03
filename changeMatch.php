<?php
include("session.php");
include("matcher.php");
include("db.php");
include("boundingbox.php");
include("getUsers.php");
session_start();

if (isset($_POST['like'])) {
    post('like');
}

if (isset($_POST['nope'])) {
    post('nope');
}

function post($decision) {
    include("db.php");
    include("session.php");
    $user = $user_from_db['user'];
    $match = $_POST["$decision"];
    $col = $decision . "s";
    $input = "";
    
    $sql = "select $col from users where user='$match'";
    $query = mysqli_query($connection, $sql);
    $line = mysqli_fetch_assoc($query);
    $text = $line["$col"];
    echo $text;
    
    if (strlen($text) > 0) {
        if (strpos($text, $user) !== false) {
        } else {
            $input = $text . ",$user";
            $sql = "UPDATE users SET $col='$input' WHERE user='$match'";
            $query = mysqli_query($connection, $sql);
        }
    } else {
        $input = $user;
        $sql = "UPDATE users SET $col='$input' WHERE user='$match'";
        $query = mysqli_query($connection, $sql);
    }
}
header("Location: index.php");
exit;
?>