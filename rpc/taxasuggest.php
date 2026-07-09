<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: application/json; charset=' . $CHARSET);
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');

$term = (array_key_exists('term', $_REQUEST) ? $_REQUEST['term'] : '');
$taxonSearchType = (array_key_exists('searchType', $_REQUEST) ? Sanitize::int($_REQUEST['searchType']) : 2);
$rankLow = (array_key_exists('ranklow', $_REQUEST) ? Sanitize::int($_REQUEST['ranklow']) : 0);
$rankHigh = (array_key_exists('rankhigh', $_REQUEST) ? Sanitize::int($_REQUEST['rankhigh']) : 0);

$retArr = array();
if($term){
	$rpcManager = new RpcTaxonomy();
	$rpcManager->setTaxonSearchType($taxonSearchType);
	$retArr = $rpcManager->getTaxaSuggest($term, $rankLow, $rankHigh);
}
echo json_encode($retArr);
?>
