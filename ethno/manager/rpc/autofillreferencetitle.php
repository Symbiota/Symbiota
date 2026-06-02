<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$reftitle = array_key_exists('title',$_REQUEST)?$_REQUEST['title']:'';

$ethnoManager = new EthnoProjectManager();
$listArr = Array();

if($reftitle){
    $listArr = $ethnoManager->getRefTitleList($reftitle);
    echo json_encode($listArr);
}
?>
