<?php
function register($user, $pass, $name, $email, $sex) {
    include("db.php");
    $user = mysqli_real_escape_string($connection, $user);
    $pass = mysqli_real_escape_string($connection, $pass);
    $name = mysqli_real_escape_string($connection, $name);
    $email = mysqli_real_escape_string($connection, $email);
    $sex = mysqli_real_escape_string($connection, $sex);
    
    if (mysqli_query($connection, 
                          "INSERT INTO users (user, pass, email, name, sex) VALUES ('$user', '$pass', '$email', '$name', '$sex');")) {
        $_SESSION['id'] = mysqli_insert_id($connection);
        $_SESSION['name'] = $name;
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        return "Username and/or email already taken";
    }
}
?>