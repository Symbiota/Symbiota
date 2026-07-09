<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceExsiccatae.php');

//To be used to convert title lookups to jQuery autocomplete functions
$exsManager = new OccurrenceExsiccatae();
$exsArr = $exsManager->getExsAbbrevSuggest($_REQUEST['term']);

echo json_encode($exsArr);
?>