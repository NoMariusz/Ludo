<?php

class GameManager{
    public $game_id;

    function __construct($game_id){
        $this->game_id = $game_id;
    }

    private static function make_new_game(){
        return DbManager::make_insert_id_querry("INSERT INTO games() VALUES();");
    }

    private static function get_free_game_id(){
        $result = DbManager::make_querry(
            "SELECT * FROM games WHERE status = 0 AND free_spaces > 0;");
        if(isset($result[0]['id'])){
            return $result[0]['id'];
        }
        // making new game if not found old
        $new_game_id = self::make_new_game();
        return $new_game_id;
    }

    static function set_player_active($player_id, $game_id){
        // set waiting status to not active players
        DbManager::make_no_result_querry(
            "UPDATE players SET status = 2 WHERE game_id = $game_id
            AND status != 5"
        );
        // set active player status
        DbManager::make_no_result_querry(
            "UPDATE players SET status = 3 WHERE id = $player_id");
        // update start turn time
        DbManager::make_no_result_querry(
            "UPDATE games SET turn_start_time = CURRENT_TIMESTAMP()
            WHERE id = $game_id"
        );
    }
    
    static function add_player_to_game($player_id){
        // get free game
        $free_game_id = self::get_free_game_id();
        // add player to that game
        DbManager::make_no_result_querry(
            "UPDATE players SET game_id = $free_game_id WHERE id = $player_id");
        // get game free_spaces and decrement it free_spaces
        $game = DbManager::make_querry(
            "SELECT * FROM games WHERE id = $free_game_id");
        $free_spaces = $game[0]['free_spaces'];
        $free_spaces --;
        DbManager::make_no_result_querry(
            "UPDATE games SET free_spaces = $free_spaces WHERE id = $free_game_id"
        );
        return $free_game_id;
    }

    static function delete_game($game_id){
        // delete pawns
        DbManager::make_no_result_querry(
            "DELETE FROM pawns WHERE game_id = $game_id");
        // delete players
        DbManager::make_no_result_querry(
            "DELETE FROM players WHERE game_id = $game_id");
        // delete game
        DbManager::make_no_result_querry(
            "DELETE FROM games WHERE id = $game_id");
    }

    static function mark_game_action($game_id){
        DbManager::make_no_result_querry(
            "UPDATE games SET last_activity_time = CURRENT_TIMESTAMP()
            WHERE id = $game_id"
        );
    }

    // starting game

    function start_game(){
        // set game status started
        DbManager::make_no_result_querry(
            "UPDATE games SET status = 1 WHERE id = $this->game_id");
        // set active player
        $players = get_game_players($this->game_id);
        $idx = random_int(0, count($players)-1);
        $active_id = $players[$idx]["id"];
        self::set_player_active($active_id, $this->game_id);
    }

    function start_game_if_can(){
        /* start game if can */;
        $match_players = get_game_players($this->game_id);
        // loop checking if can start
        $can_start = true;
        foreach($match_players as $player){
            // check if someone not started
            if ($player['status'] == 0 ){
                $can_start = false;
            }
        }
        // to start must be at least 2 players
        if (count($match_players) > 1 and $can_start){
            $this->start_game();
            return true;
        }
        // to start game must be full of players
        if (count($match_players) >= 4){
            // to prepare players becouse they not prepared
            PlayerManager::prepare_players_for_main_game($this->game_id);
            $this->start_game();
            return true;
        }
        return false;
    }

}
