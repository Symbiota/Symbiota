<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceExsiccatae.php');

$exsManager = new OccurrenceExsiccatae();
$exsArr = $exsManager->getExsiccatiSuggest($_REQUEST['term']);

echo json_encode($exsArr);
?>