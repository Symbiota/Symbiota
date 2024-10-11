<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/utilities/SpecProcNlpUtil.php');

$targetParser = 'common';
if(strpos($_SERVER['SERVER_NAME'],'bryophyte') !== false){
	include_once($SERVER_ROOT.'/classes/SpecProcNlpLbccBryophyte.php');
	$targetParser = 'bryophyte';
}
elseif(strpos($_SERVER['SERVER_NAME'],'lichen') !== false){
	include_once($SERVER_ROOT.'/classes/SpecProcNlpLbccLichen.php');
	$targetParser = 'lichen';
}
else{
	include_once($SERVER_ROOT.'/classes/SpecProcNlpLbcc.php');
}

header("Content-Type: text/html; charset=UTF-8");

$rawStr = $_REQUEST['rawocr'];
$collid = $_REQUEST['collid'];
$catNum = $_REQUEST['catnum'];

$dwcArr = array();
if($rawStr) {
	$handler;
	if($targetParser == 'bryophyte'){
		$handler = new SpecProcNlpLbccBryophyte();
		
	}
	elseif($targetParser == 'lichen'){
		$handler = new SpecProcNlpLbccLichen();
	}
	else{
		$handler = new SpecProcNlpLbcc();
	}
	if($handler) {
		$handler->setCollId($collid);
		$handler->setCatalogNumber($catNum);
		$dwcArr = $handler->parse($rawStr);
		$dwcArr = SpecProcNlpUtil::cleanDwcArr($dwcArr);
	}
}

echo json_encode($dwcArr);
?>