<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAttributes.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_REQUEST['occid'];
$collid = $_REQUEST['collid'];
$action = array_key_exists('submitAction',$_REQUEST)?$_REQUEST['submitAction']:'';

$status = 0;

$attrManager = new OccurrenceAttributes();
$attrManager->setOccid($occid);

$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN){
		$isEditor = true;
	}
	elseif($collid){
		if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])){
			$isEditor = true;
		}
		elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
			$isEditor = true;
		}
	}
}

if($isEditor){
	$postArr = array('occid' => $occid, 'traitid' => $_REQUEST['traitID'], 'setstatus' => $_REQUEST['setStatus'], 'source' => $_REQUEST['source'], 'notes' => $_REQUEST['notes']);
	$stateArr = json_decode($_REQUEST['stateData'],true);
	$postArr = array_merge($postArr,$stateArr);
	if($action == 'addTraitCoding'){
		if($attrManager->addAttributes($postArr,$SYMB_UID)){
			$status = 1;
		}
	}
	elseif($action == 'editTraitCoding'){
		if($attrManager->editAttributes($postArr)){
			$status = 1;
		}
	}
	elseif($action == 'deleteTraitCoding'){
		if($attrManager->deleteAttributes($_REQUEST['traitID'])){
			$status = 2;
		}
	}
	if($attrManager->getErrorMessage()) echo $attrManager->getErrorMessage();
}
echo $status;
?>