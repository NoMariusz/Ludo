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

function get_game_players($game_id){
    return make_querry("SELECT * FROM players WHERE game_id = $game_id");
}

function set_match_started($game_id){
    make_no_result_querry("UPDATE games SET status = 1 WHERE id = $game_id");
}

function is_player_logged(){
    session_start();
    if(! isset($_SESSION['player_id'])){
        http_response_code(404);
        return false;
    }
    return true;
}

?>