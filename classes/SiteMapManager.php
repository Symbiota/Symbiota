<?php
include_once('Manager.php');

class SiteMapManager extends Manager{

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
 		parent::__destruct();
	}

	public function getCollectionList(){
		global $USER_RIGHTS, $IS_ADMIN;
		$retArr = array();
		$adminArr = array();
		$editorArr = array();
		$sql = 'SELECT collid, institutioncode, collectioncode, collectionname, colltype FROM omcollections ';
		if(!$IS_ADMIN){
			if(array_key_exists('CollAdmin', $USER_RIGHTS)){
				$adminArr = $USER_RIGHTS['CollAdmin'];
			}
			if(array_key_exists('CollEditor', $USER_RIGHTS)){
				$editorArr = $USER_RIGHTS['CollEditor'];
			}
			if($adminArr || $editorArr){
				$sql .= 'WHERE colltype != "General Observations" AND (collid IN('.implode(',',array_merge($adminArr,$editorArr)).')) ';
			}
			else{
				$sql = '';
			}
		}
		if($sql){
			$sql .= 'ORDER BY collectionname';
			$rs = $this->conn->query($sql);
			if($rs){
				while($r = $rs->fetch_object()){
					$retArr[$r->colltype][$r->collid] = $r->collectionname . ' (' . $r->institutioncode . ($r->collectioncode ? ':' . $r->collectioncode : '') . ')';
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getChecklistList(){
		global $USER_RIGHTS;
		$returnArr = Array();
		$sql = 'SELECT clid, name, access FROM fmchecklists ';
		if($GLOBALS['IS_ADMIN']){
			//Show all without restrictions
		}
		elseif(!empty($USER_RIGHTS['ClAdmin'])){
			$clStr = implode(',', $USER_RIGHTS['ClAdmin']);
			$sql .= 'WHERE (access LIKE "public%" OR clid IN(' . $clStr . ')) ';
		}
		else{
			//Show only public lists
			$sql .= 'WHERE (access LIKE "public%") ';
		}
		$sql .= 'ORDER BY name';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$clName = $row->name.($row->access=='private'?' (limited access)':'');
			$returnArr[$row->clid] = $clName;
		}
		$rs->free();
		return $returnArr;
	}

	public function getProjectList(){
		$returnArr = Array();
		$sql = 'SELECT pid, projname, managers FROM fmprojects WHERE ispublic = 1 ORDER BY projname';
		$rs = $this->conn->query($sql);
		if($rs){
			while($row = $rs->fetch_object()){
				$returnArr[$row->pid]['name'] = $row->projname;
				$returnArr[$row->pid]['managers'] = $row->managers;
			}
			$rs->free();
		}
		return $returnArr;
	}

	public function hasGlossary(){
		$bool = false;
		if($rs = $this->conn->query('SELECT glossid FROM glossary LIMIT 1')){
			if($rs->fetch_object()) $bool = true;
			$rs->free();
		}
		return $bool;
	}

	/**
	 *
	 * Determine the version number of the underlying schema.
	 *
	 * @return string representation of the most recently applied schema version
	 */
	public function getSchemaVersion() {
		$result = false;
		$sql = 'SELECT versionnumber FROM schemaversion ORDER BY dateapplied DESC LIMIT 1 ';
		try{
			$statement = $this->conn->prepare($sql);
			$statement->execute();
			$statement->bind_result($result);
			$statement->fetch();
			$statement->close();
		}
		catch(Exception $e){
			$this->errorMessage = $e->getMessage();
		}
		return $result;
	}
}
?>