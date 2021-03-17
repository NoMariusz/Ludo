<?php
include("./includes.php");

function main(){
    $response = array('result' => true);

    if (!isset($_SESSION)){
        try {
        create_player();
        add_player_to_game();
        } catch (Exception $e) {
            $response['result'] = false;
        }
    }

    echo json_encode($response);
}

// player stuff

function create_player(){
    session_start();
    $nick = $_GET['nick'];
    $player_id = add_player_to_base($nick);
    $_SESSION['player_id'] = $player_id;
}

function add_player_to_base($nick){
    $mysqli = make_connection();
    $stmt = $mysqli-> prepare("INSERT INTO players(nick) VALUES(?);");
    $stmt -> bind_param("s", $nick);
    $stmt -> execute();
    
    /* get new player id */
    $result = mysqli_insert_id($mysqli);

    /* close statement */
    $stmt->close();

    /* close connection */
    $mysqli->close();

    return $result;
}

// adding player to game stuff

function add_player_to_game(){
    // get free game
    $free_game_id = get_free_game_id();
    // add player to that game
    $_SESSION['game_id'] = $free_game_id;
    $player_id = $_SESSION['player_id'];
    make_no_result_querry("UPDATE players SET game_id = $free_game_id WHERE id = $player_id");
    // get game free_spaces and decrement it free_spaces
    $game = make_querry("SELECT * FROM games WHERE id = $free_game_id");
    $free_spaces = $game[0]['free_spaces'];
    $free_spaces --;
    make_no_result_querry("UPDATE games SET free_spaces = $free_spaces WHERE id = $free_game_id");
    // if new player is last, start game
    if($free_spaces == 0){
        $game_id = $_SESSION['game_id'];
        prepare_players_for_main_game($game_id);
        start_game($game_id);
    }
}

function get_free_game_id(){
    $result = make_querry("SELECT * FROM games WHERE status = 0 AND free_spaces > 0;");
    if(isset($result[0]['id'])){
        return $result[0]['id'];
    }
    // making new game if not found old
    $new_game_id = make_new_game();
    return $new_game_id;
}

function make_new_game(){
    $mysqli = make_connection();
    $mysqli->query("INSERT INTO games() VALUES();");
    
    /* get new game id */
    $result = mysqli_insert_id($mysqli);

    /* close connection */
    $mysqli->close();

    return $result;
}

main();
?>