<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorResource.php');

$id = $_POST['id'];
$target = $_POST['target'];
$collidTarget = $_POST['collidtarget'];

$retArr = array();
if($id){
	$occManager = new OccurrenceEditorResource();
	$retArr = $occManager->getOccurrenceByIdentifier($id,$target,$collidTarget);
}
echo json_encode($retArr);
?>