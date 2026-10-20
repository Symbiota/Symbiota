<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');

$term = ($_REQUEST['term']);

if($SYMB_UID && $PARAMS_ARR){
	$datasetManager = new OccurrenceDataset();
	$retArr = $datasetManager->getUserList($term);
}

echo json_encode($retArr);

?>
