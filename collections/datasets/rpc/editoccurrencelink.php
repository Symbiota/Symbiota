<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: application/json');

$datasetId = array_key_exists('datasetid', $_REQUEST) ? Sanitize::int($_REQUEST['datasetid']) : 0;
$occid = array_key_exists('occid', $_REQUEST) ? Sanitize::int($_REQUEST['occid']) : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$retArr = array('status' => false);
if($SYMB_UID && $datasetId && $occid && $action){

	$datasetManager = new OccurrenceDataset();

	//Verify user permissions
	$isEditor = false;
	if ($SYMB_UID == $mdArr['uid']) {
		$isEditor = true;
	}
	elseif($roles = $datasetManager->getUserRoles($datasetId, $SYMB_UID)) {
		if (isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetAdmin'])) {
			$isEditor = true;
		} elseif (isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetEditor'])) {
			$isEditor = true;
		}
	}

	if($isEditor){
		$datasetManager->setDatasetID($datasetId);
		if($action == 'deloccidlink'){
			if($datasetManager->deleteOccurrences($occid)){
				$retArr['stauts'] = true;
			}
			else{
				//$retArr['error'] = $datasetManager->getErrorMessage();
			}
		}
		elseif($action == 'editoccidlink'){
			if(array_key_exists('notes', $_REQUEST)){
				$notes = $_REQUEST['notes'];
				if($datasetManager->updateOccurrence($occid, $notes)){
					$retArr['stauts'] = true;
				}
				else{
					//$retArr['error'] = $datasetManager->getErrorMessage();
				}
			}
		}
	}
}
json_encode($retArr);
?>