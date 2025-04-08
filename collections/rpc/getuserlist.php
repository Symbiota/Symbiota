<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');

$term = ($_REQUEST['term']);

$datasetManager = new OccurrenceDataset();
$retArr = $datasetManager->getUserList($term);

echo json_encode($retArr);

?>