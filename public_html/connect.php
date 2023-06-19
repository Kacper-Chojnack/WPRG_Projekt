<?php

	$host = "localhost";
	$db_user = "leisuret_users";
	$db_password = "admin";
	$db_name = "leisuret_users";
    $conn = mysqli_connect($host, $db_user, $db_password, $db_name);
    if (!$conn) {
        die("Something went wrong;");
    }
global $host, $db_user, $db_password, $db_name, $conn;
?>