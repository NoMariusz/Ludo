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
    // set new status in base
    set_player_ready_status();
    // prepare
    prepare_player_for_main_game($player_id);
    // set game started if all players ready or is 4 players
    start_game_if_needed();
}

main();
?>
