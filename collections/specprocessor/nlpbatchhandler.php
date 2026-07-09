<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecProcNlpLbccLichen.php');
include_once($SERVER_ROOT.'/classes/SpecProcNlpLbccBryophyte.php');

$collTarget = array_key_exists("colltarget",$_REQUEST)?$_REQUEST["colltarget"]:42;
//$processingStatus = array_key_exists("processingstatus",$_REQUEST)?$_REQUEST["processingstatus"]:'';
$ocrSource = array_key_exists("ocrsource",$_REQUEST)?$_REQUEST["ocrsource"]:'abbyy';
$printMode = array_key_exists("printmode",$_REQUEST)?$_REQUEST["printmode"]:0;
$parserTarget = array_key_exists("parsertarget",$_REQUEST)?$_REQUEST["parsertarget"]:'lbcc';
$parserTarget = strtolower($parserTarget);

$nlpHandler = null;
if($parserTarget == 'lbcc'){
	//$nlpHandler = new SpecProcNlpLbcc();
}
elseif($parserTarget == 'salix'){
	//$nlpHandler = new SpecProcNlpSalix();
}

if($nlpHandler){
	//$nlpHandler->setPrintMode($printMode);
	//$nlpHandler->setLogErrors(true);
	//$nlpHandler->batchProcess($collTarget,$ocrSource);
}
?>