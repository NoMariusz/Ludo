<?php
include("../includes.php");

function main(){
    $response = array('result' => true);

    if (!isset($_SESSION)){
        try {
            // made player
            $player_id = create_player($_GET['nick']);
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