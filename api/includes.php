<?php
/* Include all non public modules */
// error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set('Europe/Warsaw');

// include absolute to not have problem with different paths
if (php_sapi_name() == "cli") {
    // In cli-mode
    $root = '/app/api';
} else {
    // Not in cli-mode
    $root = $_SERVER['DOCUMENT_ROOT'] . '/api';
}

include_once($root . '/hidden.php');
include_once($root . '/utils.php');
include_once($root . '/constants.php');
include_once($root . '/helpers/DbManager.php');
include_once($root . '/helpers/GameManager.php');
include_once($root . '/helpers/BoardManager.php');
include_once($root . '/helpers/PlayerManager.php');
include_once($root . '/helpers/TurnManager.php');
?>