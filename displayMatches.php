<?php

function getChat($id, $gender, $user) {
    include("db.php");
    
    if (empty($_SESSION)) {
        session_start();
    }
    
    $sql = "select likes from users where id='$id'";
    $mine = mysqli_query($connection, $sql);
    $myLikes = mysqli_fetch_assoc($mine)['likes'];
    $buttons = "";

    if (strlen($myLikes) > 0) {
        $myArray = [];
        if (strpos($myLikes, ",") !== false) {
            $myArray = explode(",", $myLikes);
        } else {
            array_push($myArray, $myLikes);
        }
        
        for ($i = 0; $i < count($myArray); $i++) {
            $match = $myArray[$i];
            $sql = "select user, likes from users where user='$match'";
            $matches = mysqli_query($connection, $sql);
            $likes = mysqli_fetch_assoc($matches);
            $matchLikes = $likes['likes'];
            $matchName = $likes['user'];
            if (strpos($matchLikes, $user) !== false) {
                $button = "<input type='submit' name='match' value='$matchName' class='btn btn-primary uploadButton'><br>";
                $buttons = $buttons . $button;
            }
        }
        
        if ($buttons != "") {
            return $buttons;
        } else {
            return "No matches yet";
        }
    } else {
        return "No matches yet";
    }
}
?>