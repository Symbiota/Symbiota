<?php
	include_once('../../../config/symbini.php');
	include_once($SERVER_ROOT.'/config/dbconnection.php');
	header("Content-Type: text/html; charset=".$CHARSET);
	$retArr = Array();
	$con = MySQLiConnectionFactory::getCon("readonly");
	$queryString = $con->real_escape_string($_REQUEST['term']);
	if($queryString){
		$sql = 'SELECT tid, sciname '. 
			'FROM taxa '.
			'WHERE rankid < 220 AND sciname LIKE "'.$queryString.'%" ';
		//echo $sql;
		$result = $con->query($sql);
		while ($row = $result->fetch_object()) {
			array_push($retArr,array('id'=>$row->tid,'value'=>$row->sciname));
		}
		$result->free();
	}
	$con->close();
	echo(json_encode($retArr));
?>