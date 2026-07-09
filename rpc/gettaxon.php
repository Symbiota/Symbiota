<?php
/*
 * Input: string representing scientific name
 * Return: array containing tid (key), name, author, and kingdom (if name is homonym)
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/APITaxonomy.php');
header('Content-Type: application/json; charset='.$CHARSET);

$taxonAPI = new APITaxonomy();
$taxonArr = $taxonAPI->getTaxon($_REQUEST["sciname"]);

echo json_encode($taxonArr);
?>