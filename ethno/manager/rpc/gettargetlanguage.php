<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$perId = array_key_exists('perid',$_REQUEST)?$_REQUEST['perid']:0;
$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoDataManager = new EthnoDataManager();
$result = 0;

if($perId && $collId){
    $result = $ethnoDataManager->getPersonnelTargetLanguage($perId,$collId);
}
echo $result;
?>
