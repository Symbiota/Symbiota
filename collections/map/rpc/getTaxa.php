<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/MapSupport.php');

header('Content-Type: application/json');

$tid = isset($_REQUEST['tid']) ? filter_var($_REQUEST['tid'], FILTER_SANITIZE_NUMBER_INT): 0;

$retArr = false;
if($IS_ADMIN){
	$mapManager = new MapSupport();
	$retArr = $mapManager->getTaxaList($tid);
}
echo json_encode($retArr);
?>
