<?php
//This file can be triggered by a CRON job for automatci OCR of unprocessed images
//Following example OCR collection ids 1,4, and 5. Script will also out to log file
//php ocr_handler.php '1,4,5' 0
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecProcessorOcr.php');

$silent = 1;
$collStr = '';
if(array_key_exists(1,$argv)){
	$collStr = $argv[1];
}
if(array_key_exists(2,$argv)){
	$silect = $argv[2];
}

$ocrManager = new SpecProcessorOcr();
$ocrManager->setVerboseMode($silent);
$ocrManager->batchOcrUnprocessed($collStr);
?>