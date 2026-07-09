<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecProcDuplicates.php');

$verbose = array_key_exists("verbose",$_REQUEST)?$_REQUEST["verbose"]:1;

$nlpHandler = new SpecProcDuplicates();
$nlpHandler->batchBuildFragments();

?>