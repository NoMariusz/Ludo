<?php

class BoardManager{
    public $pawn;

    function __construct($pawn){
        $this->pawn = $pawn;
    }

    // pawns stuff

    static function make_pawns_for_player($player_id){
        global $PAWNS_FOR_PLAYER;
        $player = DbManager::make_querry(
            "SELECT * FROM players WHERE id = $player_id")[0];
        $game_id = $player['game_id'];
        $color_idx = $player['color_index'];
        for ($i=0;$i<$PAWNS_FOR_PLAYER;$i++){
            DbManager::make_no_result_querry(
                "INSERT INTO pawns(
                    position, color_index, game_id, player_id,
                    position_out_board)
                VALUES ($i, $color_idx, $game_id, $player_id, $i);"
            );
        };
    }

    static function safe_get_pawn($id){
        if(!$id){
            return false;
        }
        $data = DbManager::make_safe_querry(
            "SELECT * FROM pawns WHERE id  = ?", "d", [$id]);
        if(count($data) == 0){
            return false;
        }
        return $data[0];
    }

    // moves

    function check_pawn_can_be_moved($points){
        $player_id = $this->pawn['player_id'];
        $player = get_player($player_id);
        // player must have status 4 to move pawns
        if($player['status'] != 4){
            // echo "Pawn can not be moved: player not have turn<br>";
            return false;
        }
        // if pawn is in home then can not move
        if ($this->pawn['in_home'] == 1){
            // echo "Pawn can not be moved: pawn in home<br>";
            return false;
        }
        // check if can go on board
        if ($this->pawn['out_of_board'] == 1){
            if ($points == 6 || $points == 1){
                return true;
            }
            // echo "Pawn can not be moved: pawn out of board and points not 6 or 1<br>";
            // if out of board only 1 or 6 can move pawn
            return false;
        }
        // at normal move
        if ($this->try_move_pawn_obj($points) != false){
            return true;
        }
        return false;
    }

    function move_pawn($points){
        $this->move_pawn_obj($points);
        $this->save_pawn_changes();
        $this->make_beating();
    }

    function move_pawn_obj($points){
        if($this->pawn['out_of_board'] == 1){
            $this->move_pawn_to_board();
        } else {
            $this->normal_move_pawn_obj($points);
        }
    }

    private function normal_move_pawn_obj($points){
        global $FIELDS_COUNT;
        $game_id = $this->pawn['game_id'];
        $player_id = $this->pawn['player_id'];
        $new_position = $this->pawn['position'] + $points;
        $new_position = $new_position % $FIELDS_COUNT;

        $pawn_home_pos = $this->get_pawn_home_pos();
        // if new position is after home, then player is reaching home, so
        // should check if he have space in home
        if ($this->is_moved_to_home($new_position)){
            // get position at home after move
            $place_in_home = $new_position - $pawn_home_pos;
            // check if position in house is proper
            if ($place_in_home >= 4){
                // echo "Pawn can not be moved: not find field at home<br>";
                return false;
            }
            // get if is pawn at that position
            $res = DbManager::make_querry(
                "SELECT * from pawns where game_id = $game_id
                AND player_id = $player_id AND in_home = 1
                AND position = $place_in_home"
            );
            // check if place is free
            if (count($res) != 0){
                // echo "Pawn can not be moved: not enough space in home<br>";
                return false;
            }
            // if that place in home free
            $this->pawn['in_home'] = 1;
            $this->pawn['position'] = $place_in_home;
            return $this->pawn;
        }
        // if normal move
        $this->pawn['position'] = $new_position;
        return $this->pawn;
    }

    private function try_move_pawn_obj($points){
        /* normal_move_pawn_obj without affection in $this->pawn */
        // get start value of pawn
        $pawn_copy = $this->pawn;
        // make move
        $result = $this->normal_move_pawn_obj($points);
        // set $this->pawn start value
        $this->pawn = $pawn_copy;
        return $result;
    }

    private function move_pawn_to_board(){
        $pawn_home_pos = $this->get_pawn_home_pos();
        // move pawn to board
        $this->pawn['out_of_board'] = 0;
        $this->pawn['position'] = $pawn_home_pos;
    }

    private function save_pawn_changes(){
        $pos = $this->pawn['position'];
        $id = $this->pawn['id'];
        $out_of_board = $this->pawn['out_of_board'];
        $in_home = $this->pawn['in_home'];
        DbManager::make_no_result_querry(
            "UPDATE pawns SET position = $pos, out_of_board = $out_of_board,
            in_home = $in_home WHERE id = $id"
        );
    }

    private function is_moved_to_home($new_position){
        // get data
        $pos = $this->pawn['position'];
        $pawn_home_pos = $this->get_pawn_home_pos($this->pawn);
        // for yellow becouse on his base is change of position numering
        if ($this->pawn['color_index'] == 0){
            return $pos > $new_position;
        }
        // for normal color
        return $new_position >= $pawn_home_pos && $pos < $pawn_home_pos;
    }

    // pawn beating

    private function make_beating(){
        $game_id = $this->pawn['game_id'];
        $player_id = $this->pawn['player_id'];
        $pos = $this->pawn['position'];

        $res = DbManager::make_querry(
            "SELECT * from pawns where game_id = $game_id AND
            player_id != $player_id AND in_home = 0 AND out_of_board = 0 AND
            position = $pos"
        );
        // if not found other pawns at field
        if (!$res || count($res) == 0){
            return false;
        }
        // beat fields
        foreach($res as $pawn_to_beat){
            self::beat_pawn($pawn_to_beat);
        }
    }

    private static function beat_pawn($pawn){
        // move pawn out of board
        $pawn_id = $pawn['id'];
        $out_pos = $pawn['position_out_board'];
        DbManager::make_no_result_querry(
            "UPDATE pawns SET out_of_board = 1, position = $out_pos
            WHERE id = $pawn_id;");
    }

    // load can be moved pawn status

    static function load_move_status_for_pawns($game_id){
        // get active player pawns
        $points = get_game($game_id)['last_throw_points'];
        $player = get_game_active_player($game_id);
        $pawns = get_player_pawns($player['id']);
        // foreach pawn set move status
        $board_manager = new self(null);
        foreach($pawns as $pawn){
            // check if can be moved
            $board_manager->pawn = $pawn;
            $can_be_moved = $board_manager->check_pawn_can_be_moved($points);
            // set result to base
            $pawn_id = $pawn['id'];
            DbManager::make_no_result_querry(
                "UPDATE pawns SET can_be_moved = $can_be_moved WHERE id = $pawn_id"
            );
        }
    }

    // utils

    private function get_pawn_home_pos(){
        global $PAWNS_HOMES_POS;
        $pawn_color_idx = $this->pawn['color_index'];
        return $PAWNS_HOMES_POS[$pawn_color_idx];
    }

}
?>