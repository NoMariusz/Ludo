<?php
/* Is a util class so not include anything but is used at includes.php and has
included everything */

function get_game_players($game_id){
    return make_querry("SELECT * FROM players WHERE game_id = $game_id");
}

function start_game($game_id){
    // set game status started
    make_no_result_querry("UPDATE games SET status = 1 WHERE id = $game_id");
    // set active player
    $players = get_game_players($game_id);
    $idx = random_int(0, count($players)-1);
    $active_id = $players[$idx]["id"];
    set_player_active($active_id, $game_id);
}

function set_player_active($player_id, $game_id){
    // set status to not active players
    make_no_result_querry("UPDATE players SET status = 2 WHERE game_id = $game_id");
    // set active player status
    make_no_result_querry("UPDATE players SET status = 3 WHERE id = $player_id");
    // update start turn time
    make_no_result_querry("UPDATE games SET turn_start_time = CURRENT_TIMESTAMP() WHERE id = $game_id");
}

function change_active_player($game_id){
    /* function changing player having turn in base */
    $players = get_game_players($game_id);
    $active_player = make_querry("SELECT * from players WHERE game_id = $game_id AND status > 2;");
    $active_idx = array_search($active_player[0], $players);
    echo "Active player idx: $active_idx";
    $active_idx ++;
    if ($active_idx >= count($players)){
        $active_idx = 0;
    }
    $active_id = $players[$active_idx]['id'];
    set_player_active($active_id, $game_id);
};

function change_turn($game_id){
    // change active player
    change_active_player($game_id);
    // change turn start time
    make_no_result_querry("UPDATE games SET turn_start_time = CURRENT_TIMESTAMP() WHERE id = $game_id");
}


?>