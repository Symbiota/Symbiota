<?php
/*
 * Input: string representing scientific name
 * Return: array containing tid (key), name, author, and kingdom (if name is homonym)
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');

header('Content-Type: application/json; charset=' . $CHARSET);

$sciname = $_REQUEST['sciname'] ?? '';
$rankid = array_key_exists('rankid', $_POST) ? Sanitize::int($_POST['rankid']) : 0;
$author = array_key_exists('author', $_POST) ? $_POST['author'] : 0;
$kingdomName = array_key_exists('kingdomName', $_POST) ? $_POST['kingdomName'] : 0;

$taxonArr = array();
if($sciname){
	$rpcManager = new RpcTaxonomy();
	$taxonArr = $rpcManager->getTaxonUnit($sciname, $rankid, $author, $kingdomName);
}
echo json_encode($taxonArr);
?>