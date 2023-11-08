<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$shipmentPK = (isset($_POST['shipmentPK'])?$_POST['shipmentPK']:'');
$exportTask = $_POST['exportTask'];

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$status = '';
if($isEditor){
	$shipmentManager = new ShipmentManager();
	if($exportTask == 'receipt'){
		$shipmentManager->setShipmentPK($shipmentPK);
		$shipmentManager->exportShipmentReceipt();
	}
	elseif($exportTask == 'sampleList'){
		$shipmentManager->setShipmentPK($shipmentPK);
		$shipmentManager->exportSampleList();
	}
}
?>