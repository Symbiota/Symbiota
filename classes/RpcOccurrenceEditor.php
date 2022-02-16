<?php
include_once($SERVER_ROOT.'/classes/RpcBase.php');

class RpcOccurrenceEditor extends RpcBase{

	function __construct(){
		parent::__construct('write');
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
					$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueNew, fieldValueOld, uid) VALUES('.$occid.',"othercatalognumbers","","'.$this->cleanInStr($origOcnStr).'",'.$GLOBALS['SYMB_UID'].')';
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
						$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueNew, fieldValueOld, uid) VALUES('.$occid.',"othercatalognumbers","'.$this->cleanInStr($newOcnStr).'","'.$this->cleanInStr($origOcnStr).'",'.$GLOBALS['SYMB_UID'].')';
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
			$sql = 'SELECT occid FROM omoccurrences WHERE (catalognumber = "'.$catNumber.'") AND (collid = '.$collid.') AND (occid != '.$skipOccid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->occid] = $r->occid;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getDupesOtherCatalogNumbers($otherCatNum, $collid, $skipOccid){
		$retArr = array();
		$otherCatNum = $this->cleanInStr($otherCatNum);
		if(is_numeric($collid) && is_numeric($skipOccid) && $otherCatNum){
			$sql = 'SELECT o.occid FROM omoccurrences o LEFT JOIN omoccuridentifiers i ON o.occid = i.occid
				WHERE (o.othercatalognumbers = "'.$otherCatNum.'" OR i.identifierValue = "'.$otherCatNum.'") AND (o.collid = '.$collid.') AND (o.occid != '.$skipOccid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->occid] = $r->occid;
			}
			$rs->free();
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