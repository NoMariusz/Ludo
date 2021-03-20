<?php

function change_turn($game_id){
    // change active player
    change_active_player($game_id);
    // change values at turn start
    DbManager::make_no_result_querry(
        "UPDATE games SET turn_start_time = CURRENT_TIMESTAMP(),
        throwed_cube =  0 WHERE id = $game_id"
    );
}

function change_active_player($game_id){
    /* function changing player having turn in base */
    $players = get_game_players($game_id);
    // find active player
    $active_player = DbManager::make_querry(
        "SELECT * from players WHERE game_id = $game_id AND status > 2;");
    // find what index have active player
    $active_idx = array_search($active_player[0], $players);
    // increment active player index and set next player active
    $active_idx ++;
    if ($active_idx >= count($players)){
        $active_idx = 0;
    }
    $active_id = $players[$active_idx]['id'];
    GameManager::set_player_active($active_id, $game_id);
};

?>