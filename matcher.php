<?php
function getMatch($array) {
    $arrayLength = count($array);
    $random = rand(0, $arrayLength - 1);
    return $array[$random];
}
?>