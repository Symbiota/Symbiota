<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$occid = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;

$ethnoDataManager = new EthnoDataManager();
$result = Array();

if($occid){
    $result = $ethnoDataManager->getOccTaxaArr($occid);
}
echo json_encode($result);
?>
