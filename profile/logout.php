<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OpenIdProfileManager.php');

$profManager = new OpenIdProfileManager();

$sid = array_key_exists('sid', $_REQUEST) ? htmlspecialchars($_REQUEST['sid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$localSessionID = $profManager->lookupLocalSessionIDWithThirdPartySid($sid);

if($localSessionID){
	$profManager->forceLogout($localSessionID);
	header("HTTP/",true,200);
}
else{
	header("HTTP/",true,400);
}

?>