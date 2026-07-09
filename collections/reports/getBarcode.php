<?php
error_reporting(0);
include_once('../../config/symbini.php');
@include_once("Image/Barcode.php");
@include_once("Image/Barcode2.php");
header("Content-type: image/png");
$bcText = array_key_exists('bctext',$_REQUEST)?$_REQUEST["bctext"]:'';
$bcCode = array_key_exists('bccode',$_REQUEST)?$_REQUEST['bccode']:'Code39';
$imgType = array_key_exists('imgtype',$_REQUEST)?$_REQUEST['imgtype']:'png';
$bcHeight = array_key_exists('bcheight',$_REQUEST)?$_REQUEST['bcheight']:50;

if($bcText){
	if(class_exists('Image_Barcode2')){
		$bcText = strtoupper($bcText);
		$bcObj = new Image_Barcode2;
		$bc = $bcObj->draw($bcText, "Code39", "png", false, $bcHeight);
		imagepng($bc);
		imagedestroy($bc);
	}
	elseif(class_exists('Image_Barcode')){
		$bcText = strtoupper($bcText);
		$bcObj = new Image_Barcode;
		$bc = $bcObj->draw($bcText, "Code39", "png");
		imagepng($bc);
		imagedestroy($bc);
	}
}
?>