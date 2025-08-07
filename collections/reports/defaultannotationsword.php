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
$phpWord->addFontStyle('scientificnameFont', array('bold'=>true,'italic'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameinterFont', array('bold'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameauthFont', array('size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('familyFont', array('size'=>8,'name'=>'Arial'));
$phpWord->addFontStyle('identifiedFont', array('size'=>8,'name'=>'Arial'));
$marginSize = 80;
if(array_key_exists('marginsize',$_POST) && $_POST['marginsize']) $marginSize = 16*$_POST['marginsize'];
$borderWidth = 2;
$outerStyle = [
  'borderColor' => '000000',
  'borderSize'  => $borderWidth,
  'borderInsideHSize' => 0,
  'borderInsideVSize' => 0,
];
$section = $phpWord->addSection($sectionStyle);
$phpWord->addTableStyle('labelBox', $outerStyle);
// $outer = $section->addTable('labelBox');
// $outer->addRow();
// $boxCell = $outer->addCell($cellLength);

$innerStyle = [
  'borderSize' => 0,
  'borderColor' => 'ffffff',
  'borderInsideHSize' => 0,
  'borderInsideVSize' => 0,
];
$phpWord->addTableStyle('labelInner', $innerStyle);

// $tableStyle = array('width'=>100,
// 	'cellMargin'=>$marginSize,
// 	'borderColor' => '000000',
// 	'borderSize' => $borderWidth,
// 	'borderInsideHSize' => 0,
// 	'borderInsideHSize' => 0,
// 	'borderInsideVSize' => 0,
// 	'borderInsideHColor' => '000000',
// 	'borderInsideVColor' => '000000',
// );
if(array_key_exists('borderwidth',$_POST)) $borderWidth = $_POST['borderwidth'];
if($borderWidth) $borderWidth++;
if($borderWidth){
	$tableStyle['borderColor'] = '000000';
	$tableStyle['borderSize'] = $borderWidth;
}
$colRowStyle = array('cantSplit'=>true);
// $phpWord->addTableStyle('defaultTable',$tableStyle,$colRowStyle);
$cellStyle = array('valign'=>'top',
	'halign' => 'left')
	;



foreach($labelArr as $occid => $occArr){
	$headerStr = trim($lHeader);
	$footerStr = trim($lFooter);

	$dupCnt = $_POST['q-'.$occid];
	for($i = 0;$i < $dupCnt;$i++){
        $charCount = 0;
		$currentTxt = htmlspecialchars(' ');
		$section->addText($currentTxt, 'firstLine');
		$charCount += strlen($currentTxt);

		$outer = $section->addTable('labelBox');       // <-- uses $section
    	$outer->addRow();
    	$boxCell = $outer->addCell(12000);
		// $table->addRow();
		// $table->addRow(null, ['tblHeader' => false, 'exactHeight' => false]);
		// $cell = $table->addCell(5000,$cellStyle);
		$table = $boxCell->addTable('labelInner');
		$table->addRow();
        $cellLength = 12000;
		$averageTwipsPerCharacter = 400; //240;
		$leftCell = $table->addCell(8000, $cellStyle);
		$rightCell = $table->addCell(4000, $cellStyle);
		// $cell = $table->addCell($cellLength,$cellStyle);
		if($headerStr){
			// $textrun = $cell->addTextRun('header');
			$textrun = $leftCell->addTextRun('header');

			$currentTxt = htmlspecialchars($headerStr);
			$textrun->addText($currentTxt, 'headerfooterFont');
			$charCount += strlen($currentTxt);
		}
		// $textrun = $cell->addTextRun('scientificname');
		$textrun = $leftCell->addTextRun('scientificname');
		if($occArr['identificationqualifier']){
			$currentTxt = htmlspecialchars($occArr['identificationqualifier']) . ' ';
			$textrun->addText($currentTxt, 'scientificnameauthFont');
			$charCount += strlen($currentTxt);
		} 
		$scinameStr = $occArr['sciname'];
		$parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
		$queryArr = ['subsp.'=>'subsp.', 'sp.'=>'sp.' , 'ssp.'=>'ssp.', 'var.'=>'var.', 'variety'=>'var.', 'Variety'=>'var.', 'v.'=>'var.','f.'=>'f.', 'cf.'=>'cf.', 'aff.'=>'aff.'];
		$shouldStop = false;
		$shouldAddNextElList = ['subsp.', 'ssp.', 'var.', 'variety', 'Variety', 'v.', 'f.', 'cf.', 'aff.'];
		foreach($queryArr as $queryKey => $queryVal){
			OccurrenceLabel::processSciNameLabelForWord($scinameStr, $queryKey, $queryVal, $textrun, $charCount, $parentAuthor, in_array($queryKey, $shouldAddNextElList), $shouldStop);
		}
		if(!$shouldStop){
			$currentTxt = htmlspecialchars($scinameStr) . ' ';
			$textrun->addText($currentTxt, 'scientificnameFont');
			$charCount += strlen($currentTxt);
		}
		$scientificnameauthorshipStr = $occArr['scientificnameauthorship'];
		if($occArr['family']){
			// $charCount = 38; // @TODO remove and troubleshoot upstream
			$scientificnameauthorshipStrChars = $scientificnameauthorshipStr . $occArr['family'];
			$totalCharactersInTopLine = $charCount + strlen($scientificnameauthorshipStrChars);
            $remainingWhiteSpace = floor(($cellLength - ($totalCharactersInTopLine * $averageTwipsPerCharacter))/ $averageTwipsPerCharacter);
            $asManySpacesAsNecessary = str_repeat(' ', max($remainingWhiteSpace - 1, 1));
			// $scientificnameauthorshipStr .= $asManySpacesAsNecessary . $occArr['family'];
			// $scientificnameauthorshipStr .= ' ' . strtoupper($occArr['family']);

			$familyRun = $rightCell->addTextRun(['alignment' => 'right']);
			$currentTxt = strtoupper(htmlspecialchars($occArr['family']));
			$familyRun->addText($currentTxt, 'scientificnameauthFont');
			// $currentTxt = strtoupper(htmlspecialchars($occArr['family']));
			// $familyRun = $cell->addTextRun(['alignment' => 'right']);
			// $currentTxt = strtoupper(htmlspecialchars($occArr['family']));
		}
		$currentTxt = htmlspecialchars($scientificnameauthorshipStr);
		$textrun->addText($currentTxt, 'scientificnameauthFont');
		// $familyRun->addText($currentTxt, 'scientificnameauthFont');
        // @TODO can stop here?
		// $charCount += strlen($currentTxt);
		if($occArr['identifiedby'] || $occArr['dateidentified']){
			// $textrun = $cell->addTextRun('other');
			$textrun = $leftCell->addTextRun('other');
			if($occArr['identifiedby']){
				$identByStr = $occArr['identifiedby'];
				if($occArr['dateidentified']){
					$textrun2 = $rightCell->addTextRun('other');
					$textrun2->addText($occArr['dateidentified'], 'identifiedFont');
					// $identByStr .= '      '.$occArr['dateidentified'];
				}
				$currentTxt = 'Det: ' . htmlspecialchars($identByStr);
				$textrun->addText($currentTxt, 'identifiedFont');
				// $charCount += strlen($currentTxt);
			}
		}
		if(array_key_exists('printcatnum',$_POST) && $_POST['printcatnum'] && $occArr['catalognumber']){
			// $textrun = $cell->addTextRun('other');
			$textrun = $leftCell->addTextRun('other');
			$currentTxt = 'Catalog #: ' . htmlspecialchars($occArr['catalognumber']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
			// $charCount += strlen($currentTxt);
		}
		if($occArr['identificationremarks']){
			// $textrun = $cell->addTextRun('other');
			$textrun = $leftCell->addTextRun('other');
			$currentTxt = htmlspecialchars($occArr['identificationremarks']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
			// $charCount += strlen($currentTxt);
		}
		if($occArr['identificationreferences']){
			// $textrun = $cell->addTextRun('other');
			$textrun = $leftCell->addTextRun('other');
			$currentTxt = htmlspecialchars($occArr['identificationreferences']).' ';
			$textrun->addText($currentTxt, 'identifiedFont');
			// $charCount += strlen($currentTxt);
		}
		if($footerStr){
			// $textrun = $leftCell->addTextRun('footer');
			$table->addRow();
			// $footerCell = $table->addCell(
			// 	$cellLength, 
			// 	array_merge($cellStyle, ['gridSpan' => 2])
			// );

			$footerCell = $table->addCell($cellLength, ['gridSpan' => 2]);
			// $cell = $table->addCell($cellLength,$cellStyle);
			$textrun = $footerCell->addTextRun('footer');
			// // $textrun = $leftCell->addTextRun('footer');
			// $table->addRow(null, ['tblHeader' => false, 'exactHeight' => false]);
			// $cell = $table->addCell(5000,$cellStyle);
			// $textrun = $cell->addTextRun('footer');
			$currentTxt = htmlspecialchars($footerStr);
			$textrun->addText($currentTxt, 'headerfooterFont');
			// $charCount += strlen($currentTxt);
		}
		$currentTxt = htmlspecialchars(' ');
		$section->addText($currentTxt,'dividerFont', 'lastLine');
		// $charCount += strlen($currentTxt);
	}
}

$targetFile = $SERVER_ROOT.'/temp/report/'.$PARAMS_ARR['un'].'_annoLabel_'.date('Y-m-d').'_'.time().'.docx';
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
