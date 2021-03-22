<?php

class PlayerManager
{

    public $player_id;

    function __construct($player_id)
    {
        $this->player_id = $player_id;
    }

    static function create_player($nick)
    {
        /* create player by given nick */
        session_start();
        // add data to db
        $player_id = DbManager::make_safe_insert_id_querry(
            "INSERT INTO players(nick) VALUES(?);",
            "s",
            [$nick]
        );
        // start session
        return $player_id;
    }

    // preparing him for game

    static function prepare_players_for_main_game($game_id)
    {
        // prepare all unprepared players from game to game
        $match_players = get_game_players($game_id);
        // made self instance
        $player_manager = new self(null);
        foreach ($match_players as $player) {
            if ($player['status'] == 0) {
                // foreach player change manager player_id and prepare player
                $player_manager->player_id = $player['id'];
                $player_manager->prepare_player_for_main_game();
            }
        }
    }

    function prepare_player_for_main_game()
    {
        // add him color
        $this->add_color_to_player();
        // make pawns when have color
        BoardManager::make_pawns_for_player($this->player_id);
    }

    private function add_color_to_player()
    {
        // get data
        $player = get_player($this->player_id);
        $game_id = $player['game_id'];
        $match_players = get_game_players($game_id);
        // get available colors
        global $colors;
        $available_colors = $colors;
        // set color to player
        foreach ($match_players as $player) {
            if ($player['color_index'] != -1) {
                $color_index = $player['color_index'];
                // delete color from available
                unset($available_colors[$color_index]);
            }
        }
        // set color to player
        $color_key = array_rand($available_colors);
        DbManager::make_no_result_querry(
            "UPDATE players SET color_index = '$color_key' WHERE
            id = $this->player_id"
        );
    }

    // set player ready

    function set_player_ready_status()
    {
        DbManager::make_no_result_querry(
            "UPDATE players SET status = 1 WHERE id = $this->player_id"
        );
    }

    // player operations

    function check_if_player_have_moves($points)
    {
        $pawns = DbManager::make_querry(
            "SELECT * FROM pawns WHERE player_id = $this->player_id"
        );
        foreach ($pawns as $pawn) {
            $boardManager = new BoardManager($pawn);
            if ($boardManager->check_pawn_can_be_moved($points)) {
                return true;
            }
        }
        return false;
    }

    // giving place

    function give_player_place()
    {
        $player = get_player($this->player_id);
        $game_id = $player["game_id"];
        $last_took_place_result = DbManager::make_querry(
            "SELECT MAX(place) from players WHERE game_id = $game_id");
        $last_took_place = $last_took_place_result[0]["MAX(place)"];
        $place = $last_took_place == null ? 1 : $last_took_place + 1;
        DbManager::make_querry(
            "UPDATE players SET place = $place, status = 5
            WHERE id = $this->player_id"
        );
    }
}
