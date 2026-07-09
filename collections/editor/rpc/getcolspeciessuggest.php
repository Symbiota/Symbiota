<?php
include_once('../../../config/symbini.php'); 
include_once($SERVER_ROOT.'/config/dbconnection.php');
header("Content-Type: application/json; charset=".$CHARSET);
$con = MySQLiConnectionFactory::getCon("readonly");
$retColArr = Array();
$retArr = Array();
$term = $_REQUEST['term'];

$colData = file_get_contents('http://www.catalogueoflife.org/col/webservice?name='.$term.'*&format=php&response=terse');
$colData = unserialize($colData);
if(array_key_exists('results',$colData)){
	$retColArr = $colData['results'];
}

if($retColArr){
	foreach($retColArr as $k => $vArr){
		$retArr[$vArr['name']]['id'] = $vArr['name'];
		$retArr[$vArr['name']]['value'] = $vArr['name'];
	}
	ksort($retArr);
	echo json_encode($retArr);
}
else{
	echo 'null';
}
?>