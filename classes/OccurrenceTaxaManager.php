<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/harvestparams');

abstract class TaxaSearchType {
	const  ANY_NAME				= 1;
	const  SCIENTIFIC_NAME		= 2;
	const  FAMILY_ONLY			= 3;
	const  TAXONOMIC_GROUP		= 4;
	const  COMMON_NAME			= 5;

	public static $_list		   = array(1,2,3,4,5);

	public static function anyNameSearchTag ( $taxaSearchType ) {
		global $LANG;
		$key = 'SELECT_1-'.$taxaSearchType;
		if (array_key_exists($key,$LANG)) {
			return $LANG[$key];
		}
		return "Unsupported";
	}

	public static function taxaSearchTypeFromAnyNameSearchTag ( $searchTag ) {
		foreach (TaxaSearchType::$_list as $taxaSearchType) {
			if (TaxaSearchType::anyNameSearchTag($taxaSearchType) == $searchTag) {
				return $taxaSearchType;
			}
		}
		return 3;
	}
}

class OccurrenceTaxaManager {

	protected $conn	= null;
	protected $taxaArr = array();
	protected $associationArr = array();
	protected $taxAuthId = 1;
	protected $exactMatchOnly = false;

	public function __construct($type='readonly'){
		$this->conn = MySQLiConnectionFactory::getCon($type);
	}
	public function __destruct(){
		if ((!($this->conn === false)) && (!($this->conn === null))) {
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function setTaxonRequestVariable($inputArr = null, $exactMatchOnly = false){
		if($exactMatchOnly) $this->exactMatchOnly = true;
		//Set taxa search terms
		$taxaStr = $this->getTaxonInputVariable($inputArr, 'taxa');

		if($taxaStr){
			$this->taxaArr['search'] = $taxaStr;
			//Set usage of taxonomic thesaurus
			$this->taxaArr['usethes'] = $this->getInputVariable($inputArr, 'usethes');
			//Set default taxa type
			$defaultTaxaType = $this->getInputVariable($inputArr, 'taxontype');
			if(!$defaultTaxaType) $defaultTaxaType = TaxaSearchType::SCIENTIFIC_NAME;
			$this->taxaArr['taxontype'] = $defaultTaxaType;
			//Initerate through taxa and process
			$taxaSearchTerms = explode(';', $taxaStr);
			foreach($taxaSearchTerms as $k => $term){
				$searchTerm = $this->cleanInputStr($term);
				if(!$searchTerm){
					unset($taxaSearchTerms[$k]);
					continue;
				}
				$this->setTaxonAttributes($this->taxaArr, $defaultTaxaType, $searchTerm);
			}
			if($this->taxaArr['usethes']){
				$this->appendAcceptedChildren($this->taxaArr);
				$this->appendSynonyms($this->taxaArr);
			}
			//echo '<pre>'; print_r($this->taxaArr); echo '</pre>';
		}
	}

	public function getTaxonWhereFrag(){
		$sqlWhereTaxa = '';
		if(isset($this->taxaArr['taxa'])){
			$tidInArr = array();
			$taxonType = $this->taxaArr['taxontype'];
			foreach($this->taxaArr['taxa'] as $searchTaxon => $searchArr){
				$cleanedSearchTaxon = $this->cleanInStr($searchTaxon);
				if(isset($searchArr['taxontype'])) $taxonType = $searchArr['taxontype'];
				if($taxonType == TaxaSearchType::TAXONOMIC_GROUP){
					//Class, order, or other higher rank
					if(isset($searchArr['tid'])){
						$tidArr = array_keys($searchArr['tid']);
						$sqlWhereTaxa .= 'OR (e.parenttid IN('.implode(',', $tidArr).') ';
						$sqlWhereTaxa .= 'OR (e.tid IN('.implode(',', $tidArr).')) ';
						if(isset($searchArr['synonyms'])) $sqlWhereTaxa .= 'OR (e.tid IN('.implode(',',array_keys($searchArr['synonyms'])).')) ';
						$sqlWhereTaxa .= ') ';
					}
					else{
						//Unable to find higher taxon within taxonomic tree, thus return nothing
						$sqlWhereTaxa .= 'OR (o.tidinterpreted = 0) ';
					}
				}
				elseif($taxonType == TaxaSearchType::FAMILY_ONLY){
					if(isset($searchArr['tid'])){
						$tidArr = array_keys($searchArr['tid']);
						$sqlWhereTaxa .= 'OR ((ts.family = "'.$cleanedSearchTaxon.'") OR (ts.tid IN('.implode(',', $tidArr).'))) ';
					}
					else{
						$sqlWhereTaxa .= 'OR ((o.family = "'.$cleanedSearchTaxon.'") OR (o.sciname = "'.$cleanedSearchTaxon.'")) ';
					}
				}
				else{
					if($taxonType == TaxaSearchType::COMMON_NAME){
						$famArr = $this->setCommonNameWhereTerms($searchArr, $tidInArr);
						if($famArr) $sqlWhereTaxa .= 'OR (o.family IN("'.implode('","',$famArr).'")) ';
					}
					if(isset($searchArr['TID_BATCH'])){
						$tidInArr = array_merge($tidInArr, array_keys($searchArr['TID_BATCH']));
						if(isset($searchArr['tid'])) $tidInArr = array_merge($tidInArr, array_keys($searchArr['tid']));
					}
					else{
						$term = $this->cleanInStr(trim($searchTaxon,'%'));
						//$term = preg_replace(array('/\s{1}x\s{1}/','/\s{1}X\s{1}/','/\s{1}\x{00D7}\s{1}/u'), ' _ ', $term);
						if(array_key_exists('tid', $searchArr)){
							//Term was located within the taxonomic thesaurus
							$rankid = current($searchArr['tid']);
							$tidArr = array_keys($searchArr['tid']);
							$tidInArr = array_merge($tidInArr, $tidArr);
							if($rankid > 179){
								//Return matches that are not linked to thesaurus
								//if($this->exactMatchOnly) $sqlWhereTaxa .= 'OR (o.sciname = "' . $term . '") ';
								//else $sqlWhereTaxa .= 'OR (o.sciname LIKE "' . $term . '%") ';
							}
						}
						else{
							//Protect against someone trying to download big pieces of the occurrence table through the user interface
							if(strlen($term) < 4) $term .= ' ';
							/*
							if(strpos($term, ' ') || strpos($term, '%')){
								//Return matches for "Pinus a"
								$sqlWhereTaxa .= "OR (o.sciname LIKE '" . $term . "%') ";
							}
							else{
								$sqlWhereTaxa .= "OR (o.sciname LIKE '" . $term . " %') ";
							}
							*/
							if($this->exactMatchOnly){
								$sqlWhereTaxa .= 'OR (o.sciname = "' . $term . '") ';
							}
							else{
								$sqlWhereTaxa .= 'OR (o.sciname LIKE "' . $term . '%") ';
								/*
								if(!strpos($term,' _ ')){
									//Accommodate for formats of hybrid designations within input and target data (e.g. x, multiplication sign, etc)
									//$term2 = preg_replace('/^([^\s]+\s{1})/', '$1 _ ', $term);
									//$sqlWhereTaxa .= 'OR (o.sciname LIKE "' . $term2 . '%") ';
								}
								*/
							}
						}
					}
					if(array_key_exists('synonyms',$searchArr)){
						$synArr = $searchArr['synonyms'];
						if($synArr){
							if($taxonType == TaxaSearchType::SCIENTIFIC_NAME || $taxonType == TaxaSearchType::COMMON_NAME){
								foreach($synArr as $synTid => $sciName){
									if(strpos($sciName,'aceae') || strpos($sciName,'idae')){
										$sqlWhereTaxa .= 'OR (o.family = "' . $sciName . '") ';
									}
								}
							}
							//$sqlWhereTaxa .= 'OR (o.tidinterpreted IN('.implode(',',array_keys($synArr)).')) ';
							$tidInArr = array_merge($tidInArr,array_keys($synArr));
						}
					}
				}
			}
			if($tidInArr) $sqlWhereTaxa .= 'OR (o.tidinterpreted IN('.implode(',',array_unique($tidInArr)).')) ';
			$sqlWhereTaxa = 'AND ('.trim(substr($sqlWhereTaxa,3)).') ';
			if(strpos($sqlWhereTaxa,'e.parenttid')) $sqlWhereTaxa .= 'AND (e.taxauthid = '.$this->taxAuthId.') ';
			if(strpos($sqlWhereTaxa,'ts.family')) $sqlWhereTaxa .= 'AND (ts.taxauthid = '.$this->taxAuthId.') ';
		}
		//echo 'sql: ' . $sqlWhereTaxa . '<br><br>'; exit;
		if($sqlWhereTaxa) return $sqlWhereTaxa;
		else return false;
	}

	protected function setCommonNameWhereTerms($searchArr, &$tidInArr){
		$famArr = array();
		if(array_key_exists('families',$searchArr)){
			$famArr = $searchArr['families'];
		}
		if(array_key_exists('tid',$searchArr)){
			$tidArr = array();
			foreach($searchArr['tid'] as $tid => $rankid){
				$tidInArr[] = $tid;  //add tid to search records at that rank
				if($rankid <= 140) $tidArr[] = $tid;
			}
			if($tidArr){
				$tidStr = implode(',', $tidArr);
				$sql = 'SELECT DISTINCT t.sciname '.
					'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
					'WHERE (t.rankid = 140) AND (t.tid IN(' . $tidStr . ')) OR ((e.taxauthid = ' . $this->taxAuthId . ') AND (e.parenttid IN(' . $tidStr . ')))';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$famArr[] = $r->sciname;
				}
				$rs->free();
			}
		}
		return array_unique($famArr);
	}

	//Associations functions
	public function setAssociationRequestVariable($inputArr = null, $exactMatchOnly = false){
		if($exactMatchOnly) $this->exactMatchOnly = true;

		//sanitize
		$associationTypeStr = $this->getInputVariable($inputArr, 'association-type');
		if($associationTypeStr){
			$this->associationArr['relationship'] = $associationTypeStr;
		}

		$associatedTaxonStr = $this->getTaxonInputVariable($inputArr, 'associated-taxa');
		if($associatedTaxonStr){
			$this->associationArr['search'] = $associatedTaxonStr;

			//Set Association Use Thes
			$this->associationArr['usethes-associations'] = $this->getInputVariable($inputArr, 'usethes-associations');

			//Set Association Default Taxa Type
			$defaultTaxaType = $this->getInputVariable($inputArr, 'taxontype-association');
			if(!$defaultTaxaType) $defaultTaxaType = TaxaSearchType::SCIENTIFIC_NAME;
			$this->associationArr['associated-taxa'] = $defaultTaxaType;

			$associationTaxaSearchTerms = explode(',',$associatedTaxonStr);
			foreach($associationTaxaSearchTerms as $searchTermkey => $term){
				$searchTerm = $this->cleanInputStr($term);
				if(!$searchTerm){
					unset($associationTaxaSearchTerms);
					continue;
				}
				//Process Single Term
				$associationTaxaSearchTerms[$searchTermkey] = $searchTerm;
				$this->setTaxonAttributes($this->associationArr, $defaultTaxaType, $searchTerm);
			}
			if($this->associationArr['usethes-associations']){
				$this->appendAcceptedChildren($this->associationArr);
				$this->appendSynonyms($this->associationArr);
			}
		}
	}

	//Shared functions
	private function getTaxonInputVariable($inputArr, $variableName){
		$taxaStr = '';
		if(!empty($inputArr[$variableName])){
			$taxaStr = $inputArr[$variableName];
		}
		elseif(!empty($_REQUEST[$variableName])){
			$taxaStr = $_REQUEST[$variableName];
		}
		$taxaStr = str_replace('_', ' ',$taxaStr);
		return $taxaStr;
	}

	private function getInputVariable($inputArr, $variableName){
		if(isset($inputArr[$variableName]) && is_numeric($inputArr[$variableName])){
			return $inputArr[$variableName];
		}
		elseif(isset($_REQUEST[$variableName]) && is_numeric($_REQUEST[$variableName])){
			return $_REQUEST[$variableName];
		}
		return 0;
	}

	private function setTaxonAttributes(&$targetTaxaArr, $defaultTaxaType, $searchTerm){
		//Iterates through individual search terms and populates tid, rankid, etc
		$useThes = $targetTaxaArr['usethes'];
		$taxaType = $defaultTaxaType;
		if($defaultTaxaType == TaxaSearchType::ANY_NAME) {
			//Extract search term from label tags
			$n = explode(': ',$searchTerm);
			if (count($n) > 1) {
				$taxaType = TaxaSearchType::taxaSearchTypeFromAnyNameSearchTag($n[0]);
				$searchTerm = $n[1];
			}
			else{
				$taxaType = TaxaSearchType::SCIENTIFIC_NAME;
			}
		}
		if($taxaType == TaxaSearchType::COMMON_NAME){
			$this->setSciNamesByVerns($searchTerm, $targetTaxaArr);
		}
		$tid = 0;
		if(is_numeric($searchTerm)){
			//Tid was supplied as search term
			$tid = $searchTerm;
			$searchTerm = '';
		}
		elseif(preg_match('/\[(\d+)\]/', $searchTerm, $m)){
			//Tid supplied via autocomplete output
			$tid = $m[1];
			$searchTerm = '';
		}
		$paramArr = array();
		$typeStr = '';
		$sql = 'SELECT t.sciname, t.tid, t.rankid FROM taxa t ';
		if($tid){
			if($useThes){
				//Use the accepted name as output on matched term
				$sql .= 'INNER JOIN taxstatus ts ON t.tid = ts.tidaccepted
					WHERE (ts.taxauthid = ?) AND (ts.tid = ?)';
				$paramArr[] = $this->taxAuthId;
				$typeStr = 'i';
			}
			else{
				$sql .= 'WHERE (t.tid = ?)';
			}
			$paramArr[] = $tid;
			$typeStr .= 'i';
		}
		else{
			if($useThes){
				//Use the accepted name as output on matched term
				$sql .= 'INNER JOIN taxstatus ts ON t.tid = ts.tidaccepted
					INNER JOIN taxa t2 ON ts.tid = t2.tid
					WHERE (ts.taxauthid = ?) AND (t2.sciname = ?)';
				$paramArr[] = $this->taxAuthId;
				$typeStr = 'i';
			}
			else{
				$sql .= 'WHERE (t.sciname = ?)';
			}
			$paramArr[] = $searchTerm;
			$typeStr .= 's';
		}
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$targetTaxaArr['taxa'][$r->sciname]['tid'][$r->tid] = $r->rankid;
					if($r->rankid == 140){
						$taxaType = TaxaSearchType::FAMILY_ONLY;
					}
					elseif($r->rankid < 180){
						$taxaType = TaxaSearchType::TAXONOMIC_GROUP;
					}
					else{
						$taxaType = TaxaSearchType::SCIENTIFIC_NAME;
					}
					$targetTaxaArr['taxa'][$r->sciname]['taxontype'] = $taxaType;
				}
				if(!$rs->num_rows){
					//No records located, thus set default search type
					$targetTaxaArr['taxa'][$searchTerm]['taxontype'] = $taxaType;
				}
				$rs->free();
			}
			$stmt->close();
		}
	}

	private function setSciNamesByVerns(&$searchTerm, $targetTaxaArr) {
		if(preg_match('/^(.+)\s{1}\((.+)\)$/', $searchTerm, $m)){
			$searchTerm = $m[2];
		}
		else{
			$sql = 'SELECT DISTINCT v.VernacularName, t.tid, t.sciname, t.rankid
				FROM taxstatus ts INNER JOIN taxavernaculars v ON ts.TID = v.TID
				INNER JOIN taxa t ON t.TID = ts.tidaccepted
				WHERE (ts.taxauthid = ?) AND (v.VernacularName IN(?))
				ORDER BY t.rankid LIMIT 10';
			if ($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('ss', $this->taxAuthId, $searchTerm);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($row = $rs->fetch_object()){
					$vernName = $row->VernacularName;
					if($row->rankid == 140){
						$targetTaxaArr['taxa'][$vernName]['families'][] = $row->sciname;
					}
					else{
						$targetTaxaArr['taxa'][$vernName]['scinames'][] = $row->sciname;
					}
					$targetTaxaArr['taxa'][$vernName]['tid'][$row->tid] = $row->rankid;
				}
				$rs->free();
				$stmt->close();
			}
		}
	}

	private function appendAcceptedChildren(&$taxaArr){
		//Get accepted children
		if(isset($taxaArr['taxa'])){
			foreach($taxaArr['taxa'] as $taxonTerm => $taxonUnitArr){
				if(isset($taxonUnitArr['tid']) && isset($taxonUnitArr['taxontype']) && $taxonUnitArr['taxontype'] == 2){
					$sql = 'SELECT DISTINCT t.tid, t.sciname, t.rankid
						FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
						INNER JOIN taxaenumtree e ON t.tid = e.tid
						WHERE t.rankid >= 220 AND (ts.TidAccepted = ts.tid) AND (e.parenttid IN(' . trim(str_repeat('?,', count($taxonUnitArr['tid'])), ', ') . ')) AND (ts.taxauthid = ?) AND (e.taxauthid = ?)' ;
					$paramArr = array_keys($taxonUnitArr['tid']);
					$typeStr = trim(str_repeat('i', count($taxonUnitArr['tid'])));
					$paramArr[] = $this->taxAuthId;
					$paramArr[] = $this->taxAuthId;
					$typeStr .= 'ii';
					if($stmt = $this->conn->prepare($sql)){
						$stmt->bind_param($typeStr, ...$paramArr);
						$stmt->execute();
						$rs = $stmt->get_result();
						while($r = $rs->fetch_object()){
							if(!isset($taxaArr['taxa'][$r->sciname])){
								$taxaArr['taxa'][$r->sciname]['tid'][$r->tid] = $r->rankid;
								$taxaArr['taxa'][$r->sciname]['taxontype'] = 2;
							}
						}
						$rs->free();
						$stmt->close();
					}
				}
			}
		}
	}

	private function appendSynonyms(&$taxaArr){
		//Get synonyms of all accepted taxa
		if(isset($taxaArr['taxa'])){
			foreach($taxaArr['taxa'] as $taxonTerm => $taxonUnitArr){
				if(isset($taxonUnitArr['tid']) && isset($taxonUnitArr['taxontype']) && $taxonUnitArr['taxontype'] == 2){
					$sql = 'SELECT DISTINCT t.tid, t.sciname, t2.sciname as accepted
						FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
						INNER JOIN taxa t2 ON ts.tidaccepted = t2.tid
						WHERE (ts.TidAccepted != ts.tid) AND (ts.tidaccepted IN(' . trim(str_repeat('?,', count($taxonUnitArr['tid'])), ', ') . ')) AND (ts.taxauthid = ?) ';
					$paramArr = array_keys($taxonUnitArr['tid']);
					$typeStr = trim(str_repeat('i', count($taxonUnitArr['tid'])));
					$paramArr[] = $this->taxAuthId;
					$typeStr .= 'i';
					if($stmt = $this->conn->prepare($sql)){
						$stmt->bind_param($typeStr, ...$paramArr);
						$stmt->execute();
						$rs = $stmt->get_result();
						while($r = $rs->fetch_object()) {
							$taxaArr['taxa'][$taxonTerm]['synonyms'][$r->tid] = $r->sciname;
						}
						$rs->free();
						$stmt->close();
					}
				}
			}
		}
	}

	public function getTaxaSearchStr(){
		$returnArr = Array();
		if(isset($this->taxaArr['taxa'])){
			foreach($this->taxaArr['taxa'] as $taxonName => $taxonArr){
				$str = '';
				if(isset($taxonArr['taxontype']) && $this->taxaArr['taxontype'] == TaxaSearchType::ANY_NAME) $str .= TaxaSearchType::anyNameSearchTag($taxonArr['taxontype']).': ';
				$str .= $taxonName;
				if(array_key_exists('scinames', $taxonArr)){
					$str .= " => ".implode(',' ,$taxonArr['scinames']);
				}
				if(array_key_exists('synonyms', $taxonArr)){
					$str .= ' (' . implode(', ', $taxonArr['synonyms']) . ')';
				}
				$returnArr[] = $str;
			}
		}
		return implode(', ', $returnArr);
	}

	public function getAssociationSearchStr(){
		$str = '';
		if(isset($this->associationArr['relationship']) && $this->associationArr['relationship'] != 'none'){
			$str = 'Taxa that have the following association: ';
			$str .= $this->associationArr['relationship'];
		}
		if(isset($this->associationArr['search'])){
				$str .= ' with: ';
				$str .= $this->associationArr['search'];
		}

		return $str;
	}

	public function getTaxaSearchTerm(){
		if(isset($this->taxaArr['search'])) return $this->cleanOutStr($this->taxaArr['search']);
		return '';
	}

	//setters and getters
	public function setTaxAuthId($id){
		if(is_numeric($id)) $this->taxAuthId = $id;
	}

	//Misc support functions
	public function cleanOutArray($inputArray){
		if(is_array($inputArray)){
			foreach($inputArray as $key => $value){
				if(is_array($value)){
					$inputArray[$key] = $this->cleanOutArray($value);
				}
				else{
					$inputArray[$key] = $this->cleanOutStr($value);
				}
			}
		}
		return $inputArray;
	}

	public function cleanOutStr($str){
		if(!is_string($str) && !is_numeric($str) && !is_bool($str)) $str = '';
		return htmlspecialchars($str, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
	}

	protected function cleanInputStr($str){
		$str = preg_replace('/%%+/', '%', $str);
		$str = preg_replace('/^[\s%]+/', '', $str);
		$str = trim($str,' ,;');
		$str = preg_replace('/\s\s+/', ' ',$str);
		return $str;
	}

	protected function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>
