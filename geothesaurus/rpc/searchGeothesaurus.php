<?php
include_once('../../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header('Content-Type: application/json;charset='.$CHARSET);

ob_start();
$geoterm = array_key_exists('geoterm',$_REQUEST)?$_REQUEST['geoterm']:'';
$geoManager = new GeographicThesaurus();
$geoterms = $geoManager->searchGeothesaurus($geoterm);
echo json_encode($geoterms);
?>
