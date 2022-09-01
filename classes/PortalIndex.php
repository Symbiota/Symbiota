<?php
include_once ($SERVER_ROOT . '/classes/OmCollections.php');

class PortalIndex extends OmCollections{

	private $publicationID;
	private $portalID;
	private $returnHeader = array();

	function __construct(){
		parent::__construct('write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getSelfDetails(){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$retArr = null;
		$retArr['portalName'] = $GLOBALS['DEFAULT_TITLE'];
		$retArr['guid'] = $GLOBALS['PORTAL_GUID'];
		$retArr['urlRoot'] = $this->getDomain().$GLOBALS['CLIENT_ROOT'];
		$retArr['managerEmail'] = $GLOBALS['ADMIN_EMAIL'];
		$retArr['symbiotaVersion'] = $GLOBALS['CODE_VERSION'];
		return $retArr;
	}

	public function getPortalIndexArr($portalIdentifier){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$retArr = array();
		$sql = 'SELECT portalID, portalName, acronym, portalDescription, urlRoot, securityKey, symbiotaVersion, guid, manager, managerEmail, primaryLead, primaryLeadEmail, notes, initialTimestamp FROM portalindex ';
		if($portalIdentifier){
			if(is_numeric($portalIdentifier)) $sql .= 'WHERE portalID = '.$portalIdentifier;
			else $sql .= 'WHERE guid = "'.$portalIdentifier.'" ';
		}
		else $sql .= 'ORDER BY portalName';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$retArr[$r['portalID']] = $r;
		}
		$rs->free();

		if($retArr){
			$sql = 'SELECT p.portalID, count(o.occid) as cnt
				FROM portaloccurrences o INNER JOIN portalpublications p ON o.pubid = p.pubid
				WHERE p.portalID IN('.implode(',',array_keys($retArr)).') GROUP BY p.portalID';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->portalID]['occurCnt'] = $r->cnt;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getCollectionList($urlRoot, $collID=''){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
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
			$guidStr = '';
			if($guid) $guidStr = ',"'.$guid.'"';
			if($guid2) $guidStr .= ',"'.$guid2.'"';
			$sql = 'SELECT c.collid, c.managementType, s.recordCnt, s.uploadDate
				FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid
				WHERE c.collectionid IN('.trim($guidStr,' ,').')';
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

	public function getRemoteCollectionByID($urlRoot, $id){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$retArr = array();
		//Get collection identifier
		$url = $urlRoot.'/api/v2/occurrence/'.$id;
		$occurArr = $this->getAPIResponce($url);
		//Get collection metadata
		if(isset($occurArr['collID'])){
			$url = $urlRoot.'/api/v2/collection/'.$occurArr['collID'];
			$retArr = $this->getAPIResponce($url);
			if(!$retArr) return false;
		}
		return $retArr;
	}

	public function getDataImportProfile($collid){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT title, uspid, path, internalQuery, queryStr, cleanUpSP FROM uploadspecparameters WHERE uploadType = 13 AND collid = '.$collid;
			$rs = $this->conn->query($sql);
			if(!$rs){
				//Temp code only needed until db_schema_patch-1.2 is pushed to production
				$sql = 'SELECT title, uspid, path, queryStr, cleanUpSP FROM uploadspecparameters WHERE uploadType = 13 AND collid = '.$collid;
				$rs = $this->conn->query($sql);
			}
			if($rs){
				while($r = $rs->fetch_object()){
					$retArr[$r->uspid]['title'] = $r->title;
					$retArr[$r->uspid]['path'] = $r->path;
					if(isset($r->internalQuery)) $retArr[$r->uspid]['internalQuery'] = $r->internalQuery;
					$retArr[$r->uspid]['queryStr'] = $r->queryStr;
					$retArr[$r->uspid]['cleanUpSp'] = $r->cleanUpSP;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function initiateHandshake($remotePath){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$respArr = false;
		if($remotePath){
			if(substr($remotePath,-9) == 'index.php') $remotePath = substr($remotePath, 0, strlen($remotePath)-9);
			if(substr($remotePath,-1) != '/') $remotePath .= '/';
			//https://midwestherbaria.org/portal/api/v2/installation/518a57c3-98ce-4977-bb1c-e9eb39d45732/touch?endpoint=https://panamabiota.org/stri
			//Handshake from remote to local
			//$self = $this->getSelfDetails();
			//$handShakeUrl = $remotePath.'api/v2/installation/'.$self['guid'].'/touch?endpoint='.$self['urlRoot'];
			//Handshake from local to remote
			$pingUrl = $remotePath.'api/v2/installation/ping';
			$remoteArr = $this->getAPIResponce($pingUrl);
			if($remoteArr){
				$handShakeUrl = $this->getDomain().$GLOBALS['CLIENT_ROOT'].'/api/v2/installation/'.$remoteArr['guid'].'/touch?endpoint='.$remoteArr['urlRoot'];
				//echo '<div>Handshake URL: '.$handShakeUrl.'</div>';
				$respArr = $this->getAPIResponce($handShakeUrl);
			}
			else{
				$this->errorMessage = 'Unable to connect to remote portal (url: '.$pingUrl.')';
			}
		}
		return $respArr;
	}

	public function importProfile($portalID, $remoteID){
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		$portal = $this->getPortalIndexArr($portalID);
		$url = $portal[$portalID]['urlRoot'].'/api/v2/collection/'.$remoteID;
		$collArr = $this->getAPIResponce($url);
		$targetCollid = $collArr['collID'];
		if(!$collArr['collectionID']){
			if(isset($collArr['recordID'])) $collArr['collectionID'] = $collArr['recordID'];
			elseif(isset($collArr['collectionGuid'])) $collArr['collectionID'] = $collArr['collectionGuid'];
		}
		$targetFieldArr = array('institutionCode','collectionCode','collectionName','collectionID','datasetID','fullDescription','homepage','resourceJson','contactJson','individualUrl',
			'latitudeDecimal','longitudeDecimal','icon','collType','rightsHolder','rights','usageTerm','accessRights','bibliographicCitation');
		$collArr = array_intersect_key($collArr, array_flip($targetFieldArr));
		$collArr['managementType'] = 'Snapshot';
		$collArr['guidTarget'] = 'occurrenceId';
		if(substr($collArr['icon'],0,1) == '/'){
			$parse = parse_url($portal[$portalID]['urlRoot']);
			$collArr['icon'] = $parse['host'].$collArr['icon'];
		}
		$uploadType = 13;
		$queryStr = null;
		$endpointPublic = 1;
		$title = 'Symbiota Import';
		if($collid = $this->collectionInsert($collArr)){
			$dwcaPath = $portal[$portalID]['urlRoot'].'/webservices/dwc/dwcapubhandler.php?collid='.$targetCollid.'&schema=symbiota&extended=1';
			$sql = 'INSERT INTO uploadspecparameters(collid, uploadType, title, path, queryStr, endpointPublic, createdUid) VALUES(?,?,?,?,?,?,?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('iisssii', $collid, $uploadType, $title, $dwcaPath, $queryStr, $endpointPublic, $GLOBALS['SYMB_UID']);
				$stmt->execute();
				if(!$stmt->affected_rows && $stmt->error) $this->warningArr[] = 'ERROR creating import profile: '.$this->conn->error;
				$stmt->close();
			}
		}
		return $collid;
	}

	private function getAPIResponce($url, $asyc = false){
		$status = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if($asyc) curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
		$resJson = curl_exec($ch);
		$this->returnHeader = curl_getinfo($ch);
		if($this->returnHeader['http_code'] == '200') $status = true;
		elseif($this->returnHeader['http_code'] == '404') $this->errorMessage = '404 ULR Not Found';
		else $this->errorMessage = 'http code '.$this->returnHeader['http_code'];
		curl_close($ch);
		if($status) return json_decode($resJson, true);
		return false;
	}

	//Data write actions
	public function createPortalPublication($inputArr){
		$newPubIndex = 0;
		if(!array_key_exists('pubTitle', $inputArr) || !$inputArr['pubTitle']){
			$this->errorMessage = 'pubTitle is a required field';
			return false;
		}
		if(!array_key_exists('collid', $inputArr) || !$inputArr['collid']){
			$this->errorMessage = 'collid is a required field';
			return false;
		}
		if(!array_key_exists('portalID', $inputArr) || !$inputArr['portalID']){
			$this->errorMessage = 'portalID is a required field';
			return false;
		}
		if(!array_key_exists('direction', $inputArr) || !$inputArr['direction']){
			$this->errorMessage = 'direction is a required field';
			return false;
		}
		$portalID = $inputArr['portalID'];
		if(!is_numeric($portalID)){
			$portalArr = $this->getPortalIndexArr($portalID);
			if($portalArr) $portalID = key($portalArr);
			if(!is_numeric($portalID)){
				$this->errorMessage = 'unable to tranlate portalID ('.$portalID.')';
				return false;
			}
		}
		$pubTitle = $inputArr['pubTitle'];
		$collid = $inputArr['collid'];
		$direction = $inputArr['direction'];
		$description = isset($inputArr['description']) && $inputArr['description']?$inputArr['description']:NULL;
		$criteriaJson = isset($inputArr['criteriaJson']) && $inputArr['criteriaJson']?$inputArr['criteriaJson']:NULL;
		$includeDeterminations = isset($inputArr['includeDeterminations']) && is_numeric($inputArr['includeDeterminations'])?$inputArr['includeDeterminations']:1;
		$includeImages = isset($inputArr['includeImages']) && is_numeric($inputArr['includeImages'])?$inputArr['includeImages']:1;
		$autoUpdate = isset($inputArr['autoUpdate']) && is_numeric($inputArr['autoUpdate'])?$inputArr['autoUpdate']:0;
		$lastDateUpdate = isset($inputArr['lastDateUpdate']) && $inputArr['lastDateUpdate']?$inputArr['lastDateUpdate']:null;
		$updateInterval = isset($inputArr['updateInterval']) && $inputArr['updateInterval']?$inputArr['updateInterval']:null;
		$createdUid = $GLOBALS['SYMB_UID'];
		$sql = 'INSERT INTO portalpublications(pubTitle, description, collid, portalID, direction, criteriaJson, includeDeterminations, includeImages, autoUpdate, lastDateUpdate, updateInterval, createdUid)
			VALUES(?,?,?,?,?,?,?,?,?,?,?,?)';
		if($stmt = $this->conn->prepare($sql)) {
			$stmt->bind_param('ssiissiiisii', $pubTitle, $description, $collid, $portalID, $direction, $criteriaJson, $includeDeterminations, $includeImages, $autoUpdate, $lastDateUpdate, $updateInterval, $createdUid);
			$stmt->execute();
			if($stmt->affected_rows) $newPubIndex = $this->conn->insert_id;
			elseif($stmt->error) $this->errorMessage = 'ERROR creating portalpublication profile: '.$this->conn->error;
			$stmt->close();
		}
		else $this->errorMessage = 'ERROR creating portalpublication profile: '.$this->conn->error;
		return $newPubIndex;
	}

	public function crossMapUploadedOccurrences($pubid, $collid){
		$status = false;
		if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])) return false;
		if(is_numeric($pubid) && is_numeric($collid)){
			$sql = 'INSERT INTO portaloccurrences(occid, targetOccid, pubID, refreshTimestamp)
				SELECT u.occid, u.dbpk, '.$pubid.', NOW()
				FROM uploadspectemp u LEFT JOIN portaloccurrences l ON u.occid = l.occid
				WHERE u.occid IS NOT NULL AND u.dbpk IS NOT NULL AND u.collid = '.$collid.' AND l.occid IS NULL';
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$status = false;
				$this->errorMessage = 'ERROR creating mapping occurrence to a publishing instance';
			}
		}
		return $status;
	}

	// Setters and getters
	public function setPortalID($id){
		$this->portalID = $id;
	}

	public function setPublicationID($id){
		$this->publicationID = $id;
	}
}
?>