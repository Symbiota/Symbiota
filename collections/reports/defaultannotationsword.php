<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
require_once $SERVER_ROOT.'/vendor/phpoffice/phpword/bootstrap.php';

header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 180); //180 seconds = 3 minutes

$labelManager = new OccurrenceLabel();

$collid = $_POST["collid"];
$lHeader = $_POST['lheading'];
$lFooter = $_POST['lfooter'];
$detIdArr = $_POST['detid'];
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$columnsPerPage = array_key_exists('columncount',$_POST)?$_POST['columncount']:3;

$labelManager->setCollid($collid);

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"]))){
		$isEditor = 1;
	}
}

$labelArr = array();
if($isEditor && $action){
	$speciesAuthors = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
	$familyName = ((array_key_exists('print-family',$_POST) && $_POST['print-family'])?1:0);
	$labelArr = $labelManager->getAnnoArray($_POST['detid'], $speciesAuthors, $familyName);
	if(array_key_exists('clearqueue',$_POST) && $_POST['clearqueue']){
		$labelManager->clearAnnoQueue($_POST['detid']);
	}
}

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->addParagraphStyle('firstLine', array('lineHeight'=>.1,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('lastLine', array('spaceAfter'=>50,'lineHeight'=>.1));
$phpWord->addFontStyle('dividerFont', array('size'=>1));
$phpWord->addParagraphStyle('header', array('align'=>'center','lineHeight'=>1.0,'spaceAfter'=>40,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('footer', array('align'=>'center','lineHeight'=>1.0,'spaceBefore'=>40,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addFontStyle('headerfooterFont', array('bold'=>true,'size'=>9,'name'=>'Arial'));
$phpWord->addParagraphStyle('other', array('align'=>'left','lineHeight'=>1.0,'spaceBefore'=>30,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('scientificname', array('align'=>'left','lineHeight'=>1.0,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('noSpacing', [
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
    'lineHeight'  => 1.0,
    'keepNext'    => true,
    'keepLines'   => true,
	'alignment' => 'right'
]);
$phpWord->addFontStyle('scientificnameFont', array('bold'=>true,'italic'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameinterFont', array('bold'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameauthFont', array('size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('familyFont', array('size'=>8,'name'=>'Arial'));
$phpWord->addFontStyle('identifiedFont', array('size'=>8,'name'=>'Arial'));
$marginSize = 80;
if(array_key_exists('marginsize',$_POST) && $_POST['marginsize']) $marginSize = 16 * $_POST['marginsize'];
$borderWidth = 2;
$cellLength = 20000;

$sectionStyle = array();
if($columnsPerPage==1){
	$lineWidth = 740;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0);
}
if($columnsPerPage==2){
	$lineWidth = 350;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0,'colsNum'=>2,'colsSpace'=>180,'breakType'=>'continuous');
}
if($columnsPerPage==3){
	$lineWidth = 220;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0,'colsNum'=>3,'colsSpace'=>180,'breakType'=>'continuous');
}
$section = $phpWord->addSection($sectionStyle);

if(array_key_exists('borderwidth',$_POST)) $borderWidth = $_POST['borderwidth'];
if($borderWidth) $borderWidth++;
if($borderWidth){
	$tableStyle['borderColor'] = '000000';
	$tableStyle['borderSize'] = $borderWidth;
}

$outerStyle = [
  'borderColor' => '000000',
  'borderSize'  => $borderWidth,
  'borderInsideHSize' => 0,
  'borderInsideVSize' => 0,
];
$phpWord->addTableStyle('labelBox', $outerStyle);
	
$innerStyle = [
	'cellMargin'=>$marginSize,
	'borderSize' => 0,
	'borderColor' => 'ffffff',
	'borderInsideHSize' => 0,
	'borderInsideVSize' => 0,
];
$phpWord->addTableStyle('labelInner', $innerStyle);

$colRowStyle = array('cantSplit'=>true);
$cellStyle = array('valign'=>'top','halign' => 'left');

foreach($labelArr as $occid => $occArr){
	$headerStr = trim($lHeader);
	$footerStr = trim($lFooter);

	$dupCnt = $_POST['q-'.$occid];
	for($i = 0;$i < $dupCnt;$i++){
		$currentTxt = htmlspecialchars(' ');
		$section->addText($currentTxt, 'firstLine');

		$outer = $section->addTable('labelBox');
    	$outer->addRow();

    	$boxCell = $outer->addCell($cellLength);
		$table = $boxCell->addTable('labelInner');
		if($headerStr){
            $table->addRow();
            $cell = $table->addCell($cellLength,$cellStyle);
			$textrun = $cell->addTextRun('header');
			$currentTxt = htmlspecialchars($headerStr);
			$textrun->addText($currentTxt, 'headerfooterFont');
		}
        $table->addRow();
        $leftCell = $table->addCell(0.55 * $cellLength, $cellStyle);
		$rightCell = $table->addCell(0.45 * $cellLength, $cellStyle);

		$textrun = $leftCell->addTextRun('scientificname');
		if($occArr['identificationqualifier']){
			$currentTxt = htmlspecialchars($occArr['identificationqualifier']) . ' ';
			$textrun->addText($currentTxt, 'scientificnameauthFont');
		} 
		$scinameStr = $occArr['sciname'];
		$parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
		$queryArr = ['subsp.'=>'subsp.', 'sp.'=>'sp.' , 'ssp.'=>'ssp.', 'var.'=>'var.', 'variety'=>'var.', 'Variety'=>'var.', 'v.'=>'var.','f.'=>'f.', 'cf.'=>'cf.', 'aff.'=>'aff.'];
		$shouldStop = false;
		$shouldAddNextElList = ['subsp.', 'ssp.', 'var.', 'variety', 'Variety', 'v.', 'f.', 'cf.', 'aff.'];
		foreach($queryArr as $queryKey => $queryVal){
			OccurrenceLabel::processSciNameLabelForWord($scinameStr, $queryKey, $queryVal, $textrun, $parentAuthor, in_array($queryKey, $shouldAddNextElList), $shouldStop);
		}
		if(!$shouldStop){
			$currentTxt = htmlspecialchars($scinameStr) . ' ';
			$textrun->addText($currentTxt, 'scientificnameFont');
		}
		$scientificnameauthorshipStr = $occArr['scientificnameauthorship'];
		$familyRun = $rightCell->addTextRun('noSpacing');
		if($occArr['family']){
			$currentTxt = strtoupper(htmlspecialchars($occArr['family']));
			$familyRun->addText($currentTxt, 'scientificnameauthFont');
		}else{
			$familyRun->addText('', 'scientificnameauthFont');
		}
		$currentTxt = htmlspecialchars($scientificnameauthorshipStr);
		$textrun->addText($currentTxt, 'scientificnameauthFont');
		if($occArr['identifiedby'] || $occArr['dateidentified']){
			if($occArr['identifiedby']){
				$identByStr = $occArr['identifiedby'];
				if($occArr['dateidentified']){
					$textrun2 = $rightCell->addTextRun(['alignment' => 'right']);
					$textrun2->addText($occArr['dateidentified'], 'identifiedFont');
				}
				$currentTxt = 'Det: ' . htmlspecialchars($identByStr);
				$textrun->addText($currentTxt, 'identifiedFont');
			}
		}
		if(array_key_exists('printcatnum',$_POST) && $_POST['printcatnum'] && $occArr['catalognumber']){
			$textrun = $leftCell->addTextRun('other');
            $textrunRt = $rightCell->addTextRun('other');
			$currentTxt = 'Catalog #: ' . htmlspecialchars($occArr['catalognumber']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
		}
		if($occArr['identificationremarks']){
			$textrun = $leftCell->addTextRun('other');
            $textrunRt = $rightCell->addTextRun('other');
			$currentTxt = htmlspecialchars($occArr['identificationremarks']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
		}
		if($occArr['identificationreferences']){
			$textrun = $leftCell->addTextRun('other');
            $textrunRt = $rightCell->addTextRun('other');
			$currentTxt = htmlspecialchars($occArr['identificationreferences']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
		}
		if($footerStr){
			$table->addRow();
			$footerCell = $table->addCell($cellLength, ['gridSpan' => 2]);
			$textrun = $footerCell->addTextRun('footer');
			$currentTxt = htmlspecialchars($footerStr);
			$textrun->addText($currentTxt, 'headerfooterFont');
		}
		$currentTxt = htmlspecialchars(' ');
		$section->addText($currentTxt,'dividerFont', 'lastLine');
	}
}

$targetFile = $TEMP_DIR_ROOT . '/' . $PARAMS_ARR['un'] . '_annoLabel_' . date('Y-m-d') . '_' . time() . '.docx';
$phpWord->save($targetFile, 'Word2007');

ob_start();
ob_clean();
ob_end_flush();
header('Content-Description: File Transfer');
header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename='.basename($targetFile));
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($targetFile));
readfile($targetFile);
unlink($targetFile);
?>
