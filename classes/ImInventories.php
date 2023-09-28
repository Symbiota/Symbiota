<?php
include_once('Manager.php');

class ImInventories extends Manager{

	private $clid;
	private $pid;
	private $fieldMap = array();
	private $parameterArr = array();
	private $typeStr = '';
	private $primaryKey;

	public function __construct($conType = 'write') {
		parent::__construct(null, $conType);
	}

	public function __destruct(){
		parent::__destruct();
	}

	//Checklist functions
	public function getChecklistMetadata($pid){
		$retArr = array();
		if($this->clid){
			$sql = 'SELECT clid, name, locality, publication, abstract, authors, parentclid, notes, latcentroid, longcentroid, pointradiusmeters,
				access, defaultsettings, dynamicsql, datelastmodified, dynamicProperties, uid, type, footprintwkt, sortsequence, initialtimestamp
				FROM fmchecklists WHERE (clid = '.$this->clid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$retArr['name'] = $this->cleanOutStr($row->name);
				$retArr['locality'] = $this->cleanOutStr($row->locality);
				$retArr['notes'] = $this->cleanOutStr($row->notes);
				$retArr['type'] = $row->type;
				$retArr['publication'] = $this->cleanOutStr($row->publication);
				$retArr['abstract'] = $this->cleanOutStr($row->abstract);
				$retArr['authors'] = $this->cleanOutStr($row->authors);
				$retArr['parentclid'] = $row->parentclid;
				$retArr['uid'] = $row->uid;
				$retArr['latcentroid'] = $row->latcentroid;
				$retArr['longcentroid'] = $row->longcentroid;
				$retArr['pointradiusmeters'] = $row->pointradiusmeters;
				$retArr['access'] = $row->access;
				$retArr['defaultsettings'] = $row->defaultsettings;
				$retArr['dynamicsql'] = $row->dynamicsql;
				$retArr['hasfootprintwkt'] = ($row->footprintwkt?'1':'0');
				$retArr['sortsequence'] = $row->sortsequence;
				$retArr['datelastmodified'] = $row->datelastmodified;
				$retArr['dynamicProperties'] = $row->dynamicProperties;
			}
			$result->free();
			if($retArr){
				if($retArr['type'] == 'excludespp'){
					$sql = 'SELECT clid FROM fmchklstchildren WHERE clidchild = '.$this->clid;
					$rs = $this->conn->query($sql);
					while($r = $rs->fetch_object()){
						$retArr['excludeparent'] = $r->clid;
					}
					$rs->free();
				}
				if($pid && is_numeric($pid)){
					$sql = 'SELECT clNameOverride, mapChecklist, sortSequence, notes FROM fmchklstprojlink WHERE clid = '.$this->clid.' AND pid = '.$pid;
					$rs = $this->conn->query($sql);
					if($rs){
						if($r = $rs->fetch_object()){
							$retArr['clNameOverride'] = $this->cleanOutStr($r->clNameOverride);
							$retArr['mapchecklist'] = $r->mapChecklist;
							$retArr['sortsequence'] = $r->sortSequence;
						}
						$rs->free();
					}
				}
			}
		}
		return $retArr;
	}

	public function insertChecklist($inputArr){
		$status = false;
		if($inputArr['name']){
			if(empty($inputArr['uid'])) $inputArr['uid'] = $GLOBALS['SYMB_UID'];
			$this->setChecklistFieldMap();
			$this->setParameterArr($inputArr);
			$sql = 'INSERT INTO fmchecklists(';
			$sqlValues = '';
			$paramArr = array();
			$delimiter = '';
			foreach($this->parameterArr as $fieldName => $value){
				$sql .= $delimiter.$fieldName;
				$sqlValues .= $delimiter.'?';
				$paramArr[] = $value;
				$delimiter = ', ';
			}
			$sql .= ') VALUES('.$sqlValues.') ';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error){
						$this->primaryKey = $stmt->insert_id;
						$status = true;
					}
					else $this->errorMessage = 'ERROR inserting fmchecklists record (2): '.$stmt->error;
				}
				else $this->errorMessage = 'ERROR inserting fmchecklists record (1): '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for fmchecklists insert: '.$this->conn->error;
		}
		return $status;
	}

	public function updateChecklist($inputArr){
		$status = false;
		$this->setChecklistFieldMap();
		$this->setParameterArr($inputArr);
		$sqlFrag = '';
		$paramArr = array();
		foreach($this->parameterArr as $fieldName => $value){
			$sqlFrag .= $fieldName . ' = ?, ';
			$paramArr[] = $value;
		}
		$sql = 'UPDATE fmchecklists SET '.trim($sqlFrag, ', ').' WHERE (clid = ?)';
		if($paramArr){
			$paramArr[] = $this->clid;
			$this->typeStr .= 'i';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error) $status = true;
					else $this->errorMessage = 'ERROR updating fmchecklists record: '.$stmt->error;
				}
				else $this->errorMessage = 'ERROR updating fmchecklists record (1): '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for updating fmchecklists: '.$this->conn->error;
			if($status){
				if($inputArr['type'] == 'rarespp' && $inputArr['locality']){
					$sql = 'UPDATE omoccurrences o INNER JOIN taxstatus ts1 ON o.tidinterpreted = ts1.tid '.
						'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
						'INNER JOIN fmchklsttaxalink cl ON ts2.tid = cl.tid '.
						'SET o.localitysecurity = 1 '.
						'WHERE (cl.clid = '.$this->clid.') AND (o.stateprovince = "'.$this->cleanInStr($inputArr['locality']).'") AND (o.localitySecurityReason IS NULL) '.
						'AND (o.localitysecurity IS NULL OR o.localitysecurity = 0) AND (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) ';
					if(!$this->conn->query($sql)){
						$this->errorMessage = 'Error updating rare state species: '.$this->conn->error;
					}
				}
				elseif($inputArr['type'] == 'excludespp' && is_numeric($inputArr['excludeparent'])){
					$sql = 'INSERT IGNORE INTO fmchklstchildren(clid, clidchild) VALUES(?, ?)';
					if($stmt = $this->conn->prepare($sql)){
						$stmt->bind_param('ii', $inputArr['excludeparent'], $this->clid);
						if(!$stmt->execute()){
							$this->errorMessage = 'Error updating parent checklist for exclusion species list: '.$this->conn->error;
						}
						$stmt->close();
					}
				}
			}
		}
		return $status;
	}

	private function setChecklistFieldMap(){
		$this->fieldMap = array('name' => 's', 'authors' => 's', 'type' => 's', 'locality' => 's', 'publication' => 's', 'abstract' => 's', 'notes' => 's',
			'latCentroid' => 'd', 'longCentroid' => 'd', 'pointRadiusMeters' => 'i', 'access' => 's', 'defaultSettings' => 's', 'dynamicSql' => 's',
			'dynamicProperties' => 's', 'uid' => 'i', 'footprintWkt' => 's', 'sortSequence' => 'i');
	}

	public function deleteChecklist(){
		$status = false;
		$roleArr = $this->getManagers('ClAdmin', 'fmchecklists', $this->clid);
		unset($roleArr[$GLOBALS['SYMB_UID']]);
		if(!$roleArr){
			$this->deleteChecklistTaxaLinks();
			$sql = 'DELETE FROM fmchecklists WHERE clid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->clid);
				$stmt->execute();
				if($stmt->affected_rows && !$stmt->error){
					$status = true;
					//Delete userpermissions reference once patch is submitted
					$this->deleteUserRole('ClAdmin', $this->clid, $GLOBALS['SYMB_UID']);
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
		}
		else{
			$this->errorMessage = 'Checklist cannot be deleted until all editors are removed. Remove editors and then try again.';
		}
		return $status;
	}

	public function getChecklistArr($pid = 0){
		$retArr = Array();
		$sql = 'SELECT c.clid, c.name, c.latcentroid, c.longcentroid, c.access FROM fmchecklists c ';
		if($pid && is_numeric($pid)) $sql .= 'INNER JOIN fmchklstprojlink pl ON c.clid = pl.clid WHERE (pl.pid = '.$pid.') ';
		$sql .= 'ORDER BY c.sortSequence, c.name';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->clid]['name'] = $r->name;
			$retArr[$r->clid]['lat'] = $r->latcentroid;
			$retArr[$r->clid]['lng'] = $r->longcentroid;
			$retArr[$r->clid]['access'] = $r->access;
		}
		$rs->free();
		return $retArr;
	}

	//Checklist taxa linkages
	private function deleteChecklistTaxaLinks(){
		$status = false;
		if($this->clid){
			$sql = 'DELETE FROM fmchklsttaxalink WHERE clid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->clid);
				$stmt->execute();
				if($stmt->error) $this->errorMessage = $stmt->error;
				else $status = true;
				$stmt->close();
			}
		}
		return $status;
	}

	//Child-Parent checklist functions
	public function insertChildChecklist($clidChild, $modifiedUid){
		$status = false;
		$sql = 'INSERT INTO fmchklstchildren(clid, clidchild, modifiedUid) VALUES(?,?,?) ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->clid, $clidChild, $modifiedUid);
			if($stmt->execute()){
				if($stmt->affected_rows && !$stmt->error){
					$status = true;
				}
				else $this->errorMessage = 'ERROR inserting child checklist record (2): '.$stmt->error;
			}
			else $this->errorMessage = 'ERROR inserting child checklist record (1): '.$stmt->error;
			$stmt->close();
		}
		return $status;
	}

	public function deleteChildChecklist($clidDel){
		$status = false;
		if(is_numeric($clidDel)){
			$sql = 'DELETE FROM fmchklstchildren WHERE clid = '.$this->clid.' AND clidchild = '.$clidDel;
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR deleting child checklist link';
			}
		}
		return $status;
	}

	//Checklist coordinates functions
	public function insertChecklistCoordinates($inputArr){
		$status = false;
		if($this->clid && isset($inputArr['tid']) && $inputArr['tid']){
			$sql = 'INSERT INTO fmchklstcoordinates(clid';
			$sqlValues = '?, ';
			$paramArr = array($this->clid);
			$this->typeStr = 'i';
			$this->setChecklistCoordinatesFieldMap();
			$this->setParameterArr($inputArr);
			foreach($this->parameterArr as $fieldName => $value){
				$sql .= ', '.$fieldName;
				$sqlValues .= '?, ';
				$paramArr[] = $value;
			}
			$sql .= ') VALUES('.trim($sqlValues, ', ').') ';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error){
						$this->primaryKey = $stmt->insert_id;
						$status = true;
					}
					else $this->errorMessage = 'ERROR inserting fmchklstcoordinates record (2): '.$stmt->error;
				}
				else $this->errorMessage = 'ERROR inserting fmchklstcoordinates record (1): '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for fmchklstcoordinates insert: '.$this->conn->error;
		}
		return $status;
	}

	public function updateChecklistCoordinates($inputArr){
		$status = false;
		if($this->clid && isset($inputArr['clCoordID']) && $inputArr['clCoordID']){
			$this->setChecklistCoordinatesFieldMap();
			$this->setParameterArr($inputArr);
			$paramArr = array();
			$sqlFrag = '';
			foreach($this->parameterArr as $fieldName => $value){
				$sqlFrag .= $fieldName . ' = ?, ';
				$paramArr[] = $value;
			}
			$paramArr[] = $inputArr['clCoordID'];
			$this->typeStr .= 'i';
			$sql = 'UPDATE fmchklstcoordinates SET '.trim($sqlFrag, ', ').' WHERE (clCoordID = ?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($this->typeStr, ...$paramArr);
				$stmt->execute();
				if($stmt->affected_rows || !$stmt->error) $status = true;
				else $this->errorMessage = 'ERROR updating fmchklstcoordinates record: '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for updating fmchklstcoordinates: '.$this->conn->error;
		}
		return $status;
	}

	public function deleteChecklistCoordinates($pk){
		if($this->assocID){
			$sql = 'DELETE FROM fmchklstcoordinates WHERE chklstCoordID = '.$pk;
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR deleting fmchklstcoordinates record: '.$this->conn->error;
				return false;
			}
		}
	}

	private function setChecklistCoordinatesFieldMap(){
		$this->fieldMap = array('tid' => 'i', 'decimalLatitude' => 'd', 'decimalLongitude' => 'd', 'sourceName' => 's',
			'sourceIdentifier' => 's', 'referenceUrl' => 's', 'notes' => 's', 'dynamicProperties' => 's');
	}

	//Inventory Project functions
	public function getProjectMetadata(){
		$returnArr = Array();
		if($this->pid){
			$sql = 'SELECT pid, projname, managers, fulldescription, notes, occurrencesearch, ispublic, sortsequence FROM fmprojects WHERE (pid = '.$this->pid.') ';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->pid = $row->pid;
				$returnArr['projname'] = $row->projname;
				$returnArr['managers'] = $row->managers;
				$returnArr['fulldescription'] = $row->fulldescription;
				$returnArr['notes'] = $row->notes;
				$returnArr['occurrencesearch'] = $row->occurrencesearch;
				$returnArr['ispublic'] = $row->ispublic;
				$returnArr['sortsequence'] = $row->sortsequence;
				if($row->ispublic == 0){
					$this->isPublic = 0;
				}
			}
			$rs->free();
			//Temporarly needed as a separate call until db_schema_patch-1.1.sql is applied
			$sql = 'SELECT headerurl FROM fmprojects WHERE (pid = '.$this->pid.')';
			$rs = $this->conn->query($sql);
			if($rs){
				if($r = $rs->fetch_object()){
					$returnArr['headerurl'] = $r->headerurl;
				}
				$rs->free();
			}
		}
		return $returnArr;
	}

	public function insertProject($inputArr){
		$newPid = 0;
		$projName = $inputArr['projname'];
		$managers = (isset($inputArr['managers'])?$inputArr['managers']:NULL);
		$fullDescription = (isset($inputArr['fulldescription'])?$inputArr['fulldescription']:NULL);
		$notes = (isset($inputArr['notes'])?$inputArr['notes']:NULL);
		$isPublic = (isset($inputArr['ispublic'])?$inputArr['ispublic']:0);
		$sql = 'INSERT INTO fmprojects(projname, managers, fulldescription, notes, ispublic) VALUES(?, ?, ?, ?, ?)';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ssssi', $projName, $managers, $fullDescription, $notes, $isPublic);
			if($stmt->execute()){
				if($stmt->affected_rows && !$stmt->error){
					$newPid = $stmt->insert_id;
					$this->pid = $newPid;
				}
				else $this->errorMessage = 'ERROR inserting fmprojects record (2): '.$stmt->error;
			}
			else $this->errorMessage = 'ERROR inserting fmprojects record (1): '.$stmt->error;
			$stmt->close();
		}
		return $newPid;
	}

	public function updateProject($inputArr){
		$status = false;
		$projName = $inputArr['projname'];
		$managers = $inputArr['managers'];
		$fullDescription = $inputArr['fulldescription'];
		$notes = $inputArr['notes'];
		$isPublic = $inputArr['ispublic'];

		$sql = 'UPDATE fmprojects SET projname = ?, managers = ?, fulldescription = ?, notes = ?, ispublic = ? WHERE (pid = ?)';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ssssii', $projName, $managers, $fullDescription, $notes, $isPublic, $this->pid);
			if($stmt->execute()){
				if(!$stmt->error){
					$status = true;
				}
				else $this->errorMessage = 'ERROR updating fmprojects record (2): '.$stmt->error;
			}
			else $this->errorMessage = 'ERROR updating fmprojects record (1): '.$stmt->error;
			$stmt->close();
		}
		return $status;
	}

	public function deleteProject($projID){
		$status = true;
		if($projID && is_numeric($projID)){
			$sql = 'DELETE FROM fmprojects WHERE pid = '.$projID;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR deleting inventory project: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function getProjectList(){
		$retArr = Array();
		$sql = 'SELECT pid, projname, managers, fulldescription FROM fmprojects WHERE ispublic = 1 ORDER BY projname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->pid]['projname'] = $r->projname;
			$retArr[$r->pid]['managers'] = $r->managers;
			$retArr[$r->pid]['descr'] = $r->fulldescription;
		}
		$rs->free();
		return $retArr;
	}

	//Checklist Project Link functions
	public function insertChecklistProjectLink($clid){
		$status = true;
		if(is_numeric($clid)){
			$sql = 'INSERT INTO fmchklstprojlink(pid,clid) VALUES('.$this->pid.', '.$clid.') ';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR adding checklist to project: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function deleteChecklistProjectLink($clid){
		$status = true;
		if(is_numeric($clid)){
			$sql = 'DELETE FROM fmchklstprojlink WHERE (pid = '.$this->pid.') AND (clid = '.$clid.')';
			if($this->conn->query($sql)){
				return 'ERROR deleting checklist from project:'.$this->conn->error;
			}
		}
		return $status;
	}

	//User role funcitons
	public function getManagers($role, $tableName, $tablePK){
		$retArr = array();
		if(is_numeric($tablePK)){
			$sql = 'SELECT u.uid, CONCAT_WS(", ", u.lastname, u.firstname) as fullname, u.username '.
				'FROM userroles r INNER JOIN users u ON r.uid = u.uid '.
				'WHERE r.role = "'.$this->cleanInStr($role).'" AND r.tableName = "'.$this->cleanInStr($tableName).'" AND r.tablepk = '.$tablePK;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->uid] = $r->fullname.' ('.$r->username.')';
			}
			$rs->free();
			asort($retArr);
		}
		return $retArr;
	}

	public function insertUserRole($uid, $role, $tableName, $tablePK, $uidAssignedBy){
		$status = false;
		$sql = 'INSERT INTO userroles (uid, role, tablename, tablepk, uidassignedby) VALUES(?,?,?,?,?) ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('isssi', $uid, $role, $tableName, $tablePK, $uidAssignedBy);
			if($stmt->execute()){
				if($stmt->affected_rows && !$stmt->error){
					$status = true;
				}
				else $this->errorMessage = 'ERROR inserting user role record (2): '.$stmt->error;
			}
			else $this->errorMessage = 'ERROR inserting user role record (1): '.$stmt->error;
			$stmt->close();
		}
		return $status;
	}

	public function deleteUserRole($role, $tablePK, $uid){
		if(is_numeric($tablePK) && is_numeric($uid)){
			$sql = 'DELETE FROM userroles WHERE (role = "'.$this->cleanInStr($role).'") AND (tablepk = '.$tablePK.') AND (uid = '.$uid.')';
			$this->conn->query($sql);
		}
	}

	public function getUserArr(){
		$retArr = array();
		$sql = 'SELECT uid, CONCAT_WS(", ", lastname, firstname) as fullname, username FROM users ORDER BY lastname, firstname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->fullname.' ('.$r->username.')';
		}
		$rs->free();
		return $retArr;
	}

	//Mics support functions
	private function setParameterArr($inputArr){
		foreach($this->fieldMap as $field => $type){
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = trim($inputArr[$postField]);
				if(!$value) $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr .= $type;
			}
		}
		if(isset($inputArr['clid']) && $inputArr['clid'] && !$this->clid) $this->clid = $inputArr['clid'];
	}

	//Setter and getter functions
	public function setClid($clid){
		if(is_numeric($clid)) $this->clid = $clid;
	}

	public function getPid(){
		return $this->pid;
	}

	public function setPid($pid){
		if(is_numeric($pid)) $this->pid = $pid;
	}

	public function getPrimaryKey(){
		return $this->primaryKey;
	}
}
?>