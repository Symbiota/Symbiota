<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDuplicate.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/editor/rpc/editor_rpc');

$dupid = array_key_exists('dupid',$_REQUEST)?$_REQUEST['dupid']:'';
$occid = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:'';

$isEditor = false;
if(array_key_exists("CollAdmin",$USER_RIGHTS)) $isEditor = true;
elseif(array_key_exists("CollEditor",$USER_RIGHTS)) $isEditor = true;
if($IS_ADMIN || $isEditor){
	if(is_numeric($occid) && is_numeric($dupid)){
		$dupeManager = new OccurrenceDuplicate();
		if($dupeManager->deleteOccurFromCluster($dupid, $occid)){
			echo '1';
		}
		else{
			echo $dupeManager->getErrorStr();
		}
	}
	else{
		echo $LANG['ERROR_1'];
	}
}
else{
	echo $LANG['ERROR_2'];
}
?>
