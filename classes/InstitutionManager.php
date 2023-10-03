<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class InstitutionManager {

	private $conn;
	private $iid;
	private $collid;
	private $errorStr;

	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon("write");
	}

	public function __destruct(){
		if(!($this->conn === null)) $this->conn->close();
	}

	public function getInstitutionData(){
		$retArr = Array();
		if($this->iid){
			$sql = 'SELECT iid, institutioncode, institutionname, institutionname2, address1, address2, city, '.
				'stateprovince, postalcode, country, phone, contact, email, url, notes '.
				'FROM institutions '.
				'WHERE iid = '.$this->iid;
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_assoc()){
				$retArr = $this->cleanOutArr($row);
			}
			$rs->free();
		}
		return $retArr;
	}

	public function submitInstitutionEdits($postData){
		$status = true;
		if($postData['institutioncode'] && $postData['institutionname']){
			$sql = 'UPDATE institutions SET '.
				'institutioncode = "'.$this->cleanInStr($postData['institutioncode']).'",'.
				'institutionname = "'.$this->cleanInStr($postData['institutionname']).'",'.
				'institutionname2 = '.($postData['institutionname2']?'"'.$this->cleanInStr($postData['institutionname2']).'"':'NULL').','.
				'address1 = '.($postData['address1']?'"'.$this->cleanInStr($postData['address1']).'"':'NULL').','.
				'address2 = '.($postData['address2']?'"'.$this->cleanInStr($postData['address2']).'"':'NULL').','.
				'city = '.($postData['city']?'"'.$this->cleanInStr($postData['city']).'"':'NULL').','.
				'stateprovince = '.($postData['stateprovince']?'"'.$this->cleanInStr($postData['stateprovince']).'"':'NULL').','.
				'postalcode = '.($postData['postalcode']?'"'.$this->cleanInStr($postData['postalcode']).'"':'NULL').','.
				'country = '.($postData['country']?'"'.$this->cleanInStr($postData['country']).'"':'NULL').','.
				'phone = '.($postData['phone']?'"'.$this->cleanInStr($postData['phone']).'"':'NULL').','.
				'contact = '.($postData['contact']?'"'.$this->cleanInStr($postData['contact']).'"':'NULL').','.
				'email = '.($postData['email']?'"'.$this->cleanInStr($postData['email']).'"':'NULL').','.
				'url = '.($postData['url']?'"'.$this->cleanInStr($postData['url']).'"':'NULL').','.
				'notes = '.($postData['notes']?'"'.$this->cleanInStr($postData['notes']).'"':'NULL').' '.
				'WHERE iid = '.$postData['iid'];
			//echo "<div>$sql</div>"; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR editing institution: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function submitInstitutionAdd($postData){
		$newIID = 0;
		$sql = 'INSERT INTO institutions (institutioncode, institutionname, institutionname2, address1, address2, city, '.
			'stateprovince, postalcode, country, phone, contact, email, url, notes) '.
			'VALUES ("'.$postData['institutioncode'].'","'.
			$this->cleanInStr($postData['institutionname']).'",'.
			($postData['institutionname2']?'"'.$this->cleanInStr($postData['institutionname2']).'"':'NULL').','.
			($postData['address1']?'"'.$this->cleanInStr($postData['address1']).'"':'NULL').','.
			($postData['address2']?'"'.$this->cleanInStr($postData['address2']).'"':'NULL').','.
			($postData['city']?'"'.$this->cleanInStr($postData['city']).'"':'NULL').','.
			($postData['stateprovince']?'"'.$this->cleanInStr($postData['stateprovince']).'"':'NULL').','.
			($postData['postalcode']?'"'.$this->cleanInStr($postData['postalcode']).'"':'NULL').','.
			($postData['country']?'"'.$this->cleanInStr($postData['country']).'"':'NULL').','.
			($postData['phone']?'"'.$this->cleanInStr($postData['phone']).'"':'NULL').','.
			($postData['contact']?'"'.$this->cleanInStr($postData['contact']).'"':'NULL').','.
			($postData['email']?'"'.$this->cleanInStr($postData['email']).'"':'NULL').','.
			($postData['url']?'"'.$postData['url'].'"':'NULL').','.
			($postData['notes']?'"'.$this->cleanInStr($postData['notes']).'"':'NULL').') ';
		//echo "<div>$sql</div>"; exit;
		if($this->conn->query($sql)){
			$newIID = $this->conn->insert_id;
			if($newIID && $postData['targetcollid']){
				$sql2 = 'UPDATE omcollections SET iid = '.$newIID.' WHERE (iid IS NULL) AND (collid = '.$postData['targetcollid'].')';
				$this->conn->query($sql2);
			}
		}
		else{
			$this->errorStr = 'ERROR creating institution: '.$this->conn->error;
		}
		return $newIID;
	}

	public function deleteInstitution($delIid){
		$status = true;
		//Check to see if record is linked to collections
		$sql = 'SELECT collid, CONCAT_WS(" ",CollectionName,CONCAT(InstitutionCode,IFNULL(CONCAT(":",CollectionCode),""))) AS name '.
			'FROM omcollections WHERE iid = '.$delIid.' ORDER BY CollectionName,InstitutionCode,CollectionCode';
		//echo $sql;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$status = false;
			$this->errorStr = 'ERROR deleting institution: Following collections need to be unlinked to institution before deletion is allowed';
			$this->errorStr .= '<ul style="margin-left:20px">';
			while($r = $rs->fetch_object()){
				$this->errorStr .= '<li>'.$r->name.'</li>';
			}
			$this->errorStr .= '</ul><br/>';
		}
		$rs->free();
		if(!$status) return false;

		//Check outgoing and incoming loans
		$sql = 'SELECT loanid '.
			'FROM omoccurloans '.
			'WHERE iidOwner = '.$delIid.' OR iidBorrower = '.$delIid;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$status = false;
			$this->errorStr = 'ERROR deleting institution: Institution is linked to '.$rs->num_rows.' loan records';
		}
		$rs->free();

		if($status){
			//If record is not linked to other data, OK to delete
			$sql = 'DELETE FROM institutions WHERE iid = '.$delIid;
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR deleting institution: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function removeCollection($collid){
		$status = true;
		$sql = 'UPDATE omcollections SET iid = NULL WHERE collid = '.$collid;
		//echo $sql; exit;
		if(!$this->conn->query($sql)){
			$status = false;
			$this->errorStr = 'ERROR removing collection from institution: '.$this->conn->error;
		}
		return $status;
	}

	public function addCollection($collid,$iid){
		$status = true;
		if(is_numeric($collid) && is_numeric($iid)){
			$sql = 'UPDATE omcollections SET iid = '.$iid.' WHERE collid = '.$collid;
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR adding collection to institution: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function setInstitutionId($id){
		if(is_numeric($id)){
			$this->iid = $id;
		}
	}

	public function getInstitutionId(){
		return $this->iid;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	public function getInstitutionList(){
		$retArr = Array();
		$sql = 'SELECT i.iid, c.collid, i.institutioncode, i.institutionname, i.institutionname2, i.address1, i.address2, i.city, '.
			'i.stateprovince, i.postalcode, i.country, i.phone, i.contact, i.email, i.url, i.notes '.
			'FROM institutions i LEFT JOIN omcollections c ON i.iid = c.iid '.
			'ORDER BY i.institutionname, i.institutioncode';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if(isset($retArr[$r->iid])){
				$collStr = $retArr[$r->iid]['collid'].','.$r->collid;
				$retArr[$r->iid]['collid'] = $collStr;
			}
			else{
				$retArr[$r->iid] = $this->cleanOutArr($r);
			}
		}
		$rs->free();
		return $retArr;
	}

	public function getCollectionList(){
		$retArr = Array();
		$sql = 'SELECT collid, iid, CONCAT(collectionname, " (", CONCAT_WS("-",institutioncode, collectioncode),")") AS collname '.
			'FROM omcollections '.
			'ORDER BY collectionname,institutioncode';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['name'] = $r->collname;
			$retArr[$r->collid]['iid'] = $r->iid;
		}
		$rs->free();
		return $retArr;
	}

	private function cleanOutArr($inArr){
		$outArr = array();
		foreach($inArr as $k => $v){
			$outArr[$k] = $this->cleanOutStr($v);
		}
		return $outArr;
	}

 	private function cleanOutStr($str){
		$newStr = $str;
		if(isset($newStr)){
			$newStr = str_replace('"',"&quot;",$str);
			$newStr = str_replace("'","&apos;",$newStr);
		}
		//$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>