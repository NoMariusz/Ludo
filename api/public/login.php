<?php
include("../includes.php");

function main(){
    $response = array('result' => true);

    if (!isset($_SESSION)){
        try {
            create_player($_GET['nick']);
            add_player_to_game();
        } catch (Exception $e) {
            $response['result'] = false;
        }
    }

    echo json_encode($response);
}

main();
?>