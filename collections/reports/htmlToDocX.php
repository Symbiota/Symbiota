<?php

include_once('../../config/symbini.php');
require_once $SERVER_ROOT.'/vendor/phpoffice/phpword/bootstrap.php';
$htmlLabels = $_POST['htmlLabels'] ?? [];

$columnCount = $_POST['labeltype'] ?? 2;
if(!is_numeric($columnCount) && $columnCount != 'packet') $columnCount = 2;

$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0);
if($columnCount == 1){
	$lineWidth = 740;
}
elseif($columnCount == 2){
	$lineWidth = 350;
	$sectionStyle['colsNum'] = 2;
	$sectionStyle['colsSpace'] = 690;
	$sectionStyle['breakType'] = 'continuous';
}
elseif($columnCount == 3){
	$lineWidth = 220;
	$sectionStyle['colsNum'] = 3;
	$sectionStyle['colsSpace'] = 690;
	$sectionStyle['breakType'] = 'continuous';
}

// This required or &amp; will corrupt the docx file
 \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

$pw = new \PhpOffice\PhpWord\PhpWord();

$pw->addFontStyle('dividerFont', array('size'=>1));
$pw->addParagraphStyle('lastLine', array('spaceAfter'=>300,'lineHeight'=>.1, 'keepNext' => false, 'keepLines' => false));

$section = $pw->addSection($sectionStyle);

//echo $htmlLabels[0];
//return;
foreach($htmlLabels as $label) {
	\PhpOffice\PhpWord\Shared\Html::addHtml($section, $label);	
	$section->addText(' ', 'dividerFont', 'lastLine');
}

foreach($section->getElements() as $element) {
	try {
		$style = $element->getParagraphStyle();
		if($style != 'lastLine') {
			$style->setKeepNext(true);
			$style->setKeepLines(true);
		}
	} finally {}
}

$ses_id = time();
$target = $SERVER_ROOT.'/temp/report/'.'labels'.'_'.date('Ymd').'_labels_'.$ses_id.'.docx';

$pw->save($target, 'Word2007');

header('Content-Description: File Transfer');
header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename='.basename($target));
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($target));
readfile($target);

$files = glob($SERVER_ROOT.'/temp/report/*');
foreach($files as $file){
	if(is_file($file)){
		if(strpos($file,$ses_id) !== false){
			unlink($file);
		}
	}
}
