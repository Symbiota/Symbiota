<?php
include_once('../../config/symbini.php');
require_once $SERVER_ROOT.'/vendor/phpoffice/phpword/bootstrap.php';

$targetHtml = $_POST['targetHtml'];
$pw = new \PhpOffice\PhpWord\PhpWord();
$section = $pw->addSection();

\PhpOffice\PhpWord\Shared\Html::addHtml($section, $targetHtml);

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
