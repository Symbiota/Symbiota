<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyCleaner.php');

$term = $_REQUEST['term'];

$searchManager = new TaxonomyCleaner();
$nameArr = $searchManager->getTaxaSuggest($_REQUEST['term']);

echo json_encode($nameArr);
?>