<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$commname = array_key_exists('name',$_REQUEST)?$_REQUEST['name']:'';
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoManager = new EthnoProjectManager();
$listArr = Array();

if($commname){
    $listArr = $ethnoManager->getCommNameList($commname,$collid);
    echo json_encode($listArr);
}
?>
