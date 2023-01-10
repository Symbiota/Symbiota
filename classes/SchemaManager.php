<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class SchemaManager extends Manager{

	private $adminConn;
	private $currentVersion;
	private $verionDate;
	private $versionHistory = array();

	public function __construct(){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function setAdminConnect(){
		$this->adminConn = MySQLiConnectionFactory::getCon('admin');

	}

	public function setVerionHistory(){
		$sql = 'SELECT versionNumber, dateApplied FROM schemaversion ORDER BY id';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->currentVersion = $r->versionNumber;
			$this->versionDate = $r->dateApplied;
			$this->versionHistory[$r->versionNumber] = $r->dateApplied;
		}
		$rs->free();
	}

	public function getCurrentVersion(){
		return $this->currentVersion;
	}

	public function getVersionDate(){
		return $this->versionDate;
	}

	public function getVersionHistory(){
		if(!$this->versionHistory) $this->setVerionHistory();
		return $this->versionHistory;
	}
}
?>