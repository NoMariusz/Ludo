<pre>
<?php
include("../includes.php");

function main(){
    // set player to ready and make other ready stuff
    session_start();
    // check if is made session with player
    if(!is_player_logged()){
        http_response_code(403);
        return false;
    }
    // get data
    $player_id = $_SESSION['player_id'];
    $game_id = $_SESSION['game_id'];
    $game = get_game($game_id);
    $pawn_id = $_GET['pawn_id'];
    $pawn = BoardManager::safe_get_pawn($pawn_id);
    // check if pawn is set
    if (!isset($pawn) || !$pawn){
        http_response_code(404);
        return false;
    }
    // check if pawn belong to player
    if ($pawn['player_id'] != $player_id){
        http_response_code(403);
        return false;
    }
    $boardManager = new BoardManager($pawn);
    // check if pawn can be moved
    $points = $game['last_throw_points'];
    if (!$boardManager->check_pawn_can_be_moved($points)){
        http_response_code(403);
        return false;
    }

    // make move
    $boardManager->move_pawn($points);
    // when player move pawn change turn
    $turnManager = new TurnManager($game_id);
    $turnManager->change_turn();

    set_public_action_made();
    
    return true;
}

main();
?>
