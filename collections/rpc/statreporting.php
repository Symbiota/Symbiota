<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAccessStats.php');

$occidStr = array_key_exists('occidstr',$_REQUEST)?$_REQUEST['occidstr']:'';
$sql = array_key_exists('sql',$_REQUEST)?$_REQUEST['sql']:'';
$accessType = $_REQUEST['accesstype'];

$statManager = new OccurrenceAccessStats();

if($occidStr){
	$statManager->batchRecordEvents($occidStr,$accessType);
}
elseif($sql){
	$statManager->batchRecordEvents($sql,$accessType);
}
?>
