<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/MapSupport.php');
$status = 0;
if($IS_ADMIN){
	$mapManager = new MapSupport();
	if($mapManager->postImage($_POST)) $status = 1;
}
echo $status;
?>
