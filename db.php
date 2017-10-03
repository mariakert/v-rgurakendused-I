<?php
$connection = mysqli_connect('localhost', 'root', '');
$db = mysqli_select_db($connection, 'kinder');
mysql_query("set names 'utf8'");
?>