<?php

$servername = "localhost";
$username   = "susom";
$password   = "susom@2005";
$database   = "access";

$conn = new mysqli(
    $servername,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

?>