<?php
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/misc/collstats');

class OccurrenceSearchSupport {

	private $conn;
	private $collidStr = '';

	public function __construct($conn){
		$this->conn = $conn;
 	}

	public function __destruct(){
	}

	public function getFullCollectionList($catIdStr = '', $limitByImages = false){
		if(!preg_match('/^[,\d]+$/',$catIdStr)) $catIdStr = '';
		//Set collection array
		/*
		$collIdArr = array();
		if($this->collidStr){
			$cArr = explode(';',$this->collidStr);
			$collIdArr = explode(',',$cArr[0]);
			if(isset($cArr[1])) $collIdArr = $cArr[1];
		}
		*/
		//Set collections
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk, '.
			'cat.category, cat.icon AS caticon, cat.acronym '.
			'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
			'LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid '.
			'LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk '.
			'WHERE s.recordcnt > 0 AND (cat.inclusive IS NULL OR cat.inclusive = 1 OR cat.ccpk = 1) ';
		if($limitByImages) $sql .= 'AND s.dynamicproperties NOT LIKE \'%imgcnt":"0"%\' ';
		$sql .= 'ORDER BY ccl.sortsequence, cat.category, c.sortseq, c.CollectionName ';
		//echo "<div>SQL: ".$sql."</div>";
		$result = $this->conn->query($sql);
		$collArr = array();
		while($r = $result->fetch_object()){
			$collType = (stripos($r->colltype, "observation") !== false?'obs':'spec');
			if($r->ccpk){
				if(!isset($collArr[$collType]['cat'][$r->ccpk]['name'])){
					$collArr[$collType]['cat'][$r->ccpk]['name'] = $r->category;
					$collArr[$collType]['cat'][$r->ccpk]['icon'] = $r->caticon;
					$collArr[$collType]['cat'][$r->ccpk]['acronym'] = $r->acronym;
				}
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["instcode"] = $r->institutioncode;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collcode"] = $r->collectioncode;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collname"] = $r->collectionname;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["icon"] = $r->icon;
			}
			else{
				$collArr[$collType]['coll'][$r->collid]["instcode"] = $r->institutioncode;
				$collArr[$collType]['coll'][$r->collid]["collcode"] = $r->collectioncode;
				$collArr[$collType]['coll'][$r->collid]["collname"] = $r->collectionname;
				$collArr[$collType]['coll'][$r->collid]["icon"] = $r->icon;
			}
		}
		$result->free();

		$retArr = array();
		//Modify sort so that default catid is first
		if($catIdStr){
			$catIdArr = explode(',', $catIdStr);
			if($catIdArr){
				foreach($catIdArr as $catId){
					if(isset($collArr['spec']['cat'][$catId])){
						$retArr['spec']['cat'][$catId] = $collArr['spec']['cat'][$catId];
						unset($collArr['spec']['cat'][$catId]);
					}
					elseif(isset($collArr['obs']['cat'][$catId])){
						$retArr['obs']['cat'][$catId] = $collArr['obs']['cat'][$catId];
						unset($collArr['obs']['cat'][$catId]);
					}
				}
			}
		}
		foreach($collArr as $t => $tArr){
			foreach($tArr as $g => $gArr){
				foreach($gArr as $id => $idArr){
					$retArr[$t][$g][$id] = $idArr;
				}
			}
		}
		return $retArr;
	}

	public static function getDbRequestVariable(){
		$dbStr = '';
		if(isset($_REQUEST['db'])){
			$dbInput = $_REQUEST['db'];
			if(is_array($dbInput)){
				if(in_array('all', $dbInput)) $dbStr = 'all';
				elseif(in_array('allspec', $dbInput)) $dbStr = 'allspec';
				elseif(in_array('allobs', $dbInput)) $dbStr = 'allobs';
				else{
					$dbArr = array_unique($dbInput);
					$dbStr = implode(',', $dbArr);
				}
			}
			else{
				//Input is a string
				if(strpos($dbStr,'all') !== false) $dbStr = 'all';
				elseif(strpos($dbStr,'allspec') !== false) $dbStr = 'allspec';
				elseif(strpos($dbStr,'allobs') !== false) $dbStr = 'allobs';
				else $dbStr = $dbInput;
			}
		}
		if(($p = strpos($dbStr, ';')) !== false){
			$dbStr = substr($dbStr, 0, $p);
		}
		if(strpos($dbStr, "'")) $dbStr = '0';		//SQL Injection attempt, thus set to return nothing rather than a query that puts a load on the db server
		elseif(!preg_match('/^[a-z0-9,;]+$/', $dbStr)) $dbStr = 'all';
		return $dbStr;
	}

	public static function getDbWhereFrag($dbSearchTerm){
		$sqlRet = "";
		//Do nothing if db = all
		if($dbSearchTerm != 'all'){
			if($dbSearchTerm == 'allspec'){
				$sqlRet .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype IN("Preserved Specimens","Fossil Specimens"))) ';
			}
			elseif($dbSearchTerm == 'allobs'){
				$sqlRet .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype IN("General Observations","Observations"))) ';
			} else {
				// Check in case there is ; inside dbSearchTerm
				$dbArr = explode(';',$dbSearchTerm);
				$dbStr = "o.collid IN(" . (is_array($dbArr)? implode(',', $dbArr): $dbArr) . ")";
				$sqlRet .= 'AND ('.$dbStr.') ';
			}
		}
		return $sqlRet;
	}

	public function setCollidStr($str){
		$this->collidStr = $str;
	}
}
?>
