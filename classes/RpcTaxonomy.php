<?php
include_once($SERVER_ROOT . '/classes/RpcBase.php');
include_once($SERVER_ROOT . '/traits/TaxonomyTrait.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
//use TaxonomyTrait;

Language::load('collections/harvestparams');

class RpcTaxonomy extends RpcBase{

	private $taxAuthID = 1;
	private $taxonSearchType = 0;

	function __construct(){
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getTaxaSuggest($queryString, $rankLow = 0, $rankHigh = 0){
		$retArr = Array();
		if($queryString){
			$this->cleanQueryString($queryString);
			if(!$this->taxonSearchType && !empty($GLOBALS['DEFAULT_TAXON_SEARCH'])){
				$this->taxonSearchType = $GLOBALS['DEFAULT_TAXON_SEARCH'];
			}
			$sql = '';
			$paramArr = array();
			$typeStr = '';
			if($this->taxonSearchType == 1){	//ANY_NAME search
				global $LANG;
				$sql = 'SELECT DISTINCT tid, CONCAT("' . $LANG['SELECT_1-5'] . ': ", v.vernacularname) AS label, v.vernacularname AS sciname, "" as author, "" as kingdomName
				FROM taxavernaculars v WHERE v.vernacularname LIKE ?
				UNION
				SELECT DISTINCT tid, CONCAT("' . $LANG['SELECT_1-2'] . ': ", sciname) AS label, sciname AS value, author, kingdomName
				FROM taxa WHERE sciname LIKE ? AND rankid > 179
				UNION
				SELECT DISTINCT tid, CONCAT("' . $LANG['SELECT_1-3'] . ': ", sciname) AS label, sciname AS value, author, kingdomName
				FROM taxa WHERE sciname LIKE ? AND rankid = 140
				UNION
				SELECT tid, CONCAT("' . $LANG['SELECT_1-4'] . ': ",sciname) AS label, sciname AS value, author, kingdomName
				FROM taxa WHERE sciname LIKE ? AND rankid > 20 AND rankid < 180 AND rankid != 140 ';
				$paramArr[] = '%' . $queryString . '%';
				$paramArr[] = '%' . $queryString . '%';
				$paramArr[] = $queryString . '%';
				$paramArr[] = $queryString . '%';
				$typeStr = 'ssss';
			}
			elseif($this->taxonSearchType == 5){
				//COMMON_NAME
				$sql = 'SELECT DISTINCT v.tid, CONCAT(v.vernacularname, " (", t.sciname, ")") AS sciname, "" as author, t.kingdomName
					FROM taxavernaculars v INNER JOIN taxa t ON v.tid = t.tid
					WHERE v.vernacularname LIKE ? ';
				$paramArr[] = '%' . $queryString . '%';
				$typeStr = 's';
			}
			else{
				//SCIENTIFIC_NAME - default
				$sql = 'SELECT tid, sciname, cultivarEpithet, tradeName, author, kingdomName FROM taxa WHERE sciname LIKE ? ';
				$paramArr[] = $queryString . '%';
				$typeStr = 's';
			}
			if($this->taxonSearchType == 3){
				//FAMILY_ONLY
				$rankLow = 140;
				$rankHigh = 140;
			}
			elseif($this->taxonSearchType == 4){
				//TAXONOMIC_GROUP
				$rankLow = 11;
				$rankHigh = 179;
			}
			if($rankLow || $rankHigh){
				if(is_numeric($rankLow) || is_numeric($rankHigh)){
					if($rankLow == $rankHigh){
						$sql .= 'AND rankid = ? ';
						$paramArr[] = $rankLow;
						$typeStr .= 'i';
					}
					else{
						if($rankLow){
							$sql .= 'AND (rankid >= ?) ';
							$paramArr[] = $rankLow;
							$typeStr .= 'i';
						}
						if($rankHigh){
							$sql .= 'AND (rankid <= ?) ';
							$paramArr[] = $rankHigh;
							$typeStr .= 'i';
						}
					}
				}
			}
			if($stmt = $this->conn->prepare($sql)){
				$homonymSupportIndex = 0;
				if(!empty($GLOBALS['HOMONYM_SUPPORT'])) $homonymSupportIndex = $GLOBALS['HOMONYM_SUPPORT'];
				$stmt->bind_param($typeStr, ...$paramArr);
				$stmt->execute();
				$rs = $stmt->get_result();
				while ($r = $rs->fetch_object()) {
					$value = $r->sciname;
					$label = $r->sciname;
					if(!empty($r->tradeName)){
						$label = str_replace($r->tradeName, '', $label);
					}
					if(!empty($r->cultivarEpithet)){
						// @TODO could possibly replace off-target if cultivarEpithet matches some parent taxon exactly. We think extremely unlikely edge case, so ignoring for now.
						$label = str_replace("'" . $r->cultivarEpithet . "'", '', trim($label));
					}
					if(!empty($r->author)){
						if($homonymSupportIndex == 1 || $homonymSupportIndex == 3){
							$label = trim($label) . ' ' . $r->author;
							$value = $label . ' [' . $r->tid . ']';
						}
					}
					if(!empty($r->cultivarEpithet)){
						$label .= ' ' . $this->standardizeCultivarEpithet($r->cultivarEpithet);
					}
					if(!empty($r->tradeName)){
						$label .= ' ' . $this->standardizeTradeName($r->tradeName);
					}
					if(!empty($r->kingdomName)){
						if($homonymSupportIndex == 2 || $homonymSupportIndex == 3){
							$label .= ' - ' . $r->kingdomName;
							$value = $label . ' [' . $r->tid . ']';
						}
					}
					if (!empty($r->label)){
						$label = $r->label;
					}
					$keys = ['id' => $r->tid, 'value' => $value, 'label' => $label];
					$retArr[] = $keys;
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	private function cleanQueryString(&$queryString){
		$queryString = preg_replace('/[\+\=@$%]+/i', '', $queryString);
		if(strpos($queryString, ' ')){
			//Function replaces hybrid and other poorly standardized input to wildcard single character matches to improve return options
			$queryString = str_ireplace(array('"', "'"), '_', $queryString);
			$queryString = preg_replace('/\s{1}x{1}$/i', ' _', $queryString);
			$queryString = preg_replace('/\s{1}x{1}\s{1}/i', ' _ ', $queryString);
			$queryString = str_ireplace(' x ', ' _ ', $queryString);
			$queryString = str_ireplace(' x', ' _', $queryString);
		}
	}

	public function getTaxon($sciname){
		$retArr = array();
		$sql = 'SELECT tid, sciname, author, kingdomName FROM taxa WHERE (sciname = ?)';
		if(preg_match('/\s{1}\D{1}\s{1}/i',$sciname)){
			//Replace various formats of hybrid designation with a wildcard single character search
			$sciname = preg_replace('/\s{1}x{1}\s{1}|\s{1}×{1}\s{1}/i', ' _ ', $sciname);
			$sql = 'SELECT tid, sciname, author, kingdomName FROM taxa WHERE (sciname LIKE ?)';
		}
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('s', $sciname);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_object()){
				$retArr[$r->tid]['tid'] = $r->tid;
				$retArr[$r->tid]['sciname'] = $r->sciname;
				$retArr[$r->tid]['author'] = $r->author;
				$retArr[$r->tid]['kingdom'] = $r->kingdomName;
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	public function getTid($sciName, $rankid, $author){
		$tid = 0;
		//Sanitation
		if(!is_numeric($rankid)) $rankid = 0;
		$paramArr = array($this->taxAuthID, $sciName, $sciName);
		$typeStr = 'iss';
		$sql = 'SELECT t.tid FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid WHERE (ts.taxauthid = ?) AND (t.sciname = ? OR CONCAT(t.sciname," ",t.author) = ?) ';
		if($rankid){
			$sql .= 'AND t.rankid = ? ';
			$paramArr[] = $rankid;
			$typeStr .= 'i';
		}
		if($author){
			$sql .= 'AND t.author = ? ';
			$paramArr[] = $author;
			$typeStr .= 's';
		}
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_object()){
				$tid = $r->tid;
			}
			$rs->free();
			$stmt->close();
		}
		return $tid;
	}

	public function getAcceptedTaxa($queryTerm){
		$retArr = Array();
		$queryTerm .= '%';
		$sql = 'SELECT t.tid, t.sciname, t.cultivarEpithet, t.tradeName, t.author
			FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			WHERE (ts.taxauthid = ?) AND (ts.tid = ts.tidaccepted) AND (t.sciname LIKE ?)
			ORDER BY t.sciname LIMIT 20';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('is', $this->taxAuthID, $queryTerm);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_object()){
				$sciname = $r->sciname; //.' '.$r->author;
				if(!empty($r->tradeName)){
					$sciname = str_replace($r->tradeName, '', $sciname);
				}

				if(!empty($r->cultivarEpithet)){
					$sciname = str_replace("'" . $r->cultivarEpithet . "'", '', trim($sciname)); // @TODO could possibly replace off-target if cultivarEpithet matches some parent taxon exactly. We think extremely unlikely edge case, so ignoring for now.
				}

				if(!empty($r->author)){
					$sciname = trim($sciname) . ' ' . $r->author;
				}
				if(!empty($r->cultivarEpithet)){
					$sciname .= " " . $this->standardizeCultivarEpithet($r->cultivarEpithet);
				}
				if(!empty($r->tradeName)){
					$sciname .= ' ' . $this->standardizeTradeName($r->tradeName);
				}
				$retArr[] = array('id' => $r->tid,'label' => $sciname);
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	public function getDynamicChildren($objId, $targetId, $displayAuthor, $limitToOccurrences, $isEditor){
		$retArr = Array();
		$childArr = Array();
		//Sanitation
		if(!is_numeric($targetId)) $targetId = 0;
		if(!is_numeric($displayAuthor)) $displayAuthor = 0;
		if(!is_numeric($isEditor)) $isEditor = 0;

		//Set rank array
		$taxonUnitArr = array(1 => 'Organism',10 => 'Kingdom');
		$sqlR = 'SELECT rankid, rankname FROM taxonunits';
		$rsR = $this->conn->query($sqlR);
		while($rR = $rsR->fetch_object()){
			$taxonUnitArr[$rR->rankid] = $rR->rankname;
		}
		$rsR->free();

		$urlPrefix = '../index.php?taxon=';
		if($isEditor) $urlPrefix = 'taxoneditor.php?tid=';

		if($objId == 'root'){
			$retArr['id'] = 'root';
			$retArr['label'] = 'root';
			$retArr['name'] = 'root';
			if($isEditor) $retArr['url'] = 'taxoneditor.php';
			else $retArr['url'] = '../index.php';
			$retArr['children'] = Array();
			$lowestRank = '';
			$sql = 'SELECT MIN(t.RankId) AS RankId FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid WHERE (t.rankid != 0) AND (ts.taxauthid = ?) LIMIT 1 ';
			//echo $sql.'<br>';
			if ($statement = $this->conn->prepare($sql)) {
				$statement->bind_param("i", $this->taxAuthID);
				$statement->execute();
				$result = $statement->get_result();
				while($row = $result->fetch_object()){
					$lowestRank = $row->RankId;
				}
				$result->free();
				$statement->close();
			}
			$sql1 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid FROM taxa t LEFT JOIN taxstatus ts ON t.tid = ts.tid WHERE ts.taxauthid = ? AND t.RankId = ? ';
			//echo "<div>".$sql1."</div>";

			if ($statement1 = $this->conn->prepare($sql1)) {
				$i = 0;
				$statement1->bind_param("ii", $this->taxAuthID, $lowestRank);
				$statement1->execute();
				$result1 = $statement1->get_result();
				while($row1 = $result1->fetch_object()){
					$rankName = (isset($taxonUnitArr[$row1->rankid]) ? $taxonUnitArr[$row1->rankid] : 'Unknown');
					$label = '2-' . $row1->rankid . '-' . $rankName.'-' . $row1->sciname;
					$sciName = $row1->sciname;
					if($row1->tid == $targetId) $sciName = '<b>' . $sciName . '</b>';
					$sciName = "<span style='font-size:75%;'>" . $rankName . ":</span> " . $sciName . ($displayAuthor ? " " . $row1->author : "");
					$childArr[$i]['id'] = $row1->tid;
					$childArr[$i]['label'] = $label;
					$childArr[$i]['name'] = $sciName;
					$childArr[$i]['url'] = $urlPrefix.$row1->tid;
					$sql3 = 'SELECT tid FROM taxaenumtree WHERE taxauthid = ? AND parenttid = ? LIMIT 1 ';
					//echo "<div>".$sql3."</div>";
					if ($statement3 = $this->conn->prepare($sql3)) {
						$statement3->bind_param("ii", $this->taxAuthID, $row1->tid);
						$statement3->execute();
						$result3 = $statement3->get_result();
						if($row3 = $result3->fetch_object()){
							$childArr[$i]['children'] = true;
						}
						else{
							$sql4 = 'SELECT DISTINCT tid, tidaccepted FROM taxstatus WHERE (taxauthid = ?) AND (tidaccepted = ?) ';
							//echo "<div>".$sql4."</div>";
							if ($statement4 = $this->conn->prepare($sql4)) {
								$statement4->bind_param("ii", $this->taxAuthID, $row1->tid);
								$statement4->execute();
								$result4 = $statement4->get_result();
								while($row4 = $result4->fetch_object()){
									if($row4->tid != $row4->tidaccepted){
										$childArr[$i]['children'] = true;
									}
								}
								$result4->free();
								$statement4->close();
							}
						}
						$result3->free();
						$statement3->close();
					}
					$i++;
				}
				$result1->free();
				$statement1->close();
			}
		}
		else{
			$objId = filter_var($objId, FILTER_SANITIZE_NUMBER_INT);
			//Get children, but only accepted children
			$sql = 'SELECT DISTINCT t.tid, t.sciname, t.cultivarEpithet, t.tradeName, t.author, t.rankid FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid ';
			if($limitToOccurrences) $sql .= 'INNER JOIN taxaenumtree e ON t.tid = e.parenttid INNER JOIN omoccurrences o ON e.tid = o.tidInterpreted ';
			$sql .=	'WHERE (ts.taxauthid = ?) AND (ts.tid = ts.tidaccepted) AND ((ts.parenttid = ?) OR (t.tid = ?)) ';
			//echo $sql.'<br>';
			if ($statement = $this->conn->prepare($sql)) {
				$statement->bind_param("iii", $this->taxAuthID, $objId, $objId);
				$statement->execute();
				$result = $statement->get_result();
				$i = 0;
				while($r = $result->fetch_object()){
					$rankName = (isset($taxonUnitArr[$r->rankid]) ? $taxonUnitArr[$r->rankid] : 'Unknown');
					$label = '2-'.$r->rankid.'-'.$rankName.'-'.$r->sciname;
					$sciNameParts = $this->splitScinameByProvided($r->sciname, $r->cultivarEpithet, $r->tradeName, $r->author);
                    $sciName = $sciNameParts['base'];
                    if($r->rankid >= 180) $sciName = '<i>' . $sciName . '</i>';
                    $sciName .= $displayAuthor ? " " . $r->author : "";
                    if(!empty($sciNameParts['cultivarEpithet'])) $sciName .= " '" . $sciNameParts['cultivarEpithet'] . "'";
                    if(!empty($sciNameParts['tradeName'])) $sciName .= " " . $sciNameParts['tradeName'];
                    if($r->tid == $targetId) $sciName = '<b>' . $sciName . '</b>';
                    $sciName = "<span style='font-size:75%;'>" . $rankName . ":</span> " . $sciName;
					if($r->tid == $objId){
						$retArr['id'] = $r->tid;
						$retArr['label'] = $label;
						$retArr['name'] = $sciName;
						$retArr['url'] = $urlPrefix.$r->tid;
						$retArr['children'] = Array();
					}
					else{
						$childArr[$i]['id'] = $r->tid;
						$childArr[$i]['label'] = $label;
						$childArr[$i]['name'] = $sciName;
						$childArr[$i]['url'] = $urlPrefix.$r->tid;
						$sql3 = 'SELECT tid FROM taxaenumtree WHERE taxauthid = ? AND parenttid = ? LIMIT 1 ';
						//echo 'sql3: '.$sql3.'<br/>';
						if ($statement3 = $this->conn->prepare($sql3)) {
							$statement3->bind_param("ii", $this->taxAuthID, $r->tid);
							$statement3->execute();
							$result3 = $statement3->get_result();
							if($row3 = $result3->fetch_object()){
								$childArr[$i]['children'] = true;
							}
							else{
								$sql4 = 'SELECT DISTINCT tid, tidaccepted FROM taxstatus WHERE taxauthid = ? AND tidaccepted = ? ';
								//echo 'sql4: '.$sql4.'<br/>';
								if ($statement4 = $this->conn->prepare($sql4)) {
									$statement4->bind_param("ii", $this->taxAuthID, $r->tid);
									$statement4->execute();
									$result4 = $statement4->get_result();
									while($row4 = $result4->fetch_object()){
										if($row4->tid != $row4->tidaccepted){
											$childArr[$i]['children'] = true;
										}
									}
									$result4->free();
									$statement4->close();
								}
							}
							$result3->free();
							$statement3->close();
						}
						$i++;
					}
				}
				$result->free();
				$statement->close();
			}

			//Get synonyms for all accepted taxa
			$sqlSyns = 'SELECT DISTINCT t.tid, t.sciname, t.cultivarEpithet, t.tradeName, t.author, t.rankid '.
				'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
				'WHERE (ts.tid <> ts.tidaccepted) AND (ts.taxauthid = ?) AND (ts.tidaccepted = ?)';
			//echo 'syn: '.$sqlSyns.'<br/>';
			if ($statementSyns = $this->conn->prepare($sqlSyns)) {
				$statementSyns->bind_param("ii", $this->taxAuthID, $objId);
				$statementSyns->execute();
				$resultSyns = $statementSyns->get_result();
				while($row = $resultSyns->fetch_object()){
					$rankName = (isset($taxonUnitArr[$row->rankid]) ? $taxonUnitArr[$row->rankid] : 'Unknown');
					$label = '1-' . $row->rankid . '-' . $rankName . '-' . $row->sciname;
					$sciNameParts = $this->splitScinameByProvided($row->sciname, $row->cultivarEpithet, $row->tradeName, $row->author);
                    $sciName = $sciNameParts['base'];
                    if($row->rankid >= 180) $sciName = '[<i>' . $sciName . '</i>]';
                    $sciName .= $displayAuthor ? " " . $row->author : "";
                    if(!empty($sciNameParts['cultivarEpithet'])) $sciName .= " '" . $sciNameParts['cultivarEpithet'] . "'";
                    if(!empty($sciNameParts['tradeName'])) $sciName .= " " . $sciNameParts['tradeName'];
                    if($row->tid == $targetId) $sciName = '<b>' . $sciName . '</b>';
					$childArr[$i]['id'] = $row->tid;
					$childArr[$i]['label'] = $label;
					$childArr[$i]['name'] = $sciName;
					$childArr[$i]['url'] = $urlPrefix.$row->tid;
					$i++;
				}
				$resultSyns->free();
				$statementSyns->close();
			}
		}

		usort($childArr, function ($a,$b){ return strnatcmp($a['label'],$b['label']);} );
		$retArr['children'] = $childArr;
		return $retArr;
	}

	//Setters and getters
	public function setTaxAuthId($id){
		if(is_numeric($id)) $this->taxAuthID = $id;
	}

	public function setTaxonSearchType($searchType){
		if(is_numeric($searchType)) $this->taxonSearchType = $searchType;
	}

	public function isValidApiCall(){
		//Verification also happening within haddler checking is user is logged in and a valid admin/editor
		$status = parent::isValidApiCall();
		if(!$status) return false;
		return true;
	}
}
?>
