<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAttributes.php');
header("Content-Type: application/json; charset=".$CHARSET);

$exact = isset($_REQUEST['exact'])&&$_REQUEST['exact']?true:false;

$attrManager = new OccurrenceTraitAttributes('readonly');
echo $attrManager->getTaxonFilterSuggest($_REQUEST['term'],$exact);
?>
