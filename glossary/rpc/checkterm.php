<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/GlossaryManager.php');

$term = $_POST['term'];
$language = $_POST['language'];
$relGlossId = $_POST['relglossid'];
$tid = $_POST['tid'];

$isEditor = false;
if($IS_ADMIN || array_key_exists('GlossaryEditor',$USER_RIGHTS)) $isEditor = true;

$retStr = '';
if($isEditor){
	$glosManager = new GlossaryManager();
	if($glosManager->checkTerm($term, $language, $relGlossId, $tid)) $retStr = 1;
}

echo $retStr;
?>