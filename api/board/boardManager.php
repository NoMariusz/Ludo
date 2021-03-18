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
            "INSERT INTO pawns(
                position, color_index, game_id, player_id, position_out_board)
            VALUES ($i, $color_idx, $game_id, $player_id, $i);"
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
    $player_id = $pawn['player_id'];
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
    echo "start moving pawn<br>";
    var_dump($pawn);
    // $start_pos = $pawn['position'];
    if($pawn['out_of_board'] == 1){
        $pawn = move_pawn_to_board($pawn);
    } else {
        $pawn = normal_move_pawn_obj($pawn, $points);
    }
    save_pawn_changes($pawn);
    make_beating($pawn);
    var_dump($pawn);
    echo "end moving pawn<br>";
}

function normal_move_pawn_obj($pawn, $points){
    global $FIELDS_COUNT;
    $game_id = $pawn['game_id'];
    $player_id = $pawn['player_id'];
    $new_position = $pawn['position'] + $points;
    $new_position = $new_position % $FIELDS_COUNT;

    $pawn_home_pos = get_pawn_home_pos($pawn);
    // if new position is after home, then player is reaching home, so should
    // check if he have space in home
    if (is_moved_to_home($pawn, $new_position)){
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
    $pawn_home_pos = get_pawn_home_pos($pawn);
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

function get_pawn_home_pos($pawn){
    global $PAWNS_HOMES_POS;
    $pawn_color_idx = $pawn['color_index'];
    return $PAWNS_HOMES_POS[$pawn_color_idx];
}

function is_moved_to_home($pawn, $new_position){
    // get data
    $pos = $pawn['position'];
    $pawn_home_pos = get_pawn_home_pos($pawn);
    // for yellow becouse on his base is change of position numering
    if ($pawn['color_index'] == 0){
        return $pos > $new_position;
    }
    // for normal color
    return $new_position >= $pawn_home_pos && $pos < $pawn_home_pos;
}

// pawn beating

function make_beating($pawn){
    $game_id = $pawn['game_id'];
    $player_id = $pawn['player_id'];
    $pos = $pawn['position'];

    $res = make_querry(
        "SELECT * from pawns where game_id = $game_id AND
        player_id != $player_id AND in_home = 0 AND out_of_board = 0 AND
        position = $pos"
    );
    echo "beating check<br>";
    var_dump($res);
    // if not found other pawns at field
    if (!$res || count($res) == 0){
        return false;
    }
    // beat fields
    foreach($res as $pawn_to_beat){
        beat_pawn($pawn_to_beat);
    }
}

function beat_pawn($pawn){
    echo "Beating pawn $pawn<br>";
    // move pawn out of board
    $pawn_id = $pawn['id'];
    $out_pos = $pawn['position_out_board'];
    make_no_result_querry(
        "UPDATE pawns SET out_of_board = 1, position = $out_pos
        WHERE id = $pawn_id;");
}
?>