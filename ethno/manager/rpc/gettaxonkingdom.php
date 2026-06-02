<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;

$ethnoDataManager = new EthnoDataManager();
$result = 0;

if($tid){
    $ethnoDataManager->setTid($tid);
    $ethnoDataManager->setTaxonomy();
    $result = $ethnoDataManager->getKingdomId();
}
echo $result;
?>
