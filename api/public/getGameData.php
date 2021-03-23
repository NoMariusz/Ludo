<?php
include("../includes.php");

function main(){
    // return game data to player
    session_start();
    // check if is made session with player
    if(!is_player_logged()){
        return false;
    }
    // get game informations and players
    $game_id = $_SESSION['game_id'];
    $game_data = DbManager::make_querry("SELECT * FROM games WHERE id = $game_id");
    $players_data = get_game_players($game_id);
    $pawns = DbManager::make_querry("SELECT * FROM pawns WHERE game_id = $game_id");
    // return informations
    echo json_encode(array(
        'game' => count($game_data) == 0 ? array() : $game_data[0],
        'players' => $players_data,
        'player_id' => $_SESSION['player_id'],
        'pawns' => $pawns
    ));

    set_public_action_made();
    
}

main();

?>