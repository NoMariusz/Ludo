<?php

function create_player($nick){
    /* create player by given nick */
    session_start();
    // add data to db
    $player_id = DbManager::make_safe_insert_id_querry(
        "INSERT INTO players(nick) VALUES(?);", "s", [$nick]);
    // start session
    return $player_id;
}

// preparing him for game

function prepare_players_for_main_game($game_id){
    // prepare all unprepared players from game to game
    $match_players = get_game_players($game_id);
    foreach($match_players as $player){
        if ($player['status'] == 0){
            prepare_player_for_main_game($player['id']);
        }
    }
}

function prepare_player_for_main_game($player_id){
    // add him color
    add_color_to_player($player_id);
    // make pawns when have color
    BoardManager::make_pawns_for_player($player_id);
}

function add_color_to_player($player_id){
    // get data
    $player = get_player($player_id);
    $game_id = $player['game_id'];
    $match_players = get_game_players($game_id);
    // get available colors
    global $colors;
    $available_colors = $colors;
    // set color to player
    foreach($match_players as $player){
        if ($player['color_index'] != -1){
            $color_index = $player['color_index'];
            // delete color from available
            unset($available_colors[$color_index]);
        }
    }
    // set color to player
    $color_key = array_rand($available_colors);
    DbManager::make_no_result_querry(
        "UPDATE players SET color_index = '$color_key' WHERE id = $player_id"
    );
}

// set player ready

function set_player_ready_status(){
    $player_id = $_SESSION['player_id'];
    DbManager::make_no_result_querry(
        "UPDATE players SET status = 1 WHERE id = $player_id");
}

// player operations

function check_if_player_have_moves($player, $points){
    $player_id = $player['id'];
    $pawns =DbManager::make_querry(
        "SELECT * FROM pawns WHERE player_id = $player_id");
    foreach($pawns as $pawn){
        $boardManager = new BoardManager($pawn);
        if ($boardManager->check_pawn_can_be_moved($points)){
            return true;
        }
    }
    return false;
}

