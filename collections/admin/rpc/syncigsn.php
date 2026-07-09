<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');

$occid = trim($_REQUEST['occid']);
$catalogNumber = trim($_REQUEST['catnum']);
$igsn = trim($_REQUEST['igsn']);

$statusArr = 0;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)){
	if(is_numeric($occid) && $catalogNumber && $igsn){
		$sesarManager = new OccurrenceSesar();
		$statusArr = $sesarManager->syncIGSN($occid,$catalogNumber,$igsn);
	}
	else $statusArr = array('status'=>0,'errCode'=>9);
}
else $statusArr = array('status'=>0,'errCode'=>8);
echo json_encode($statusArr);
?>