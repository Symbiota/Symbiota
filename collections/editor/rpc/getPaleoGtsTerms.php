<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcOccurrenceEditor.php');
header('Content-Type: application/json; charset=' . $CHARSET);

$earlyInterval = $_POST['earlyInterval'];
$lateInterval = $_POST['lateInterval'];

$searchManager = new RpcOccurrenceEditor();
$retArr = $searchManager->getPaleoGtsTerms($earlyInterval, $lateInterval);

echo json_encode($retArr);
?>