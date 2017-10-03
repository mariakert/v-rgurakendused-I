<?php
include("db.php");
session_start();

if (isset($_POST['lat']) && isset($_POST['lon'])) {
    $_SESSION['lat'] = $_POST['lat'];
    $_SESSION['lon'] = $_POST['lon'];
    $id = $_SESSION['id'];
    $lat = $_SESSION['lat'];
    $lon = $_SESSION['lon'];
    
    if ($query = mysqli_query($connection, 
                          "select * from location where id='$id'")) {
        $rowCount = mysqli_num_rows($query);
        
        $sql = "";
        if ($rowCount == 0) {
            $sql = "INSERT INTO location (id, lat, lon) VALUES ($id, $lat, $lon);";
        } else {
            $sql = "UPDATE location SET lat=$lat, lon=$lon WHERE id='$id'";
        }
        
        $setLocation = mysqli_query($connection, $sql);
    }
}

header("Location: index.php");
?>