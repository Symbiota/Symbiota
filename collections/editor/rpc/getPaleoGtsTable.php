<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcOccurrenceEditor.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/editor/rpc/getPaleoGtsTable');

header('Content-Type: application/json; charset=' . $CHARSET);

$earlyInterval = isset($_REQUEST['earlyInterval']) ? $_REQUEST['earlyInterval'] : '';
$lateInterval = isset($_REQUEST['lateInterval']) ? $_REQUEST['lateInterval'] : '';
$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : 'simple_map';

$retArr = array();
if($earlyInterval || $lateInterval){
	$paleoManager = new RpcOccurrenceEditor();
	if($format == 'full_map'){

	}
	else{
		$tableStr = $paleoManager->getPaleoGtsTable($earlyInterval, $lateInterval);
		if($tableStr === false){
			$retArr['tableStr'] = '';
			$retArr['error'] = $paleoManager->getErrorMessage();
		}
		else $retArr['tableStr'] = $tableStr;
	}
}
echo json_encode($retArr);
?>
