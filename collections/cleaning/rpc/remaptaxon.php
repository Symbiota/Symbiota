<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyCleaner.php');
header("Content-Type: text/html; charset=UTF-8");

$collid = $_REQUEST['collid'];
$oldSciname = urldecode($_REQUEST['oldsciname']);
$tid = $_REQUEST['tid'];
$idQualifier = (isset($_REQUEST['idq'])?$_REQUEST['idq']:'');

$status = '0';
if($collid && $oldSciname && $tid){
	$cleanerManager = new TaxonomyCleaner();
	if($cleanerManager->remapOccurrenceTaxon($collid, $oldSciname, $tid, $idQualifier)){
		$status = '1';
	}
}
echo $status;
?>