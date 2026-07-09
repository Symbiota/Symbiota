<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcUsers.php');
header("Content-Type: application/json; charset=".$CHARSET);

$term = $_REQUEST['term'];
$collid = isset($_REQUEST['collid'])?$_REQUEST['collid']:0;

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collid){
	if(array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])) $isEditor = true;
	elseif(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = true;
}

$retArr = array();
if($isEditor){
	$rpcManager = new RpcUsers();
	if($rpcManager->isValidApiCall()) $retArr = $rpcManager->getUserArr($term);
}

echo json_encode($retArr);
?>