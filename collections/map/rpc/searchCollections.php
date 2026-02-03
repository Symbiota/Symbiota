<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceMapManager.php');

header('Content-Type: application/json;charset=' . $CHARSET);
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');

ob_start();

$recLimit = array_key_exists('recordlimit', $_REQUEST) && is_numeric($_REQUEST['recordlimit'])? $_REQUEST['recordlimit'] : 15000;

$mapManager = new OccurrenceMapManager();
$searchArray = $mapManager->getQueryTermArr();

//Gets Coordinates
$coordArr = [
	'taxaArr' => [],
	'collArr' => [],
	'recordArr' => []
];

try {
	$coordArr = $mapManager->getCoordinateMap(0, $recLimit);
} catch(Throwable $th) {
	$coordArr['error'] = $th->getMessage();
}

$coordArr['origin'] = GeneralUtil::getDomain() . $GLOBALS['CLIENT_ROOT'];
$coordArr['query'] = http_build_query($searchArray);
$coordArr['searchArray'] = $searchArray;

echo json_encode($coordArr);
