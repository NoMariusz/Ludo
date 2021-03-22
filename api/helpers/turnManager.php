<?php

class TurnManager
{
    public $game_id;

    function __construct($game_id)
    {
        $this->game_id = $game_id;
    }

    function change_turn()
    {
        // change active player
        $this->change_active_player($this->game_id);
        // change values at turn start
        DbManager::make_no_result_querry(
            "UPDATE games SET turn_start_time = CURRENT_TIMESTAMP(),
            throwed_cube =  0 WHERE id = $this->game_id"
        );
        // set all pawns in game can not be moved
        DbManager::make_no_result_querry(
            "UPDATE pawns SET can_be_moved = 0 WHERE game_id = $this->game_id"
        );
        // give player place if end game
        $this -> made_giving_places();
    }

    private function change_active_player()
    {
        /* function changing player having turn in base */
        // get playing players
        $players = get_game_playing_players($this->game_id);
        // find active player
        $active_players = DbManager::make_querry(
            "SELECT * from players WHERE game_id = $this->game_id
            AND status > 2 AND status != 5;"
        );
        // if not is active player in match end work
        if(count($active_players) == 0){
            return false;
        }
        // find what index have active player
        $active_idx = array_search($active_players[0], $players);
        // increment active player index and set next player active
        $active_idx++;
        if ($active_idx >= count($players)) {
            $active_idx = 0;
        }
        $active_id = $players[$active_idx]['id'];
        GameManager::set_player_active($active_id, $this->game_id);
    }

    // checking if someone win

    private function made_giving_places()
    {
        // get all players from match without place
        $players = DbManager::make_querry(
            "SELECT * FROM players WHERE game_id = $this->game_id
            AND place IS NULL"
        );
        // for each player if his pawns at home then give them place
        foreach($players as $player){
            $pawns = get_player_pawns($player['id']);
            if (BoardManager::are_every_pawns_at_home($pawns)){
                $player_manager = new PlayerManager($player['id']);
                $player_manager->give_player_place();
            }
        }
        // if remain only one playing player then give him place
        if (count($players) == 1){
            $player_manager = new PlayerManager($players[0]['id']);
            $player_manager->give_player_place();
        }
    }


}
