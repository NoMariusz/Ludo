<?php

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

