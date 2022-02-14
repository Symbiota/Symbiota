<?php
include_once ($SERVER_ROOT . '/classes/Manager.php');

class PortalIndex extends Manager{

	private $portalID;

	function __construct(){
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getPortalIndexArr($portalID){
		$retArr = array();
		$sql = 'SELECT portalID, portalName, acronym, portalDescription, urlRoot, securityKey, symbiotaVersion, guid, manager, managerEmail, primaryLead, primaryLeadEmail, notes, initialTimestamp
			FROM portalindex ';
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

	public function getAPIResponce($url, $asyc = false){
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