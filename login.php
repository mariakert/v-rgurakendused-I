<?php
function logIn($userPost, $passPost) {
    include('db.php');
    $user = mysqli_real_escape_string($connection, $userPost);
    $pass = mysqli_real_escape_string($connection, $passPost);

    if ($query = mysqli_query($connection, 
                          "select * from users where user='$user' and pass='$pass'")) {
        $rowCount = mysqli_num_rows($query);
        $row = mysqli_fetch_assoc($query);

        if ($rowCount == 1) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        } else {
            return "Username or password is invalid";
        }
    } else {
        return "Logging in failed";
    }
}
?>