<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class PortalProperties {

	/*
	 * INPUT: 	propname (required): string
	 * 			tableName (optional): string (e.g. omcollections, users)
	 *        	tabelPK (optional): int
	 * OUTPUT: 	raw values: string
	 */
	public static function getProperty($propName, $tableName = null, $tablePK = null, $conn = null){
		$returnValue = null;
		if($propName){
			$closeConnection = false;
			if(!$conn){
				$closeConnection = true;
				$conn = MySQLiConnectionFactory::getCon('readonly');
			}
			//Get dynamicProperties from omcollections
			$sql = 'SELECT propValue FROM adminproperties WHERE propName = ? ';
			if($tableName && $tablePK) $sql .= 'AND tableName = ? AND tablePK = ?';
			if($stmt = $conn->prepare($sql)){
				if($tableName && $tablePK) $stmt->bind_param('ssi', $propName, $tableName, $tablePK);
				else $stmt->bind_param('ssi', $propName);
				$stmt->execute();
				$stmt->bind_result($returnValue);
				$stmt->get_result($returnValue);
				$stmt->fetch();
				$stmt->close();
			}
			if($closeConnection){
				if($conn !== false) $conn->close();
			}
		}
		return $returnValue;
	}
}
?>