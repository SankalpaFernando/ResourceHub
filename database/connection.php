<?php

$host = "switchback.proxy.rlwy.net";
$port = 13673;
$user = "root";
$password = "HANUdqwTVVxIzZHfqTXREScdXRDciriF";
$database = "railway";

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

?>