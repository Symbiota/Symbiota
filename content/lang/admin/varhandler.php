<?php
$path = array_key_exists('path',$_REQUEST)?$_REQUEST['path']:'';
if(file_exists($path)){
	include_once($path);
	if(isset($LANG)) echo json_encode($LANG);
}
?>