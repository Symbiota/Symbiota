<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$term = $_REQUEST['term'];

$clManager = new ChecklistManager();
$retArr = $clManager->getSpeciesSearch($term);
echo json_encode($retArr);
?>