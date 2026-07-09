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
	$sql = "SELECT o.refid, o.secondarytitle, o.volume, o.title, o.edition ".
		"FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId ".
		"WHERE (o.title LIKE '%".$queryString."%' OR o.secondarytitle LIKE '%".$queryString."%') AND o.ReferenceTypeId <> 27 AND t.IsParent = 1 ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$titleLine = '';
		if($row->secondarytitle){
			$titleLine .= $row->secondarytitle.' ';
			if($row->volume){
				$titleLine .= 'Vol. '.$row->volume.' ';
			}
		}
		if($row->title != $row->secondarytitle){
			$titleLine .= $row->title;
		}
		if(!$row->secondarytitle && $row->volume){
			$titleLine .= ' Vol. '.$row->volume.' ';
		}
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