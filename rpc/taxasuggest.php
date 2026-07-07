<?php
/*
 * Input: term = scientific name fragment, taxonType, $rankLow = rankid lower limit, $rankHigh = rankid upper limit
 * Return: autosuggest return list
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonSearchSupport.php');
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');
header('Content-Type: application/json; charset=' . $CHARSET);

$term = (array_key_exists('term', $_REQUEST) ? $_REQUEST['term'] : '');
$taxonType = (array_key_exists('t', $_REQUEST) ? $_REQUEST['t'] : 0);
$rankLow = (array_key_exists('ranklow', $_REQUEST) ? $_REQUEST['ranklow'] : 0);
$rankHigh = (array_key_exists('rankhigh', $_REQUEST) ? $_REQUEST['rankhigh'] : 0);

$nameArr = array();
if($term){
	$searchManager = new TaxonSearchSupport();
	if($rankLow || $rankHigh){
		$nameArr = $searchManager->getTaxaSuggestFilteredByRank($term, $rankLow, $rankHigh);
	}
	else{
		if(isset($DEFAULT_TAXON_SEARCH) && !$taxonType) $taxonType = $DEFAULT_TAXON_SEARCH;
		$nameArr = $searchManager->getTaxaSuggest($term, $taxonType);
	}
}
echo json_encode($nameArr);
?>
