<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTaxaManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/harvestparams');

class TaxonSearchSupport{

	private $conn;

 	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon('readonly');
 	}

	public function __destruct(){
 		if(!($this->conn === false)) $this->conn->close();
	}

	public function getTaxaSuggest($queryString, $taxonType){
		$retArr = Array();
		if($queryString){
			$this->cleanQueryString($queryString);
			$sql = "";
			if($taxonType == 1){	//ANY_NAME search
			    global $LANG;
			    $sql =
			    "SELECT DISTINCT tid, CONCAT('".$LANG['SELECT_1-5'].": ',v.vernacularname) AS label, v.vernacularname AS sciname ".
			    "FROM taxavernaculars v ".
			    "WHERE v.vernacularname LIKE '%".$queryString."%' ".

			    "UNION ".

			    "SELECT DISTINCT tid, CONCAT('".$LANG['SELECT_1-2'].": ', sciname) AS label,  sciname AS value ".
			    "FROM taxa ".
			    "WHERE sciname LIKE '%".$queryString."%' AND rankid > 179 ".

			    "UNION ".

			    "SELECT DISTINCT tid, CONCAT('".$LANG['SELECT_1-3'].": ', sciname) AS label, sciname AS value ".
			    "FROM taxa ".
			    "WHERE sciname LIKE '".$queryString."%' AND rankid = 140 ".

			    "UNION ".

			    "SELECT tid, CONCAT('".$LANG['SELECT_1-4'].": ',sciname) AS label, sciname AS value ".
			    "FROM taxa ".
			    "WHERE sciname LIKE '" . $queryString . "%' AND rankid > 20 AND rankid < 180 AND rankid != 140 ";

			}
			elseif($taxonType == 3){
				//FAMILY_ONLY
				$sql = 'SELECT tid, sciname FROM taxa WHERE rankid = 140 AND sciname LIKE "' . $queryString . '%" LIMIT 30';
			}
			elseif($taxonType == 4){
				//TAXONOMIC_GROUP
				$sql = 'SELECT tid, sciname FROM taxa WHERE rankid > 20 AND rankid < 180 AND sciname LIKE "' . $queryString . '%" LIMIT 30';
			}
			elseif($taxonType == 5){
				//COMMON_NAME
				$sql = 'SELECT DISTINCT v.tid, CONCAT(v.vernacularname, " (", t.sciname, ")") AS sciname
					FROM taxavernaculars v INNER JOIN taxa t ON v.tid = t.tid
					WHERE v.vernacularname LIKE "%' . $queryString . '%" LIMIT 50';
				//$sql = 'SELECT DISTINCT tid, vernacularname AS sciname FROM taxavernaculars WHERE vernacularname LIKE "%'.$queryString.'%" LIMIT 50 ';
			}
			else{
				//SCIENTIFIC_NAME - default
				$sql = 'SELECT tid, sciname FROM taxa WHERE sciname LIKE "' . $queryString . '%" LIMIT 20';
			}
			$rs = $this->conn->query($sql);
			while ($r = $rs->fetch_object()) {
				$keys = ['id' => $r->tid, 'value' => $r->sciname];
				if (!empty($r->label))
					$keys['label'] = $r->label;
				$retArr[] = $keys;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getTaxaSuggestFilteredByRank($queryString, $rankLow, $rankHigh){
		$retArr = Array();
		if($queryString){
			$this->cleanQueryString($queryString);
			$sql = 'SELECT sciname FROM taxa WHERE (sciname LIKE "' . $queryString . '%") ';
			if(is_numeric($this->rankLow)){
				if($this->rankHigh) $sql .= 'AND (rankid BETWEEN ' . $rankLow . ' AND ' . $rankHigh . ') ';
				else $sql .= 'AND (rankid = ' . $rankLow . ') ';
			}
			$sql .= 'LIMIT 30';
			$rs = $this->conn->query($sql);
			while ($r = $rs->fetch_object()) {
				$retArr[] = $r->sciname;
			}
			$rs->free();
		}
		return $retArr;
	}

	private function cleanQueryString(&$queryString){
		$queryString = preg_replace('/[\+\=@$%]+/i', '', $queryString);
		if(strpos($queryString, ' ')){
			$queryString = str_ireplace(array('"', "'"), '_', $queryString);
			$queryString = preg_replace('/\s{1}x{1}$/i', ' _', $queryString);
			$queryString = preg_replace('/\s{1}x{1}\s{1}/i', ' _ ', $queryString);
			$queryString = str_ireplace(' x ', ' _ ', $queryString);
			$queryString = str_ireplace(' x', ' _', $queryString);
		}
	}
}
?>
