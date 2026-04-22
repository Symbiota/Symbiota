<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/PortalIndex.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$portalID = filter_var($_REQUEST['portalID'], FILTER_SANITIZE_NUMBER_INT);
$remoteUrl = filter_var($_REQUEST['remoteUrl'], FILTER_SANITIZE_URL);

$status = false;
if($portalID && $remoteUrl){
	$portalManager = new PortalIndex();
	$status = $portalManager->updateInstallation($portalID, $remoteUrl);
}
echo ($status ? 1 : 0);
?>