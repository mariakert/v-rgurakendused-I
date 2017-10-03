<?php
session_start();

if (isset($_SESSION['radius'])) {
    unset($_SESSION['radius']);
}

header("Location: index.php");
?>