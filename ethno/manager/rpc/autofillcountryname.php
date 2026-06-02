<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$countryname = array_key_exists('name',$_REQUEST)?$_REQUEST['name']:'';

$ethnoManager = new EthnoProjectManager();
$listArr = Array();

if($countryname){
    $listArr = $ethnoManager->getCountryNameList($countryname);
    echo json_encode($listArr);
}
?>
