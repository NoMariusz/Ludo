<?php
$PAWNS_FOR_PLAYER = 4;

// pawns stuff

function make_pawns_for_player($player_id){
    global $PAWNS_FOR_PLAYER;
    $player = make_querry("SELECT * FROM players WHERE id = $player_id")[0];
    $game_id = $player['game_id'];
    $color_idx = $player['color_index'];
    for ($i=0;$i<$PAWNS_FOR_PLAYER;$i++){
        make_no_result_querry(
            "INSERT INTO pawns(position, color_index, game_id, player_id) VALUES ($i, $color_idx, $game_id, $player_id);"
        );
    };
}

function safe_get_pawn($id){
    if(!$id){
        return false;
    }
    $data = make_safe_querry("SELECT * FROM pawns WHERE id  = ?", "d", [$id]);
    if(count($data) == 0){
        return false;
    }
    return $data[0];
}

// checking moves

function check_if_player_have_moves($player, $points){
    $player_id = $player['id'];
    $pawns = make_querry("SELECT * FROM pawns WHERE player_id = $player_id");
    foreach($pawns as $pawn){
        if (check_pawn_can_be_moved($pawn, $points)){
            return true;
        }
    }
    return false;
}

function check_pawn_can_be_moved($pawn, $points){
    $game_id = $pawn['game_id'];
    $player_id = $pawn['player_id'];
    $game = get_game($game_id);
    $player = get_player($player_id);
    // player must have status 4 to move pawns
    if($player['status'] != 4){
        return false;
    }
    // if pawn is in home then can not move
    if ($pawn['in_home'] == 1){
        return false;
    }
    // check if can go on board
    if ($pawn['out_of_board'] == 1){
        if ($points == 6 || $points == 1){
            return true;
        }
        // if out of board only 1 or 6 can move pawn
        return false;
    }
    // at normal move
    if (normal_move_pawn_obj($pawn, $points) != false){
        return true;
    }
    return false;
}

// moves

function move_pawn($pawn, $points){
    if($pawn['out_of_board'] == 1){
        $pawn = move_pawn_to_board($pawn);
    } else {
        $pawn = normal_move_pawn_obj($pawn, $points);
    }
    save_pawn_changes($pawn);
}

function normal_move_pawn_obj($pawn, $points){
    global $PAWNS_HOMES_POS, $FIELDS_COUNT, $PAWNS_HOMES_PREPOS;
    $game_id = $pawn['game_id'];
    $player_id = $pawn['player_id'];
    $new_position = $pawn['position'] + $points;
    $new_position = $new_position % $FIELDS_COUNT;
    $pawn_color_idx = $pawn['color_index'];

    $pawn_home_pos = $PAWNS_HOMES_POS[$pawn_color_idx];
    $pawn_home_prepos = $PAWNS_HOMES_PREPOS[$pawn_color_idx];
    // if new position is after home, then player is reaching home, so should
    // check if he have space in home
    if ($new_position >= $pawn_home_pos && $pawn['position'] <= $pawn_home_prepos){
        $place_in_home = $new_position - $pawn_home_pos;
        // check if is pawn at that position
        $res = make_querry(
            "SELECT * from pawns where game_id = $game_id AND player_id = $player_id AND in_home = 1 AND position = $place_in_home"
        );
        // if that place in home free
        if (count($res) == 0){
            $pawn['in_home'] = 1;
            $pawn['position'] = $place_in_home;
            return $pawn;
        } else {
            return false;
        }
    }
    // if normal move
    $pawn['position'] = $new_position;
    return $pawn;
}

function move_pawn_to_board($pawn){
    global $PAWNS_HOMES_POS;
    // get data
    $pawn_color_idx = $pawn['color_index'];
    $pawn_home_pos = $PAWNS_HOMES_POS[$pawn_color_idx];
    // move pawn to board
    $pawn['out_of_board'] = 0;
    $pawn['position'] = $pawn_home_pos;
    return $pawn;
}

function save_pawn_changes($pawn){
    $pos = $pawn['position'];
    $id = $pawn['id'];
    $out_of_board = $pawn['out_of_board'];
    $in_home = $pawn['in_home'];
    make_no_result_querry(
        "UPDATE pawns SET position = $pos, out_of_board = $out_of_board,
        in_home = $in_home WHERE id = $id"
    );
}
?>