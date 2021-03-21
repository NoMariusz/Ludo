<?php
include("../includes.php");

function main(){
    // set player to ready and make other ready stuff
    session_start();
    // check if is made session with player
    if(!is_player_logged()){
        http_response_code(404);
        return false;
    }
    // get data
    $game_id = $_SESSION['game_id'];
    $game = get_game($game_id);
    $pawn_id = $_GET['pawn_id'];
    $pawn = BoardManager::safe_get_pawn($pawn_id);
    // check if pawn is set
    if (!isset($pawn) || !$pawn){
        http_response_code(400);
        return false;
    }
    $boardManager = new BoardManager($pawn);
    // check if pawn can be moved
    $points = $game['last_throw_points'];
    if (!$boardManager->check_pawn_can_be_moved($points)){
        http_response_code(400);
        return false;
    }

    // make move at object without affection db
    $boardManager->move_pawn_obj($points);
    $pawn_after_move = $boardManager->pawn;
    echo json_encode($pawn_after_move);
    return true;
}

main();
?>
