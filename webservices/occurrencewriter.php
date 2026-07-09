<?php
/*
 * ****  Input Variables  ********************************************
 *
 * occid (optional): symbiota occurrence record PK. Required if guid is null.
 * recordid (optional): recordID GUID (UUID). Required if occid is null. 
 * dwcobj (required): occurrence edits as a JSON representation of a DwC object (key/value pairs); data must be UTF-8
 * editor (optional): string representing editor 
 * source (optional): string representing source 
 * edittype (optional): occurrence, identification, comment
 * timestamp (optional): original timestamp of edit within external application 
 * key (optional): security key used to authorize. May be enforced later
 * 
 */

date_default_timezone_set('America/Phoenix');
include_once('../config/symbini.php');
require_once($SERVER_ROOT.'/classes/APIOccurEditor.php');

$occid = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;
$recordID = array_key_exists('recordid',$_REQUEST)?$_REQUEST['recordid']:'';
$dwcObj = (isset($_REQUEST['dwcobj'])?$_REQUEST['dwcobj']:'');
$editType = array_key_exists('edittype',$_REQUEST)?$_REQUEST['edittype']:'occurrence';
$source = array_key_exists('source',$_REQUEST)?$_REQUEST['source']:'';
$editor = array_key_exists('editor',$_REQUEST)?$_REQUEST['editor']:'';
$origTimestamp = array_key_exists('timestamp',$_REQUEST)?$_REQUEST['timestamp']:'';
$securityKey = array_key_exists('key',$_REQUEST)?$_REQUEST['key']:'';

//Sanitation 
if(!preg_match('/^[\d,]+$/', $occid)) $occid = 0;
$recordID = preg_replace("/[^A-Za-z0-9\-]/","",$recordID);
$securityKey = preg_replace("/[^A-Za-z0-9\-]/","",$securityKey);

$servManager = new APIOccurEditor();
if(!$occid && !$recordID)
	exit('{"Result":{"Failure":[{"Message":"Occurrence identifier is null"}]}}');
	
if(!$dwcObj)
	exit('{"Result":{"Failure":[{"Message":"dwcObj edit object is null"}]}}');

if(!$servManager->validateSecurityKey($securityKey))
	exit('{"Result":{"Failure":[{"Message":"Security key validation failed!"}]}}');
	
$servManager->setVerboseMode(1);
if($occid){
	$servManager->setOccid($occid);
}
elseif($recordID){
	if(!$servManager->setRecordID($recordID)){
		exit('{"Result":{"Failure":[{"Message":"RecordID not valid"}]}}');
	}
}
if($servManager->setDwcArr($dwcObj)){
	$servManager->setEditType($editType);
	$servManager->setSource($source);
	$servManager->setEditor($editor);
	$servManager->setOrigTimestamp($origTimestamp);
	
	echo $servManager->applyEdit();
}
else{
	echo '{"Result":{"Failure":[{"Message":"dwcObj failed to validate"}]}}';
}
?>