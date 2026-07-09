<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = $_REQUEST['collid'];
$idType = array_key_exists('idtype',$_REQUEST)?$_REQUEST['idtype']:'out';		//in, out, ex

$loanManager = new OccurrenceLoans();
$loanManager->setCollId($collid);
$retMsg = $loanManager->generateNextID($idType);

echo $retMsg;
?>