<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/RpcTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');
header('Content-Type: application/json; charset=' . $CHARSET);

$objId = array_key_exists('id',$_REQUEST) ? $_REQUEST['id'] : 0;
$targetId = !empty($_REQUEST['targetid']) ? Sanitize::int($_REQUEST['targetid']) : 0;
$taxAuthId = !empty($_REQUEST['taxauthid']) ? Sanitize::int($_REQUEST['taxauthid']) : 1;
$editorMode = empty($_REQUEST['emode']) ? 0 : 1;
$displayAuthor = !empty($_REQUEST['authors']) ? 1 : 0;
$limitToOccurrences = !empty($_REQUEST['limittooccurrences']) ? 1 : 0;

$rpcManager = new RpcTaxonomy();
$rpcManager->setTaxAuthId($taxAuthId);

$retArr = $rpcManager->getDynamicChildren($objId, $targetId, $displayAuthor, $limitToOccurrences, $editorMode);
echo json_encode($retArr);
?>