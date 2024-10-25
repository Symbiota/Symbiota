<?php
include_once('Manager.php');

class SiteMapManager extends Manager{

	private $collArr = array();
	private $obsArr = array();
	private $genObsArr = array();

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
 		parent::__destruct();
	}

	public function setCollectionList(){
		global $USER_RIGHTS, $IS_ADMIN;
		$adminArr = array();
		$editorArr = array();
		$sql = 'SELECT collid, CONCAT_WS(":", institutioncode, collectioncode) AS ccode, collectionname, colltype FROM omcollections ';
		if(!$IS_ADMIN){
			if(array_key_exists("CollAdmin",$USER_RIGHTS)){
				$adminArr = $USER_RIGHTS['CollAdmin'];
			}
			if(array_key_exists("CollEditor",$USER_RIGHTS)){
				$editorArr = $USER_RIGHTS['CollEditor'];
			}
			if($adminArr || $editorArr){
				$sql .= 'WHERE (collid IN('.implode(',',array_merge($adminArr,$editorArr)).')) ';
			}
			else{
				$sql = '';
			}
		}
		if($sql){
			$sql .= "ORDER BY collectionname";
			//echo "<div>".$sql."</div>";
			$rs = $this->conn->query($sql);
			if($rs){
				while($row = $rs->fetch_object()){
					$name = $row->collectionname.($row->ccode?" (".$row->ccode.")":"");
					$isCollAdmin = ($IS_ADMIN||in_array($row->collid,$adminArr)?1:0);
					if($row->colltype == 'Observations'){
						$this->obsArr[$row->collid]['name'] = $name;
						$this->obsArr[$row->collid]['isadmin'] = $isCollAdmin;
					}
					elseif($row->colltype == 'General Observations'){
						$this->genObsArr[$row->collid]['name'] = $name;
						$this->genObsArr[$row->collid]['isadmin'] = $isCollAdmin;
					}
					else{
						$this->collArr[$row->collid]['name'] = $name;
						$this->collArr[$row->collid]['isadmin'] = $isCollAdmin;
					}
				}
				$rs->close();
			}
		}
	}

	public function getCollArr(){
		return $this->collArr;
	}

	public function getObsArr(){
		return $this->obsArr;
	}

	public function getGenObsArr(){
		return $this->genObsArr;
	}

	public function getChecklistList($clArr){
		$returnArr = Array();
		$sql = 'SELECT clid, name, access FROM fmchecklists ';
		if($GLOBALS['IS_ADMIN']){
			//Show all without restrictions
		}
		elseif($clArr){
			$sql .= 'WHERE (access LIKE "public%" OR clid IN('.implode(',',$clArr).')) ';
		}
		else{
			//Show only public lists
			$sql .= 'WHERE (access LIKE "public%") ';
		}
		$sql .= 'ORDER BY name';
		//echo "<div>".$sql."</div>";
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$clName = $row->name.($row->access=='private'?' (limited access)':'');
			$returnArr[$row->clid] = $clName;
		}
		$rs->close();
		return $returnArr;
	}

	public function getProjectList($projArr = ""){
		$returnArr = Array();
		$sql = 'SELECT pid, projname, managers FROM fmprojects WHERE ispublic = 1 ';
		if($projArr){
			$sql .= 'AND (pid IN('.implode(',',$projArr).')) ';
		}
		$sql .= 'ORDER BY projname';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		if($rs){
			while($row = $rs->fetch_object()){
				$returnArr[$row->pid]['name'] = $row->projname;
				$returnArr[$row->pid]['managers'] = $row->managers;
			}
			$rs->close();
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