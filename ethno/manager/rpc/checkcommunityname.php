<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$commname = array_key_exists('name',$_REQUEST)?$_REQUEST['name']:'';
$commid = array_key_exists('id',$_REQUEST)?$_REQUEST['id']:'';

$ethnoManager = new EthnoProjectManager();
$result = false;

if($commname){
    $result = $ethnoManager->checkCommName($commname,$commid);
}
echo $result;
?>
