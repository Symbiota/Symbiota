<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$refauthor = array_key_exists('author',$_REQUEST)?$_REQUEST['author']:'';

$ethnoManager = new EthnoProjectManager();
$listArr = Array();

if($refauthor){
    $listArr = $ethnoManager->getRefAuthorList($refauthor);
    echo json_encode($listArr);
}
?>
