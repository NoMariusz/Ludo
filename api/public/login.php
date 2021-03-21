<?php
include("../includes.php");

function main(){
    $response = array('result' => true);
    $nick = $_GET['nick'];
    if (!isset($_SESSION) && $nick != null){
        try {
            // made player
            $player_id = PlayerManager::create_player($nick);
            $_SESSION['player_id'] = $player_id;
            // add player to game
            $game_id = GameManager::add_player_to_game($player_id);
            $_SESSION['game_id'] = $game_id;
            // start game if can, cbecouse after player join game can be full
            $gameManager = new GameManager($game_id);
            $gameManager -> start_game_if_can();
        } catch (Exception $e) {
            $response['result'] = false;
        }
    }

    echo json_encode($response);
}

main();
?>