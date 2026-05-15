<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT.'/classes/OccurrenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: text/html; charset=' . $CHARSET);

if($SYMB_UID){
	$datasetID = array_key_exists('targetdatasetid', $_POST) ? $_POST['targetdatasetid'] : 0;
	$sourcePage = array_key_exists('sourcepage',$_POST) ? $_POST['sourcepage'] : 'datasetmanager';
	$occid = array_key_exists('occid',$_POST) ? $_POST['occid'] : 0;
	$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

	//Sanitation
	if(!is_numeric($datasetID) && $datasetID != '--newDataset') $datasetID = 0;

	if($action && $datasetID){
		$datasetManager = new OccurrenceDataset();
		if($datasetID == '--newDataset'){
			$name = 'newDataset ('.date('Y-m-d H:i:s').')';
			if($datasetManager->createDataset($name, '', '', '', $SYMB_UID)) $datasetID = $datasetManager->getDatasetId();
			else echo $datasetManager->getErrorMessage();
		}
		$datasetID = Sanitize::int($datasetID);
		if($datasetID){
			$targetLink = 'datasetmanager.php?datasetid=' . $datasetID;
			if($sourcePage == 'individual') $targetLink = '../individual/index.php?occid=' . Sanitize::int($occid);
			if($action == 'addSelectedToDataset'){
				if($occid){
					if($datasetManager->addSelectedOccurrences($datasetID, $occid)){
						header('Location: '.$targetLink);
					}
					else echo $datasetManager->getErrorMessage();
				}
			}
			elseif($action == 'addAllToDataset'){
				$occurManager = new OccurrenceManager('write');
				if($occurManager->addOccurrencesToDataset($datasetID)) header('Location: '.$targetLink);
				else echo $occurManager->getErrorMessage();
			}
		}
	}
}
?>