<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$classNew = $_REQUEST['classnew'];
$samplePK = $_REQUEST['samplepk'];

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$json = '{}';
if($isEditor && $classNew){
	$shipmentManager = new ShipmentManager();
	$json = $shipmentManager->confirmCollectionTransfer($samplePK,$classNew);
}
echo $json;
?>