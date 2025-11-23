<?php

$servername ="localhost";
$username ="root";
$password ="";
$dbname ="bat_cafe";
$port =3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("connection failed:". $conn->connect_error);
    }

$conn->set_charset("utf8");

?>