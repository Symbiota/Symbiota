<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/config/dbconnection.php');

$con = MySQLiConnectionFactory::getCon("readonly");
$returnArr = Array();
$retArrRow = Array();
$queryString = $con->real_escape_string($_REQUEST['term']);
// Is the string length greater than 0?
if($queryString) {
	$sql = "";
	$sql = "SELECT o.refid, o.title, o.edition ".
		"FROM referenceobject AS o  ".
		"WHERE o.title LIKE '%".$queryString."%' AND o.ReferenceTypeId = 27 ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$titleLine = '';
		$titleLine .= $row->title;
		if($row->edition){
			$titleLine .= ' '.$row->edition.' Ed.';
		}
		$retArrRow['label'] = htmlentities($titleLine, ENT_COMPAT, $CHARSET);
		$retArrRow['value'] = $row->refid;
		array_push($returnArr, $retArrRow);
	}
}
$con->close();
echo json_encode($returnArr);
?>