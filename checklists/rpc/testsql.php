<?php
include_once($SERVER_ROOT . '/config/symbini.php');
include_once($SERVER_ROOT.'/config/dbconnection.php');
header("Content-Type: text/html; charset=".$CHARSET);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$con = MySQLiConnectionFactory::getCon('readonly');
//get the q parameter from URL
$sqlFrag = $con->real_escape_string($_REQUEST['sql']); 
$clid = $con->real_escape_string($_REQUEST['clid']); 

$responseStr = '-1';
if($sqlFrag && $clid && $IS_ADMIN || (array_key_exists("ClAdmin",$USER_RIGHTS) && in_array($clid,$USER_RIGHTS["ClAdmin"]))){
	$responseStr = '0';
	$sql = "SELECT * FROM omoccurrences o WHERE ".$sqlFrag." limit 1";
	$result = $con->query($sql);
	if($result){
		$responseStr = "1";
	}
	if($result) $result->close();
}
if(!($con === false)) $con->close();
echo $responseStr;
?>