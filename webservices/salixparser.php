<?php
/*
 * INPUT
 * 		ocrinput [required] - 
 *  	returnformat [optional] - json, xml, html
 *  	charset output [not yet supported] - utf-8, iso-8859-1
 * 
 * OUTPUT
 * 		Success: string representing parsed data with Darwin Core terms as field keys 
 * 		Fail: string with error
 */

//error_reporting(E_ALL);
error_reporting(0);
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SalixHandler.php');
header("Content-Type: text/html; charset=UTF-8");

$debug = 1;

$ocrInput = $_REQUEST['ocrinput'];
$returnFormat = array_key_exists('returnformat',$_REQUEST)?$_REQUEST['returnformat']:'';
//$charset = array_key_exists('charset',$_REQUEST)?$_REQUEST['charset']:'';

$salixHandler = new SalixHandler();

if($returnFormat) $salixHandler->setReturnFormat($returnFormat);
//if($charset) $salixHandler->setCharset($charset);
if($debug) $salixHandler->setVerbose(1);

$parseData = $salixHandler->parse($ocrInput);

if($parseData){
	echo $parseData;
}
else{
	echo $salixHandler->getErrorStr;
}
?>