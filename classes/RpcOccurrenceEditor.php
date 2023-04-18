<?php
include_once($SERVER_ROOT.'/classes/RpcBase.php');

class RpcOccurrenceEditor extends RpcBase{

	function __construct($connType = 'readonly'){
		parent::__construct(null,$connType);
	}

	function __destruct(){
		parent::__destruct();
	}

	public function deleteIdentifier($identifierID, $occid){
		$bool = false;
		if(is_numeric($identifierID)){
			$origOcnStr = '';
			$sql = 'SELECT CONCAT_WS(": ",identifierName,identifierValue) as identifier FROM omoccuridentifiers WHERE (idomoccuridentifiers = '.$identifierID.') ORDER BY sortBy ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$origOcnStr = $r->identifier;
			}
			$rs->free();
			$sql = 'DELETE FROM omoccuridentifiers WHERE idomoccuridentifiers = '.$identifierID;
			if($this->conn->query($sql)){
				$bool = true;
				if($origOcnStr){
					$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueNew, fieldValueOld, appliedStatus, uid)
						VALUES('.$occid.',"omoccuridentifiers","","'.$this->cleanInStr($origOcnStr).'",1,'.$GLOBALS['SYMB_UID'].')';
					$this->conn->query($sql);
				}
			}
			else $this->errorMessage = 'ERROR deleting occurrence identifier: '.$this->conn->error;
		}
		elseif(is_numeric($occid)){
			if(strpos($identifierID,'ocnid-') === 0){
				$ocnIndex = substr($identifierID,6);
				$origOcnStr = '';
				$sql = 'SELECT otherCatalogNumbers FROM omoccurrences WHERE occid = '.$occid;
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()) $origOcnStr = $r->otherCatalogNumbers;
				$rs->free();
				$ocnStr = trim($origOcnStr,',;| ');
				$otherCatNumArr = array();
				if($ocnStr){
					$ocnStr = str_replace(array(',',';'),'|',$ocnStr);
					$ocnArr = explode('|',$ocnStr);
					$cnt = 0;
					foreach($ocnArr as $identUnit){
						if($ocnIndex == $cnt) continue;
						$unitArr = explode(':',trim($identUnit,': '));
						$tag = '';
						if(count($unitArr) > 1) $tag = trim(array_shift($unitArr));
						$value = trim(implode(', ',$unitArr));
						$otherCatNumArr[$value] = $tag;
						$cnt++;
					}
				}
				$newOcnStr = '';
				foreach($otherCatNumArr as $v => $t){
					$newOcnStr .= ($t?$t.': ':'').$v.'; ';
				}
				$newOcnStr = trim($newOcnStr,'; ');
				if($newOcnStr != $origOcnStr){
					$sql = 'UPDATE omoccurrences SET otherCatalogNumbers = '.($newOcnStr?'"'.$this->cleanInStr($newOcnStr).'"':'NULL').' WHERE occid = '.$occid;
					if($this->conn->query($sql)){
						$bool = true;
						$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueNew, fieldValueOld, appliedStatus, uid)
							VALUES('.$occid.',"omoccuridentifiers","'.$this->cleanInStr($newOcnStr).'","'.$this->cleanInStr($origOcnStr).'",1,'.$GLOBALS['SYMB_UID'].')';
						$this->conn->query($sql);
					}
					else echo 'ERROR deleting occurrence identifier: '.$this->conn->error;
				}
			}
		}
		return $bool;
	}

	public function getDupesCatalogNumber($catNum, $collid, $skipOccid){
		$retArr = array();
		$catNumber = $this->cleanInStr($catNum);
		if(is_numeric($collid) && is_numeric($skipOccid) && $catNumber){
			$sql = 'SELECT occid FROM omoccurrences WHERE (catalognumber = ?) AND (collid = ?) AND (occid != ?) ';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('sii', $catNum, $collid, $skipOccid);
				$stmt->execute();
				$occid = 0;
				$stmt->bind_result($occid);
				while($stmt->fetch()){
					$retArr[$occid] = $occid;
				}
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function getDupesOtherCatalogNumbers($otherCatNum, $collid, $skipOccid){
		$retArr = array();
		if(is_numeric($collid) && is_numeric($skipOccid) && $otherCatNum){
			$sql = 'SELECT o.occid FROM omoccurrences o LEFT JOIN omoccuridentifiers i ON o.occid = i.occid
				WHERE (o.othercatalognumbers = ? OR i.identifierValue = ?) AND (o.collid = ?) AND (o.occid != ?) ';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('ssii', $otherCatNum, $otherCatNum, $collid, $skipOccid);
				$stmt->execute();
				$occid = 0;
				$stmt->bind_result($occid);
				while($stmt->fetch()){
					$retArr[$occid] = $occid;
				}
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function getOccurrenceVouchers($occid){
		$retArr = array();
		if(is_numeric($occid)){
			$sql = 'SELECT c.clid, c.name FROM fmvouchers v INNER JOIN fmchklsttaxalink cl ON v.clTaxaID = cl.clTaxaID INNER JOIN fmchecklists c ON cl.clid = c.clid WHERE v.occid = ?';
			if($stmt = $this->conn->prepare($sql)) {
				if($stmt->bind_param('i', $occid)){
					$stmt->execute();
					$clid = '';
					$name = '';
					$stmt->bind_result($clid, $name);
					while($stmt->fetch()){
						$retArr[$clid] = $name;
					}
					$stmt->close();
				}
				else $this->errorMessage = 'ERROR binding params for getOccurrenceVouchers: '.$stmt->error;
			}
			else $this->errorMessage = 'ERROR preparing statement for getOccurrenceVouchers: '.$this->conn->error;
		}
		return $retArr;
	}

	//Setters and getters
	public function isValidApiCall(){
		//Verification also happening within haddler checking is user is logged in and a valid admin/editor
		$status = parent::isValidApiCall();
		if(!$status) return false;
		return true;
	}
}
?>