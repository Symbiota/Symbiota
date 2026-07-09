<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');

$collId = $_REQUEST['collid'];
$fieldName = $_REQUEST['fieldname'];
$oldValue = $_REQUEST['oldvalue'];
$buMatch = array_key_exists('bumatch',$_REQUEST)?$_REQUEST['bumatch']:0;

$retCnt = '';
if($fieldName){
	$occManager = new OccurrenceEditorManager();
	$occManager->setCollId($collId);
	$occManager->setQueryVariables();

	$retCnt = $occManager->getBatchUpdateCount($fieldName,$oldValue, $buMatch);
}
echo $retCnt;
?>