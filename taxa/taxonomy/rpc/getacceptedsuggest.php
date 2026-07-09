<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcTaxonomy.php');
header("Content-Type: application/json; charset=".$CHARSET);


$queryTerm = $_REQUEST['term'];
$taxAuthId = array_key_exists('taid',$_REQUEST)?$_REQUEST['taid']:'1';

$rpcManager = new RpcTaxonomy();
$rpcManager->setTaxAuthId($taxAuthId);
$retArr = $rpcManager->getAcceptedTaxa($queryTerm);

echo json_encode($retArr);
?>