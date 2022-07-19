<?php
    // $server = "localhost";
    // $username = "root";
    // $password = "";
    // $database = "house_items";
    $server = "remotemysql.com";
    $username = "x8771v6dXN";
    $password = "I7kMX8cZOo";
    $database = "x8771v6dXN";

    $connection = new mysqli($server, $username, $password, $database);
    if($connection->connect_error) {
        die("connection to this database failed dur to " . mysqli_connect_error());
    }
?>