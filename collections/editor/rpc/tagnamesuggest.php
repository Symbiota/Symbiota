<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcOccurrenceEditor.php');
header('Content-Type: application/json; charset='.$CHARSET);

$retArr = array();

$editorManager = new RpcOccurrenceEditor();
$retArr = $editorManager->getTagName($_REQUEST['collid'], $_REQUEST['term']);

echo json_encode($retArr);
?>