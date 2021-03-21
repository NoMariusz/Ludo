<pre>
<?php
/* worker making jobs in interval that should be made as background */
include("./includes.php");

ini_set('max_execution_time', '3600'); 

function main(){
    while(true){
        update_turns();
        sleep(2);
    }
}

function update_turns(){
    // get running games
    $running_games = DbManager::make_querry(
        "SELECT * from games WHERE status = 1;");
    // made operation for each running game
    foreach($running_games as $game){
        update_turns_by_time($game);
        update_turns_by_player_moves($game);
    }
}

function update_turns_by_time($game){
    /* update turn if turn time end */
    global $TURN_TIME;
    $start_time = strtotime($game['turn_start_time']);
    $time_pass = time() - $start_time;
    if ($time_pass >= $TURN_TIME){
        $turnManager = new TurnManager($game['id']);
        $turnManager->change_turn();
    }
}

function update_turns_by_player_moves($game){
    /* update turn if player not have moves to made */
    // if in match not throwed cube then not change turns
    if($game['throwed_cube'] == 0){
        return false;
    }
    $points = $game['last_throw_points'];
    $game_id = $game['id'];
    $player = DbManager::make_querry(
        "SELECT * FROM players WHERE game_id = $game_id AND status = 4"
    )[0];
    // if none player in game has status 4, so active not throw cube pass
    // change turns
    if ($player == null){
        return false;
    }
    // check if player have move
    $player_manager = new PlayerManager($player['id']);
    if ($player_manager->check_if_player_have_moves($points)){
        echo "player have moves <br>";
    } else {
        // if player have not any move
        $turnManager = new TurnManager($game_id);
        $turnManager->change_turn();
    }
}

main();
?>