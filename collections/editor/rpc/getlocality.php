<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDuplicate.php');
header("Content-Type: application/json; charset=".$CHARSET);

$recordedBy = (isset($_REQUEST['recordedby'])?$_REQUEST['recordedby']:'');
$eventDate = (isset($_REQUEST['eventdate'])?$_REQUEST['eventdate']:'');
$locality = (isset($_REQUEST['locality'])?$_REQUEST['locality']:'');
$locationID = (isset($_REQUEST['locationid'])?$_REQUEST['locationid']:'');

$dupManager = new OccurrenceDuplicate();
$retArr = array();
if($locationID) $retArr = $dupManager->getDupeLocalityByLocationID($locationID);
else $retArr = $dupManager->getDupeLocalityByLocalFrag($recordedBy, $eventDate, $locality);

if($retArr){
	if($CHARSET == 'UTF-8'){
		echo json_encode($retArr);
	}
	else{
		$str = '[';
		foreach($retArr as $k => $vArr){
			$str .= '{"id":"'.$vArr['id'].'","value":"'.str_replace('"',"''",$vArr['value']).'"},';
		}
		echo trim($str,',').']';
	}
}
else{
	echo 'null';
}
?>