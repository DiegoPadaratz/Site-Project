<?php 

    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $bd = "sistema";

    $mysqli = new mysqli($host, $user, $pass, $bd);

    if($mysqli->connect_error){
        die($mysqli->error);
    }

    //Function Format Name
    function formatName($string){
        return preg_replace('/[\s\d,!?]/', '', $string);
    }

    function currentDate(){
        date_default_timezone_set('America/Sao_Paulo');
        return date("Y-m-d H:i:s", time());
    }

    function formatDate($date){
        return date("d/m/Y", strtotime($date)) . " às " . date("H:i", strtotime($date));
    }

    function formatBirth($date){
        return date("d/m/Y", strtotime($date));
    }