<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');

$collid = $_REQUEST['collid'];

if(is_numeric($collid)){
	$sesarManager = new OccurrenceSesar();
	$sesarManager->setCollid($collid);
	$seed = $sesarManager->generateIgsnSeed();
	echo $seed;
}
?>