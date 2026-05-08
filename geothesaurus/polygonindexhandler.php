<?php
/*
 * For Future page:
 * php geothesaurus/polygonindexhandler.php pending 10
 * php geothesaurus/polygonindexhandler.php 123,456
 */


$_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'off';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];

include_once(__DIR__ . '/../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrencePolygonIndex.php');

$mode = $argv[1] ?? 'pending';
// TO DELETE limit number for tests only
$limit = $argv[2] ?? 10;

$indexManager = new OccurrencePolygonIndex(MySQLiConnectionFactory::getCon('write'));

if($mode === 'pending'){
	$count = $indexManager->rebuildPendingGeographicPolygonIndexes($limit);
	echo 'Rebuilt '.$count.' pending polygon indexes'."\n";
}
else{
	$geoThesIDs = OccurrencePolygonIndex::sanitizePolygonIds(explode(',', $mode));
	foreach($geoThesIDs as $geoThesID){
		if($indexManager->rebuildGeographicPolygonIndex($geoThesID)){
			echo 'Rebuilt polygon index for geoThesID: '.$geoThesID."\n";
		}
		else{
			echo 'ERROR rebuilding geoThesID: '.$geoThesID.': '.$indexManager->getErrorMessage()."\n";
		}
	}
}
?>
