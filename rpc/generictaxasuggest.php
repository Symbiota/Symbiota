<?php
header('Content-Type: application/json; charset=' . $CHARSET);

$term = $_REQUEST['term'];

//$url = 'https://api.gbif.org/v1/species/suggest?offset=0&limit=20&q=' . $term . '&highlight=true&spellCheck=true&spellCheckCount=10&datasetKey=d7dddbf4-2cf0-4f39-9b2a-bb099caae36c';
$url = 'https://api.checklistbank.org/dataset/314231/nameusage/search?q=' . $term;

$colData = unserialize(file_get_contents($url));
$retColArr = Array();
if(array_key_exists('results',$colData)){
	$retColArr = $colData['results'];
}

if($retColArr){
	$retArr = Array();
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