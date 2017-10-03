<?php
function getUsers() {
    include("db.php");
    include("session.php");
    $gender = $user_from_db['sex'];
    $user = $user_from_db['user'];
    $sql = "";
    $users = [];
    if ($gender == "F") {
        $sql = "select * from users where sex='M'";
    } else {
        $sql = "select * from users where sex='F'";
    }
    $query = mysqli_query($connection, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            if (strpos($row['likes'], $user) === false && strpos($row['nopes'], $user) === false) {
                array_push($users, $row);
            }
        }
    }
    return $users;
}
?>