<?php
include("../includes.php");

function main(){
    // set playe to ready and make other ready stuff
    session_start();
    // check if is made session with player
    if(!is_player_logged()){
        return false;
    }
    
    // get player and chcek if his code is 0
    $player_id = $_SESSION['player_id'];
    $player_data = make_querry("SELECT * FROM players WHERE id = $player_id");
    if($player_data[0]['status'] != 0){
        http_response_code(404);
        return false;
    }
    // set new status in base
    set_player_ready_status();
    // add him color
    add_color_to_player();
    // set game started if all players ready or is 4 players
    start_game_if_needed();
}

function set_player_ready_status(){
    $player_id = $_SESSION['player_id'];
    make_no_result_querry("UPDATE players SET status = 1 WHERE id = $player_id");
}

function add_color_to_player(){
    $game_id = $_SESSION['game_id'];
    $match_players = get_game_players($game_id);
    // get available colors
    global $colors;
    $available_colors = $colors;
    var_dump($available_colors);
    foreach($match_players as $player){
        if ($player['color'] != 'grey'){
            // get color index
            $idx = array_search($player['color'], $available_colors);
            // delete color from table
            unset($available_colors[$idx]);
        }
    }
    // set color to player
    $key = array_rand($available_colors);
    $color = $available_colors[$key];
    var_dump($key);
    var_dump($color);
    $player_id = $_SESSION['player_id'];
    make_no_result_querry("UPDATE players SET color = '$color' WHERE id = $player_id");
}

function start_game_if_needed(){
    $game_id = $_SESSION['game_id'];
    $match_players = get_game_players($game_id);
    // loop checking if can start
    $can_start = true;
    foreach($match_players as $player){
        // check if someone not started
        if ($player['status'] == 0 ){
            $can_start = false;
        }
    }
    if (count($match_players) > 1 and $can_start){
        start_game($game_id);
    }
}

main();
?>
