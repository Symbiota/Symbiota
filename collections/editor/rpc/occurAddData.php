<?php
include_once(__DIR__ . '/../../../config/symbini.php');
global $SERVER_ROOT, $IS_ADMIN, $USER_RIGHTS;

include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');

$collid = array_key_exists('collid',$_REQUEST);
$responseArr = array();
$isEditor = 0;
if($collid){
	if($IS_ADMIN){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])){
		$isEditor = 1;
	}
	if($isEditor){
		$occurrenceEditor = new OccurrenceEditorManager();
		$occurrenceEditor->setCollId($_REQUEST['collid']);

		if(array_key_exists('catalognumber',$_REQUEST) && $occurrenceEditor->catalogNumberExists($_REQUEST['catalognumber'])){
			$responseArr['occid'] = $occurrenceEditor->getOccId();
			if($_REQUEST['addaction'] == '1'){
				$responseArr['action'] = 'none';
				$responseArr['status'] = 'false';
				$responseArr['error'] = 'dupeCatalogNumber';
			}
			elseif($_REQUEST['addaction'] == '2'){
				$responseArr['action'] = 'update';
				$responseArr['status'] = 'true';
				if(!$occurrenceEditor->editOccurrence($_REQUEST)){
					$responseArr['status'] = 'false';
					$responseArr['error'] = $occurrenceEditor->getErrorStr();
				}
			}
		}
		else{
			$responseArr['action'] = 'add';
			if($occurrenceEditor->addOccurrence($_REQUEST)) {
				$responseArr['status'] = 'true';
				$responseArr['occid'] = $occurrenceEditor->getOccId();
			} else {
				$responseArr['status'] = 'false';
				$responseArr['error'] = $occurrenceEditor->getErrorStr();
			}
		}
	}
}
echo json_encode($responseArr);
?>
