<?php
    // $server = "localhost";
    // $username = "root";
    // $password = "";
    // $database = "house_items";
    $server = "remotemysql.com";
    $username = "1BGoX1DaCt";
    $password = "qAh0Jw70AD";
    $database = "1BGoX1DaCt";

    $connection = new mysqli($server, $username, $password, $database);
    if($connection->connect_error) {
        die("connection to this database failed dur to " . mysqli_connect_error());
    }
?>