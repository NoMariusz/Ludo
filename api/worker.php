<pre>
<?php
/* worker making jobs in interval that should be made as background */
include("./includes.php");

ini_set('max_execution_time', '3600'); 

function main(){
    while(true){
        loop();
        sleep(2);
    }
}

function loop(){
    update_turns();
    update_turns_by_player_moves();
}

function update_turns(){
    /* update turn if turn time end */
    global $TURN_TIME;
    $running_games = DbManager::make_querry(
        "SELECT * from games WHERE status = 1;");
    foreach($running_games as $game){
        $start_time = strtotime($game['turn_start_time']);
        $time_pass = time() - $start_time;
        if ($time_pass >= $TURN_TIME){
            change_turn($game['id']);
        } 
    }
}

function update_turns_by_player_moves(){
    /* update turn if player not have moves to made */
    $running_games = DbManager::make_querry(
        "SELECT * from games WHERE status = 1;");
    foreach($running_games as $game){
        // if in match not throwed cube then pass
        if($game['throwed_cube'] == 0){
            continue;
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
        if (check_if_player_have_moves($player ,$points)){
            echo "player have moves <br>";
        } else {
            // if player have not any move
            change_turn($game_id);
        }
    }
}

main();
?>