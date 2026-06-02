<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$pertitle = array_key_exists('title',$_REQUEST)?$_REQUEST['title']:'';
$perfn = array_key_exists('fn',$_REQUEST)?$_REQUEST['fn']:'';
$perln = array_key_exists('ln',$_REQUEST)?$_REQUEST['ln']:'';
$perid = array_key_exists('id',$_REQUEST)?$_REQUEST['id']:'';

$ethnoManager = new EthnoProjectManager();
$result = false;

if($perfn){
    $result = $ethnoManager->checkPerName($pertitle,$perfn,$perln,$perid);
}
echo $result;
?>
