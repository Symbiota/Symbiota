<?php
include_once(__DIR__ . '/../../config/symbini.php');
$status = array('status' => false, 'error' => 'unknown');
if($SYMB_UID){
	include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
	include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

	header('Content-Type: application/json; charset=' . $CHARSET);

	if($occid = Sanitize::int($_REQUEST['occid'])){
		$datasetManager = new OccurrenceDataset();
		if($datasetManager->addCartOccurrence($occid)){
			if($datasetManager->getErrorMessage() == 'RECORD_NOT_INSERTED'){
				$status['error'] = 'ALREADY_IN_CART';
			}
			else{
				$status['status'] = true;
				$status['error'] = '';
			}
		}
		else{
			$status['error'] = $datasetManager->getErrorMessage();
		}
	}
}
echo json_encode($status);
?>
