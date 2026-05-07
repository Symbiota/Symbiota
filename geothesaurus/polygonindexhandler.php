<?php
/*
 * CLI handler for building cached occurrence memberships for searchable
 * geographic polygons.
 *
 * Examples:
 * php polygonindexhandler.php pending 10
 * php polygonindexhandler.php 123,456
 */
if(PHP_SAPI !== 'cli') exit('CLI only');

include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrencePolygonIndex.php');

$mode = $argv[1] ?? 'pending';
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
			echo 'Rebuilt polygon index for geoThesID '.$geoThesID."\n";
		}
		else{
			echo 'ERROR rebuilding geoThesID '.$geoThesID.': '.$indexManager->getErrorMessage()."\n";
		}
	}
}
?>
