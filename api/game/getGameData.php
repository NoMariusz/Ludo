<?php
include("../utils.php");

function main(){
    // return game data to player
    session_start();
    // check if is made session with player
    if(! isset($_SESSION['player_id'])){
        http_response_code(404);
        return false;
    }
    // get game informations and players
    $game_id = $_SESSION['game_id'];
    $game_data = make_querry("SELECT * FROM games WHERE id = $game_id");
    $players_data = get_game_players($game_id);
    // return informations
    echo json_encode(array('game' => $game_data, 'players' => $players_data, 'player_id' => $_SESSION['player_id']));
}

main();

?>