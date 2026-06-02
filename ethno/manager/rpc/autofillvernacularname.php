<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$name = array_key_exists('name',$_REQUEST)?$_REQUEST['name']:'';

$ethnoDataManager = new EthnoDataManager();
$listArr = Array();

if($name){
    $listArr = $ethnoDataManager->getVernacularNameList($name);
}
echo json_encode($listArr);
?>
