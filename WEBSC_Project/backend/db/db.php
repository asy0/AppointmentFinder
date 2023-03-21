<?php
$host = "localhost";
$username = "bif2webscriptinguser";
$password = "bif2021";
$database = "AppointmentFinder";


$conn = mysqli_connect($host, $username, $password, $database);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


