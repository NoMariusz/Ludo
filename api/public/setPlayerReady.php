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
    $player_data = DbManager::make_querry("SELECT * FROM players WHERE id = $player_id");
    if($player_data[0]['status'] != 0){
        http_response_code(404);
        return false;
    }
    $player_manager = new PlayerManager($player_id);
    // set new status in base
    $player_manager->set_player_ready_status();
    // prepare
    $player_manager->prepare_player_for_main_game();
    // set game started if all players ready or is 4 players
    $game_id = $_SESSION['game_id'];
    $gameManager = new GameManager($game_id);
    $gameManager->start_game_if_can();

    set_public_action_made();
}

main();
?>
