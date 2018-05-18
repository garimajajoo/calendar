<?php
// Content of database.php

// You know what this is! Allows us to access our databse!
$mysqli = new mysqli('localhost', 'phpuser', 'password', 'calendar');

if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
}
?>