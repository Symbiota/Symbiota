<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/NpsReport.php');

if($SYMB_UID){
	$dsid = array_key_exists('dsid',$_REQUEST)?$_REQUEST['dsid']:'';
	$year = array_key_exists('year',$_REQUEST)?$_REQUEST['year']:'';
	if($dsid){
		$reporthandler = new NpsReport();
		$reporthandler->setDatasetID($dsid);
		$reporthandler->setTargetYear($year);
		$reporthandler->generateNpsReport();
		exit;
	}
}
?>