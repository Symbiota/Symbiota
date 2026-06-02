<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$occid = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;

$ethnoDataManager = new EthnoDataManager();
$result = 0;

if($occid){
    $ethnoDataManager->setOccId($occid);
    $ethnoDataManager->setOccTaxonomy();
    $result = $ethnoDataManager->getKingdomId();
}
echo $result;
?>
