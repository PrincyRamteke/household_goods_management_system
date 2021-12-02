<?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "house_items";

    $connection = new mysqli($server, $username, $password, $database);
    if($connection->connect_error) {
        die("connection to this database failed dur to " . mysqli_connect_error());
    }
?>