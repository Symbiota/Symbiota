<?php
/*
 * Input: string representing scientific name
 * Return: array containing tid (key), name, author, and kingdom (if name is homonym)
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');

header('Content-Type: application/json; charset=' . $CHARSET);

$sciname = isset($_REQUEST['sciname']) ? $_REQUEST['sciname'] : '';

$taxonArr = array();
if($sciname){
	$rpcManager = new RpcTaxonomy();
	$taxonArr = $rpcManager->getTaxon($sciname);
}
echo json_encode($taxonArr);
?>