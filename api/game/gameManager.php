<?php

function get_game_players($game_id){
    return make_querry("SELECT * FROM players WHERE game_id = $game_id");
}

function set_match_started($game_id){
    // set game status started
    make_no_result_querry("UPDATE games SET status = 1 WHERE id = $game_id");
}

?>