<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceChecklistManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$checklistManager = new OccurrenceChecklistManager('write');

$taxonFilter = array_key_exists("taxonfilter",$_REQUEST)&&$_REQUEST["taxonfilter"]?$_REQUEST["taxonfilter"]:1;
$interface = array_key_exists("interface",$_REQUEST)&&$_REQUEST["interface"]?$_REQUEST["interface"]:"checklist";

//Sanitation
if(!is_numeric($taxonFilter)) $taxonFilter = 1;

$dynClid = $checklistManager->buildSymbiotaChecklist($taxonFilter);
if($interface == "key"){
	header("Location: ../ident/key.php?dynclid=".$dynClid."&taxon=All Species");
}
else{
	header("Location: ../checklists/checklist.php?dynclid=".$dynClid);
}
?>