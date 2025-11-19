<?php
	include_once('../../../config/symbini.php');
	include_once($SERVER_ROOT.'/classes/OccurrenceActionManager.php');
	include_once($SERVER_ROOT . '/classes/utilities/Language.php');

	Language::load('collections/editor/rpc/editor_rpc');
	
	$occid = array_key_exists('occid',$_REQUEST)             ? $_REQUEST['occid']        : null;
	$requesttype = array_key_exists('requesttype',$_REQUEST) ? $_REQUEST['requesttype']  : null ;
	$remarks = array_key_exists('remarks',$_REQUEST)         ? $_REQUEST['remarks']      : '';
    $uid = $SYMB_UID;	

    if ($uid!=null) { 
	   $actionManager = new OccurrenceActionManager();
       $result = $actionManager->makeOccurrenceActionRequest($uid,$occid,$requesttype,$remarks);
       if ($result==null) { 
          $returnValue = $LANG['FAILED_ADD_REQUEST'] . '. ' . $actionManager->getErrorMessage();
       } else { 
          $returnValue = $LANG['ADDED_REQUEST'] . ' ' . $requesttype [$result];
       }
    } 

	echo $returnValue;
?>
