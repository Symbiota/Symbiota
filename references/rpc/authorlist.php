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
	$sql = "SELECT DISTINCT refauthorid, CONCAT_WS(' ',firstname,middlename,lastname) AS authorName ".
		"FROM referenceauthors ".
		"WHERE lastname LIKE '".$queryString."%' ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$retArrRow['label'] = htmlentities($row->authorName, ENT_COMPAT, $CHARSET);
		$retArrRow['value'] = $row->refauthorid;
		array_push($returnArr, $retArrRow);
	 }
}
$con->close();
echo json_encode($returnArr);
?>