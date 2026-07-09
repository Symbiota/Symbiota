<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/GlossaryManager.php');

$queryStr = array_key_exists('term',$_REQUEST)?$_REQUEST['term']:$_REQUEST['q'];
$type = $_REQUEST['t'];

$isEditor = false;
if($IS_ADMIN || array_key_exists('GlossaryEditor',$USER_RIGHTS)) $isEditor = true;

$retArr = array();
if($isEditor){
	$glosManager = new GlossaryManager();
	$retArr = $glosManager->getTaxaList($queryStr, $type);
}

echo json_encode($retArr);
?>