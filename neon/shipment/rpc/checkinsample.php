<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$sampleIdentifier = $_REQUEST['identifier'];
$shipmentPK = (array_key_exists('shipmentpk',$_REQUEST)?$_REQUEST['shipmentpk']:'');
$sampleReceived = (array_key_exists('received',$_REQUEST)?$_REQUEST['received']:'');
$acceptedForAnalysis = (array_key_exists('accepted',$_REQUEST)?$_REQUEST['accepted']:'');
$condition = (array_key_exists('condition',$_REQUEST)?$_REQUEST['condition']:'');
$alternativeSampleID = (array_key_exists('altSampleID',$_REQUEST)?$_REQUEST['altSampleID']:'');
$notes = (array_key_exists('notes',$_REQUEST)?$_REQUEST['notes']:'');

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

if($isEditor){
	$shipmentManager = new ShipmentManager();
	if($shipmentPK) $shipmentManager->setShipmentPK($shipmentPK);
	$json = $shipmentManager->checkinSample($sampleIdentifier,$sampleReceived,$acceptedForAnalysis,$condition,$alternativeSampleID,$notes);
}
echo $json;
?>