<?php
include_once('../../../config/symbini.php'); 
include_once($SERVER_ROOT.'/classes/OccurrenceAttributes.php');
header("Content-Type: application/json; charset=".$CHARSET);

$exact = isset($_REQUEST['exact'])&&$_REQUEST['exact']?true:false;

$attrManager = new OccurrenceAttributes('readonly');
echo $attrManager->getTaxonFilterSuggest($_REQUEST['term'],$exact);
?>