<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: text/html; charset=' . $CHARSET);

$sciName = $_REQUEST['sciname'];
$taxAuthId = array_key_exists('taxauthid',$_POST) ? Sanitize::int($_POST['taxauthid']) : 1;
$rankid = array_key_exists('rankid', $_POST) ? Sanitize::int($_POST['rankid']) : 0;
$author = array_key_exists('author', $_POST) ? $_POST['author'] : 0;

$tid = '';
if($sciName){
	$rpcManager = new RpcTaxonomy();
	$rpcManager->setTaxAuthId($taxAuthId);
	$tid = $rpcManager->getTid($sciName, $rankid, $author);
}
echo $tid;
?>