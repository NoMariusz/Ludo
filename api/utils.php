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

function is_player_logged(){
    // notice to call only after session_start()
    if(! isset($_SESSION['player_id'])){
        http_response_code(404);
        return false;
    }
    return true;
}

function get_game($id){
    $game = make_querry("SELECT * FROM games WHERE id = $id;")[0];
    return $game;
}

function get_player($id){
    $e = make_querry("SELECT * FROM players WHERE id = $id;")[0];
    return $e;
}

function get_pawn($id){
    $e = make_querry("SELECT * FROM pawns WHERE id = $id;")[0];
    return $e;
}

?>