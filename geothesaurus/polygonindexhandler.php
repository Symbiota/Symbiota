<?php
/*
 * For Future page:
 * php geothesaurus/polygonindexhandler.php pending 10 10000
 * php geothesaurus/polygonindexhandler.php 123,456 10000
 */


$startTime = microtime(true);

$_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'off';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];
$_SERVER['PHP_SELF'] = $_SERVER['PHP_SELF'] ?? '/geothesaurus/polygonindexhandler.php';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'];
$_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
$_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

include_once(__DIR__ . '/../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrencePolygonIndex.php');

$mode = $argv[1] ?? 'pending';
$pendingLimit = $argv[2] ?? 10;
$batchSize = $argv[3] ?? 50000;
if($mode !== 'pending' && isset($argv[2])) $batchSize = $argv[2];

$indexManager = new OccurrencePolygonIndex(MySQLiConnectionFactory::getCon('write'));

if($mode === 'pending'){
	$count = $indexManager->rebuildPendingPolygons($pendingLimit, $batchSize);
	echo 'Rebuilt '.$count.' pending polygon indexes'."\n";
}
else{
	$geoThesIDs = OccurrencePolygonIndex::sanitizePolygonIds(explode(',', $mode));
	foreach($geoThesIDs as $geoThesID){
		if($indexManager->rebuildPolygons($geoThesID, $batchSize)){
			echo 'Rebuilt polygon index for geoThesID: '.$geoThesID."\n";
		}
		else{
			echo 'ERROR rebuilding geoThesID: '.$geoThesID.': '.$indexManager->getErrorMessage()."\n";
		}
	}
}

printf('Execution time: %.2f seconds'."\n", microtime(true) - $startTime);
?>
