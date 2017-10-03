<?php
function getUsersInBoundingBox($users) {
    include("db.php");
    if (empty($_SESSION)) {
        session_start();
    }
    $resultArray= [];
    $lat1 = $_SESSION['lat'];
    $long1 = $_SESSION['lon'];
    $radius = 0;
    
    if (empty($_SESSION['radius'])) {
        return $users;
    } else {
        $radius = $_SESSION['radius'];
        
        for ($i = 0; $i < count($users); $i++) {
            $row = $users[$i];
            $matchID = $row['id'];
            $sql = "select * from location where id='$matchID'";
            $query = mysqli_query($connection, $sql);

            $rowCount = mysqli_num_rows($query);

            $sql = "";
            if ($rowCount == 0) {
                continue;
            } else {
                $coords = mysqli_fetch_assoc($query);
                $lat2 = $coords['lat'];
                $long2 = $coords['lon'];

                $earth_radius = 6371;  

                $dLat = deg2rad($lat2 - $lat1);  
                $dLon = deg2rad($long2 - $long1);  

                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);  
                $c = 2 * asin(sqrt($a));  
                $distance = $earth_radius * $c;

                if ($distance < $radius) {
                    array_push($resultArray, $row);
                }
            }
        }
        return $resultArray; 
    } 
}

?>