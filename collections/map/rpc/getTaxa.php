<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/MapSupport.php');

header('Content-Type: application/json');

$retArr = false;
if($IS_ADMIN){
	$mapManager = new MapSupport();
	$retArr = $mapManager->getTaxaList();
}
echo json_encode($retArr);
?>
