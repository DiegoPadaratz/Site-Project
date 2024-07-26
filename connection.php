<?php 

    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $bd = "sistema";

    $mysqli = new mysqli($host, $user, $pass, $bd);

    if($mysqli->connect_error){
        die($mysqli->error);
    }