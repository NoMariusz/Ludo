<?php
/* Include all non public modules */

// include absolute to not have problem with different paths
$root = $_SERVER['DOCUMENT_ROOT'].'/Ludo/api';

include_once($root.'/hidden.php');
include_once($root.'/utils.php');
include_once($root.'/constants.php');
include_once($root.'/helpers/DbManager.php');
include_once($root.'/helpers/GameManager.php');
include_once($root.'/helpers/boardManager.php');
include_once($root.'/helpers/playerManager.php');
include_once($root.'/helpers/TurnManager.php');
?>