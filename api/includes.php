<?php
/* Include all non public modules */

// include absolute to not have problem with different paths
$root = $_SERVER['DOCUMENT_ROOT'].'/Ludo';

include_once($root.'/api/hidden.php');
include_once($root.'/api/utils.php');
include_once($root.'/api/constants.php');
include_once($root.'/api/helpers/gameManager.php');
include_once($root.'/api/helpers/boardManager.php');
include_once($root.'/api/helpers/playerManager.php');
include_once($root.'/api/helpers/turnManager.php');
?>