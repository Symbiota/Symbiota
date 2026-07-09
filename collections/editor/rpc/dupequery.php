<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDuplicate.php');

$collName = array_key_exists('cname',$_POST)?trim($_POST['cname']):'';
$collNum = array_key_exists('cnum',$_POST)?trim($_POST['cnum']):'';
$collDate = array_key_exists('cdate',$_POST)?trim($_POST['cdate']):'';
$ometid = array_key_exists('ometid',$_POST)?trim($_POST['ometid']):'';
$exsNumber = array_key_exists('exsnumber',$_POST)?trim($_POST['exsnumber']):'';
$currentOccid = array_key_exists('curoccid',$_POST)?trim($_POST['curoccid']):0;

$dupeManager = new OccurrenceDuplicate();
$retStr = $dupeManager->getDupes($collName, $collNum, $collDate, $ometid, $exsNumber, $currentOccid);
echo $retStr;
?>