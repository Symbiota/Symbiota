<?php
include_once ($SERVER_ROOT . '/classes/OmCollections.php');

class PortalIndex extends OmCollections{

	private $portalID;

	function __construct(){
		parent::__construct('write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getPortalIndexArr($portalID){
		$retArr = array();
		$sql = 'SELECT portalID, portalName, acronym, portalDescription, urlRoot, securityKey, symbiotaVersion, guid, manager, managerEmail, primaryLead, primaryLeadEmail, notes, initialTimestamp FROM portalindex ';
		if($portalID) $sql .= 'WHERE portalID = '.$portalID;
		else $sql .= 'ORDER BY portalName';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$retArr[$r['portalID']] = $r;
		}
		$rs->free();

		$sql = 'SELECT portalID, count(*) as cnt FROM portaloccurrences WHERE portalID IN('.implode(',',array_keys($retArr)).') GROUP BY portalID';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->portalID]['occurCnt'] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getCollectionList($urlRoot, $collID=''){
		$retArr = array();
		$url = $urlRoot.'/api/v2/collection/'.$collID;
		$retArr = $this->getAPIResponce($url);
		if(!$collID){
			$retArr = $retArr['results'];
			foreach($retArr as $id => $collArr){
				if($collArr['managementType'] == 'Live Data' && !$collArr['collectionID']){
					if(isset($collArr['recordID'])) $collArr['collectionID'] = $collArr['recordID'];
					elseif(isset($collArr['collectionGuid'])) $collArr['collectionID'] = $collArr['collectionGuid'];
				}
				$retArr[$id]['internal'] = $this->getInternalCollection($collArr['collectionID'],$collArr['collectionGuid']);
			}
		}
		else $retArr['internal'] = $this->getInternalCollection($retArr['collectionID'],$retArr['collectionGuid']);
		return $retArr;
	}

	private function getInternalCollection($guid,$guid2){
		$retArr = array();
		if($guid || $guid2){
			$guidArr = array();
			if($guid) $guidArr[] = $guid;
			if($guid2) $guidArr[] = $guid2;
			$sql = 'SELECT c.collid, c.managementType, s.recordCnt, s.uploadDate FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid WHERE c.collectionid IN("'.implode('","',$guidArr).'")';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->collid]['managementType'] = $r->managementType;
				$retArr[$r->collid]['recordCnt'] = $r->recordCnt;
				$retArr[$r->collid]['uploadDate'] = $r->uploadDate;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getDataImportProfile($collid){
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT title, uspid, path, internalQuery, queryStr, cleanUpSP FROM uploadspecparameters WHERE uploadType = 13 AND collid = '.$collid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->uspid]['title'] = $r->title;
				$retArr[$r->uspid]['path'] = $r->path;
				$retArr[$r->uspid]['internalQuery'] = $r->internalQuery;
				$retArr[$r->uspid]['queryStr'] = $r->queryStr;
				$retArr[$r->uspid]['cleanUpSp'] = $r->cleanUpSP;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function importProfile($portalID, $remoteID){
		$portal = $this->getPortalIndexArr($portalID);
		$url = $portal[$portalID]['urlRoot'].'/api/v2/collection/'.$remoteID;
		$collArr = $this->getAPIResponce($url);
		if(!$collArr['collectionID']){
			if(isset($collArr['recordID'])) $collArr['collectionID'] = $collArr['recordID'];
			elseif(isset($collArr['collectionGuid'])) $collArr['collectionID'] = $collArr['collectionGuid'];
		}
		$targetFieldArr = array('institutionCode','collectionCode','collectionName','collectionID','datasetID','fullDescription','homepage','resourceJson','contactJson','individualUrl',
			'latitudeDecimal','longitudeDecimal','icon','collType','rightsHolder','rights','usageTerm','accessRights','bibliographicCitation');
		$collArr = array_intersect_key($collArr, array_flip($targetFieldArr));
		$collArr['managementType'] = 'Snapshot';
		$collArr['guidTarget'] = 'occurrenceId';
		$parse = parse_url($portal[$portalID]['urlRoot']);
		$collArr['icon'] = $parse['host'].$collArr['icon'];
		if($collid = $this->collectionInsert($collArr)){
			$sql = 'INSERT INTO uploadspecparameters(collid, uploadType, title, path, queryStr, endpointPublic, createdUid)
				VALUES('.$collid.',13,"Symbiota Import","'.$portal[$portalID]['urlRoot'].'",NULL,1,'.$GLOBALS['SYMB_UID'].')';
			if(!$this->conn->query($sql)){
				$this->warningArr[] = 'ERROR creating import profile: '.$this->conn->error;
			}
		}
		return $collid;
	}

	private function getAPIResponce($url, $asyc = false){
		$resJson = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if($asyc) curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
		$resJson = curl_exec($ch);
		if(!$resJson){
			$this->errorMessage = 'FATAL CURL ERROR: '.curl_error($ch).' (#'.curl_errno($ch).')';
			return false;
			//$header = curl_getinfo($ch);
		}
		curl_close($ch);
		return json_decode($resJson,true);
	}

	// Setters and getters
	public function setPortalID($id){
		$this->portalID = $id;
	}
}
?>