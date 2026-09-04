<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: application/json; charset=' . $CHARSET);

$term = $_REQUEST['term'];
$taxAuthId = array_key_exists('taid',$_REQUEST) ? Sanitize::int($_REQUEST['taid']) : 1;
$rankLimit = array_key_exists('rlimit',$_REQUEST) ? Sanitize::int($_REQUEST['rlimit']) : 0;
$rankLow = array_key_exists('rlow',$_REQUEST) ? Sanitize::int($_REQUEST['rlow']) : 0;
$rankHigh = array_key_exists('rhigh',$_REQUEST) ? Sanitize::int($_REQUEST['rhigh']) : 0;

$retArr = array();
if($term){
	if($rankLimit){
		$rankLow = $rankLimit;
		$rankHigh = $rankLimit;
	}
	$rpcManager = new RpcTaxonomy();
	$rpcManager->setTaxAuthId($taxAuthId);
	$retArr = $rpcManager->getTaxaSuggest($term, $rankLow, $rankHigh);
}
echo json_encode($retArr);
?>