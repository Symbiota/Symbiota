<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: application/json; charset=' . $CHARSET);
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');

$term = (array_key_exists('term', $_REQUEST) ? $_REQUEST['term'] : '');
$taxonSearchType = (array_key_exists('searchType', $_REQUEST) ? Sanitize::int($_REQUEST['searchType']) : 2);
$rankMin = (array_key_exists('rankMin', $_REQUEST) ? Sanitize::int($_REQUEST['rankMin']) : '');
$rankMax = (array_key_exists('rankMax', $_REQUEST) ? Sanitize::int($_REQUEST['rankMax']) : '');
$limitToAccepted = empty($_REQUEST['limitToAccepted']) ? 0 : 1;
$taxAuthID = (array_key_exists('taxAuthID', $_REQUEST) ? Sanitize::int($_REQUEST['taxAuthID']) : 1);

$retArr = array();
if($term){
	$rpcManager = new RpcTaxonomy();
	$rpcManager->setTaxonSearchType($taxonSearchType);
	if(is_numeric($rankMin)) $rpcManager->setRankMin($rankMin);
	if(is_numeric($rankMax)) $rpcManager->setRankMax($rankMax);
	if($limitToAccepted){
		$rpcManager->setLimitToAccepted($limitToAccepted);
		$rpcManager->setTaxAuthId($taxAuthID);
	}
	$retArr = $rpcManager->getTaxaSuggest($term);
}
echo json_encode($retArr);
?>
