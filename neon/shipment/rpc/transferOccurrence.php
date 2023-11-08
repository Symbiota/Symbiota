<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$target = $_REQUEST['target'];
$occid = $_REQUEST['occid'];

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$retCode = 0;
if($isEditor && is_numeric($target) && is_numeric($occid)){
	$shipmentManager = new ShipmentManager();
	$retCode = $shipmentManager->transferOccurrence($occid, $target);
}
echo $retCode;
?>