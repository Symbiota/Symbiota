<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/DynamicChecklistManager.php');
include_once($SERVER_ROOT . '/content/lang/checklists/checklist . ' . $LANG_TAG . '.php');
header("Content-Type: text/html; charset=".$CHARSET);

$lat = $_POST['lat'];
$lng = $_POST['lng'];
$radius = $_POST['radius'];
$radiusUnits = $_POST['radiusunits'];
$dynamicRadius = (isset($DYN_CHECKLIST_RADIUS)?$DYN_CHECKLIST_RADIUS:10);
$taxa = $_POST['taxa'];
$tid = $_POST['tid'];
$interface = $_POST['interface'];

//sanitation
if(!is_numeric($lat)) $lat = 0;
if(!is_numeric($lng)) $lng = 0;
if(!is_numeric($radius)) $radius = 0;
if($radiusUnits != 'mi') $radiusUnits == 'km';
if(!is_numeric($dynamicRadius)) $dynamicRadius = 10;
$taxa = filter_var($taxa,FILTER_SANITIZE_STRING);
if(!is_numeric($tid)) $tid = 0;

$dynClManager = new DynamicChecklistManager();

if($taxa && !$tid) $tid = $dynClManager->getTid($taxa);
$dynClid = 0;
if($radius) $dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $radiusUnits, $tid);
else $dynClid = $dynClManager->createDynamicChecklist($lat, $lng, $dynamicRadius, $tid);

if($dynClid){
	if($interface == "key"){
		header("Location: ".$CLIENT_ROOT."/ident/key.php?dynclid=".$dynClid."&taxon=All Species");
	}
	else{
		header("Location: ".$CLIENT_ROOT."/checklists/checklist.php?dynclid=".$dynClid);
	}
}
else echo $LANG['ERROR_GEN_CHECK'];
$dynClManager->removeOldChecklists();
?>
