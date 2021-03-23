<?php

function is_player_logged(){
    // notice to call only after session_start()
    if(! isset($_SESSION['player_id'])){
        http_response_code(400);
        return false;
    }
    // if player not exists then return error
    $player = get_player($_SESSION['player_id']);
    if ($player === false){
        http_response_code(400);
        return false;
    }
    return true;
}

function set_public_action_made(){
    $game_id = $_SESSION['game_id'];
    if (isset($game_id)){
        GameManager::mark_game_action($game_id);
    }
}

function get_game($id){
    $game = DbManager::make_querry("SELECT * FROM games WHERE id = $id;"
        )[0];
    return $game;
}

function get_player($id){
    $e = DbManager:: make_querry("SELECT * FROM players WHERE id = $id;");
    if(count($e) == 0){
        return false;
    }
    return $e[0];
}

function get_pawn($id){
    $e = DbManager::make_querry("SELECT * FROM pawns WHERE id = $id;")[0];
    return $e;
}

function get_game_players($game_id){
    return DbManager::make_querry(
        "SELECT * FROM players WHERE game_id = $game_id");
}

function get_game_active_player($game_id){
    return DbManager::make_querry(
        "SELECT * FROM players WHERE game_id = $game_id AND status = 4"
    )[0];
}

function get_game_playing_players($game_id){
    return DbManager::make_querry(
        "SELECT * FROM players WHERE game_id = $game_id AND status > 1 AND status != 5"
    );
}

function get_player_pawns($player_id){
    return DbManager::make_querry(
        "SELECT * FROM pawns WHERE player_id = $player_id");
}

?>