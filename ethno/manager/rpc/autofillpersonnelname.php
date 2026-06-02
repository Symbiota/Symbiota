<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$firstname = array_key_exists('perfn',$_REQUEST)?$_REQUEST['perfn']:'';
$lastname = array_key_exists('perln',$_REQUEST)?$_REQUEST['perln']:'';

$ethnoManager = new EthnoProjectManager();
$listArr = Array();

if($firstname || $lastname){
    $listArr = $ethnoManager->getPersonnelNameList($firstname,$lastname);
    echo json_encode($listArr);
}
?>
