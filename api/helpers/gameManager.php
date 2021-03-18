<?php

function make_new_game(){
    return make_insert_id_querry("INSERT INTO games() VALUES();");;
}

function get_free_game_id(){
    $result = make_querry(
        "SELECT * FROM games WHERE status = 0 AND free_spaces > 0;");
    if(isset($result[0]['id'])){
        return $result[0]['id'];
    }
    // making new game if not found old
    $new_game_id = make_new_game();
    return $new_game_id;
}

// starting game

function start_game($game_id){
    // set game status started
    make_no_result_querry("UPDATE games SET status = 1 WHERE id = $game_id");
    // set active player
    $players = get_game_players($game_id);
    $idx = random_int(0, count($players)-1);
    $active_id = $players[$idx]["id"];
    set_player_active($active_id, $game_id);
}

function get_game_players($game_id){
    return make_querry("SELECT * FROM players WHERE game_id = $game_id");
}

function set_player_active($player_id, $game_id){
    // set status to not active players
    make_no_result_querry(
        "UPDATE players SET status = 2 WHERE game_id = $game_id");
    // set active player status
    make_no_result_querry(
        "UPDATE players SET status = 3 WHERE id = $player_id");
    // update start turn time
    make_no_result_querry(
        "UPDATE games SET turn_start_time = CURRENT_TIMESTAMP()
        WHERE id = $game_id"
    );
}

function start_game_if_needed(){
    /* start game by players ready */
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
    // to start must be at least 2 players
    if (count($match_players) > 1 and $can_start){
        start_game($game_id);
    }
}

// changing turns

function change_turn($game_id){
    // change active player
    change_active_player($game_id);
    // change values at turn start
    make_no_result_querry(
        "UPDATE games SET turn_start_time = CURRENT_TIMESTAMP(),
        throwed_cube =  0 WHERE id = $game_id"
    );
}

function change_active_player($game_id){
    /* function changing player having turn in base */
    $players = get_game_players($game_id);
    // find active player
    $active_player = make_querry(
        "SELECT * from players WHERE game_id = $game_id AND status > 2;");
    // find what index have active player
    $active_idx = array_search($active_player[0], $players);
    var_dump($players);
    var_dump($active_player);
    echo "Active player idx: $active_idx<br>";
    // increment active player index and set next player active
    $active_idx ++;
    if ($active_idx >= count($players)){
        $active_idx = 0;
    }
    $active_id = $players[$active_idx]['id'];
    set_player_active($active_id, $game_id);
};
