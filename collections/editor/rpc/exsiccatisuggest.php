<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceExsiccatae.php');

$exsManager = new OccurrenceExsiccatae();
$exsArr = $exsManager->getExsiccatiSuggest($_REQUEST['term']);

echo json_encode($exsArr);
?>
