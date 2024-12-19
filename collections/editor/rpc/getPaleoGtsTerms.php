<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcOccurrenceEditor.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/classes/RpcOccurrenceEditor.'.$LANG_TAG.'.php'))
	include_once($SERVER_ROOT.'/content/lang/classes/RpcOccurrenceEditor.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/classes/RpcOccurrenceEditor.en.php');
header('Content-Type: application/json; charset=' . $CHARSET);

$earlyInterval = $_POST['earlyInterval'];
$lateInterval = $_POST['lateInterval'];
$format = isset($_POST['format']) ? $_POST['format'] : 'simple_map';

$retStr = '';
$paleoManager = new RpcOccurrenceEditor();
if($format == 'full_map'){

}
else{
	$retStr = $paleoManager->getPaleoGtsTerms($earlyInterval, $lateInterval);
}

echo $retStr;
?>