<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcOccurrenceEditor.php');

$occid = isset($_REQUEST['occid']) ? filter_var($_REQUEST['occid'], FILTER_SANITIZE_NUMBER_INT) : false;

$retArr = array();
if(is_numeric($occid)){
	$editorManager = new RpcOccurrenceEditor();
	$retArr = $editorManager->getOccurrenceVouchers($occid);
}
echo json_encode($retArr);
?>