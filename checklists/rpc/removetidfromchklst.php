<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/config/dbconnection.php');
header("Content-Type: text/html; charset=".$CHARSET);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$clid = $_REQUEST["clid"];
$tid = $_REQUEST["tid"];

if(is_numeric($clid) && is_numeric($tid)){
	if($IS_ADMIN || (array_key_exists('ClAdmin',$USER_RIGHTS) && in_array($clid,$USER_RIGHTS['ClAdmin']))){
		$conn = MySQLiConnectionFactory::getCon("write");
		$tid = $conn->real_escape_string($tid);
		$clid = $conn->real_escape_string($clid);
		$sql = 'DELETE FROM fmchklsttaxalink WHERE chklsttaxalink.CLID = '.$clid.' AND chklsttaxalink.TID = '.$tid;
		//echo $sql;
		if($conn->query($sql)){
			echo $tid;
		}
		else{
			echo '0';
		}
	}
}
$conn->close();
?>
