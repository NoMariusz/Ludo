<pre>
<?php
/* worker making jobs in interval that should be made as background */
include("./includes.php");

ini_set('max_execution_time', '3600'); 

function main(){
    while(true){
        clear_unactive_games();
        update_turns();
        sleep(2);
    }
}

// updating turns in matches

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
    $player = get_game_active_player($game_id);
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

// clearing unactive games

function clear_unactive_games(){
    global $CLEAR_UNACTIVE_GAME_TIME;
    // get max timestamp of games to clear
    $timetimestamp_to_clear = time() - $CLEAR_UNACTIVE_GAME_TIME;
    // add hour to time so work on GMP+1 time zone
    $timetimestamp_to_clear += 3600;
    // get date to delete games having last activity before that date
    $activity_time_to_clear = new DateTime('@' . $timetimestamp_to_clear);
    $activity_time_to_clear = $activity_time_to_clear->format("Y-m-d H:i:s");
    // get games to clear and clear them
    $games_to_clear = DbManager::make_querry(
        "SELECT * FROM games WHERE
        last_activity_time < '$activity_time_to_clear';"
    );
    foreach($games_to_clear as $game){
        GameManager::delete_game($game['id']);
    }
}

main();
?>