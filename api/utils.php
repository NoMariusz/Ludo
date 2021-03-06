<?php

function make_connection(){
    include('hidden.php');
    $mysqli = mysqli_connect($host, $user, $passwd, $db);
    return $mysqli;
}

function make_querry($querry){
    $mysqli =  make_connection();

    $res = $mysqli->query($querry);
    $arr = $res->fetch_all(MYSQLI_ASSOC);
    
    mysqli_close($mysqli);

    return($arr);
}

function make_no_result_querry($querry){
    $mysqli =  make_connection();

    $res = $mysqli->query($querry);
    
    mysqli_close($mysqli);

    return($res);
}
