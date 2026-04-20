<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/DynamicChecklistManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('checklists/checklist');

header('Content-Type: text/html; charset=' . $CHARSET);

$lat = filter_var($_POST['lat'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$lng = filter_var($_POST['lng'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$radius = $_POST['radius'] ? filter_var($_POST['radius'], FILTER_SANITIZE_NUMBER_INT) : 0;
$radiusUnits = $_POST['radiusunits'];
$interface = $_POST['interface'];
$taxon = $_POST['taxa'];
if(!empty($_POST['tid'])) $taxon = filter_var($_POST['tid'], FILTER_SANITIZE_NUMBER_INT);
if($radius && $radiusUnits == 'mi') $radius = round($radius * 1.6);

$dynClManager = new DynamicChecklistManager();

if($dynClid = $dynClManager->createChecklist($lat, $lng, $taxon, $radius)){
	if($interface == 'key'){
		header('Location: ' . $CLIENT_ROOT . '/ident/key.php?dynclid=' . $dynClid . '&taxon=All Species');
	}
	else{
		header('Location: ' . $CLIENT_ROOT . '/checklists/checklist.php?dynclid=' . $dynClid);
	}
}
else echo $LANG['ERROR_GEN_CHECK'];
$dynClManager->removeOldChecklists();
?>
