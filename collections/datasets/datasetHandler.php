<?php
include_once(__DIR__ . '/../../config/symbini.php');
if($SYMB_UID){
	include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
	include_once($SERVER_ROOT . '/classes/OccurrenceManager.php');
	include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

	header('Content-Type: text/html; charset=' . $CHARSET);

	$datasetID = array_key_exists('targetdatasetid', $_POST) ? $_POST['targetdatasetid'] : 0;
	$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

	//Sanitation
	if(!is_numeric($datasetID) && $datasetID != '--newDataset') $datasetID = 0;

	if($action && $datasetID){
		$datasetManager = new OccurrenceDataset();
		$datasetManager->setDatasetID($datasetID);
		if($datasetID == '--newDataset'){
			$dataArr = array('datasetName' => 'newDataset (' . date('Y-m-d H:i:s') . ')');
			if($datasetManager->insertDataset($dataArr)) $datasetID = $datasetManager->getDatasetId();
			else echo $datasetManager->getErrorMessage();
		}
		$datasetID = Sanitize::int($datasetID);
		if($datasetID){
			$occid = $_POST['occid'];
			$sourcePage = array_key_exists('sourcepage',$_POST) ? $_POST['sourcepage'] : 'datasetmanager';
			$targetLink = 'datasetmanager.php?datasetid=' . $datasetID;
			if($sourcePage == 'individual') $targetLink = '../individual/index.php?occid=' . Sanitize::int($occid);
			if($action == 'addSelectedToDataset'){
				if($occid){
					if($datasetManager->insertOccurrences($occid)){
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
