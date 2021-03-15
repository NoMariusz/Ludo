<?php
$PAWNS_FOR_PLAYER = 4;

function make_pawns_for_player($player_id){
    global $PAWNS_FOR_PLAYER;
    $player = make_querry("SELECT * FROM players WHERE id = $player_id")[0];
    $game_id = $player['game_id'];
    $color_idx = $player['color_index'];
    for ($i=0;$i<$PAWNS_FOR_PLAYER;$i++){
        make_no_result_querry(
            "INSERT INTO pawns(position, color_index, game_id, player_id) VALUES ($i, $color_idx, $game_id, $player_id);"
        );
    };
}
?>