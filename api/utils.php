<?php

function make_connection(){
    global $host, $user, $passwd, $db;
    $mysqli = mysqli_connect($host, $user, $passwd, $db);
    return $mysqli;
}

function make_querry($querry){
    $mysqli =  make_connection();

    $res = $mysqli->query($querry);
    if(!$res) return false;
    $arr = $res->fetch_all(MYSQLI_ASSOC);
    
    mysqli_close($mysqli);

    return($arr);
}

function make_safe_querry($sql, $types = null, $params = null) {
    $mysqli = make_connection();

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if(!$stmt->execute()) return false;
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function make_no_result_querry($querry){
    $mysqli =  make_connection();

    $res = $mysqli->query($querry);
    
    mysqli_close($mysqli);

    return($res);
}

function make_insert_id_querry($querry){
    $mysqli = make_connection();
    $mysqli->query($querry);
    
    /* get insert id */
    $result = mysqli_insert_id($mysqli);

    /* close connection */
    $mysqli->close();

    return $result;
}

function make_safe_insert_id_querry($sql, $types = null, $params = null){
    $mysqli = make_connection();

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);

    // if execute not succeed
    if(!$stmt->execute()) return false;

    // get insert id
    $result =  mysqli_insert_id($mysqli);

    // close statement
    $stmt->close();

    // close connection
    $mysqli->close();

    return $result;
}

function is_player_logged(){
    // notice to call only after session_start()
    if(! isset($_SESSION['player_id'])){
        http_response_code(404);
        return false;
    }
    return true;
}

function get_game($id){
    $game = make_querry("SELECT * FROM games WHERE id = $id;")[0];
    return $game;
}

function get_player($id){
    $e = make_querry("SELECT * FROM players WHERE id = $id;")[0];
    return $e;
}

function get_pawn($id){
    $e = make_querry("SELECT * FROM pawns WHERE id = $id;")[0];
    return $e;
}

?>