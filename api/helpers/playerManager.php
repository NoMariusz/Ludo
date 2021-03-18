<?php

function create_player(){
    session_start();
    $nick = $_GET['nick'];
    $player_id = add_player_to_base($nick);
    $_SESSION['player_id'] = $player_id;
}

function add_player_to_base($nick){
    return make_safe_insert_id_querry("INSERT INTO players(nick) VALUES(?);", "s", [$nick]);
    // $mysqli = make_connection();
    // $stmt = $mysqli-> prepare("INSERT INTO players(nick) VALUES(?);");
    // $stmt -> bind_param("s", $nick);
    // $stmt -> execute();
    
    // /* get new player id */
    // $result = mysqli_insert_id($mysqli);

    // /* close statement */
    // $stmt->close();

    // /* close connection */
    // $mysqli->close();

    // return $result;
}

function add_player_to_game(){
    // get free game
    $free_game_id = get_free_game_id();
    // add player to that game
    $_SESSION['game_id'] = $free_game_id;
    $player_id = $_SESSION['player_id'];
    make_no_result_querry("UPDATE players SET game_id = $free_game_id WHERE id = $player_id");
    // get game free_spaces and decrement it free_spaces
    $game = make_querry("SELECT * FROM games WHERE id = $free_game_id");
    $free_spaces = $game[0]['free_spaces'];
    $free_spaces --;
    make_no_result_querry("UPDATE games SET free_spaces = $free_spaces WHERE id = $free_game_id");
    // if new player is last, start game
    if($free_spaces == 0){
        $game_id = $_SESSION['game_id'];
        prepare_players_for_main_game($game_id);
        start_game($game_id);
    }
}

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
    make_pawns_for_player($player_id);
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
    make_no_result_querry("UPDATE players SET color_index = '$color_key' WHERE id = $player_id");
}

