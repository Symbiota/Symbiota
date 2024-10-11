<?php
//error_reporting(E_ALL);
//error_reporting(0);
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/SpecProcNlpUtil.php');
include_once($SERVER_ROOT . '/classes/SpecProcNlpSalix.php');
header("Content-Type: text/html; charset=".$CHARSET);

$rawOcr = $_REQUEST['rawocr'];
$debug = 0;

$nlpManager = new SpecProcNlpSalix();
$dwcArr = array();
if($rawOcr){
	//Get rid of UTF-8 curly smart quotes and dashes
	$badwordchars=array("\xe2\x80\x98", // left single quote
						"\xe2\x80\x99", // right single quote
						"\xe2\x80\x9c", // left double quote
						"\xe2\x80\x9d", // right double quote
						"\xe2\x80\x94", // em dash
						"\xe2\x80\xa6" // elipses
	);
	$fixedwordchars=array("'", "'", '"', '"', '-', '...');
	$rawOcr = str_replace($badwordchars, $fixedwordchars, $rawOcr);

	$dwcArr = $nlpManager->parse($rawOcr);
	if($debug){
		$fh = fopen($SERVER_ROOT.'/content/logs/ocrdebug.txt','w');
		fwrite($fh,'Raw OCR:');
		fwrite($fh,$rawOcr);
		fwrite($fh,"\n\n\n------------------------------------------------------------------\n\n\n");
		fwrite($fh,'Parsed data:');
		foreach($dwcArr as $k => $v){
			fwrite($fh,$k.': '.$v."\n");
		}
		fclose($fh);
	}
	$dwcArr = SpecProcNlpUtil::cleanDwcArr($dwcArr);
}
echo json_encode($dwcArr);
?>