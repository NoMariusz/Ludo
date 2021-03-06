<?php
include("../includes.php");

function main(){
    // set playe to ready and make other ready stuff
    session_start();
    // check if is made session with player
    if(!is_player_logged()){
        http_response_code(403);
        return false;
    }
    // check if can throw cube
    if (!can_throw_cube()){
        http_response_code(403);
        return false;
    }

    $player_id = $_SESSION['player_id'];
    // random number
    $points = rand(1, 6);
    // set result to game
    $game_id = $_SESSION['game_id'];
    DbManager::make_no_result_querry(
        "UPDATE games SET last_throw_points = $points WHERE id = $game_id");
        DbManager::make_no_result_querry(
        "UPDATE games SET throwed_cube = 1 WHERE id = $game_id");
    // set to player status 4 so they can move pawns
    DbManager::make_no_result_querry(
        "UPDATE players SET status = 4 WHERE id = $player_id");
    // change if pawns can be moved in game
    BoardManager::load_move_status_for_pawns($game_id);

    set_public_action_made();

    echo json_encode(["points" => $points]);
    return true;
}

function can_throw_cube(){
    // get data
    $player_id = $_SESSION['player_id'];
    $player_data = DbManager::make_querry(
        "SELECT * FROM players WHERE id = $player_id")[0];
    // check if player can throw cube
    if($player_data['status'] != 3){
        return false;
    }
    return true;
}

main();
?>
