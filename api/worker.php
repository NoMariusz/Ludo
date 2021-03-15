<?php
/* worker making jobs in interval that should be made as background */
include("./includes.php");

ini_set('max_execution_time', '3600'); 

function main(){
    while(true){
        loop();
        sleep(5);
    }
}

function loop(){
    update_turns();
}

function update_turns(){
    global $TURN_TIME;
    $running_games = make_querry("SELECT * from games WHERE status = 1;");
    foreach($running_games as $game){
        $start_time = strtotime($game['turn_start_time']);
        $time_pass = time() - $start_time;
        if ($time_pass >= $TURN_TIME){
            change_turn($game['id']);
        } 
    }
}

main();
?>