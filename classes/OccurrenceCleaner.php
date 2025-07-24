<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT.'/classes/AgentManager.php');
include_once($SERVER_ROOT.'/classes/GeographicThesaurus.php');

class OccurrenceCleaner extends Manager{

	private $collid;
	private $obsUid;
	private $featureCount = 0;

	const UNVERIFIABLE_NO_POLYGON = -1;
	const COORDINATE_LOCALITY_MISMATCH = 0;
	const HAS_POLYGON_FAILED_TO_VERIFY = 1;
	const COUNTRY_VERIFIED = 2;
	const STATE_PROVINCE_VERIFIED = 5;
	const COUNTY_VERIFIED = 7;

	public function __construct(){
		parent::__construct(null,'write');
	}

	public function __destruct(){
		parent::__destruct();
	}

	//Search and resolve duplicate specimen records
	public function getDuplicateCatalogNumber($type, $start, $limit = 500){
		$dupArr = array();
		if($type=='cat'){
			$sql = 'SELECT catalogNumber as catnum, count(occid) as cnt FROM omoccurrences WHERE catalognumber IS NOT NULL AND collid = '.$this->collid.' GROUP BY catalogNumber ';
		}
		else{
			$sql = 'SELECT o.otherCatalogNumbers as catnum, count(o.occid) as cnt
				FROM omoccurrences o LEFT JOIN omoccuridentifiers i ON o.occid = i.occid
				WHERE i.occid IS NULL AND o.otherCatalogNumbers IS NOT NULL AND o.collid = '.$this->collid.' GROUP BY o.otherCatalogNumbers ';
		}
		$sql .= 'HAVING cnt > 1 ';
		$rs = $this->conn->query($sql);
		$cnt = -1*$start;
		while($r = $rs->fetch_object()){
			$cnt++;
			if($cnt > 0) $dupArr[] = $this->cleanInStr($r->catnum);
			if(count($dupArr) > $limit) break;
		}
		$rs->free();

		$stagingArr = array();
		if($dupArr){
			$sqlFrag = '';
			if($type=='cat'){
				$sqlFrag = 'occid, otherCatalogNumbers, catalognumber AS dupid FROM omoccurrences WHERE collid = '.$this->collid.' AND catalognumber IN("'.implode('","', $dupArr).'") ORDER BY catalognumber';
			}
			else{
				$sqlFrag = 'occid, otherCatalogNumbers, otherCatalogNumbers AS dupid FROM omoccurrences WHERE collid = '.$this->collid.' AND otherCatalogNumbers IN("'.implode('","', $dupArr).'") ORDER BY otherCatalogNumbers';
			}
			$stagingArr = $this->getDuplicates($sqlFrag);
		}

		if($type=='other' && count($dupArr) < $limit){
			$stagingArr = array_merge($stagingArr, $this->setAdditionalIdentifiers($cnt, ($limit - count($dupArr))));
		}

		//Replace catalog number keys with renumbered numeric keys, thus avoid unusual characters interferring with naming form target element
		$retArr = array_values($stagingArr);
		return $retArr;
	}

	private function setAdditionalIdentifiers($cnt, $limit){
		$retArr = array();
		$start = 0;
		if($cnt < 0) $start = -1*$cnt;
		$dupArr = array();
		$sql = 'SELECT i.identifierName, i.identifierValue, COUNT(i.occid) as cnt
			FROM omoccurrences o INNER JOIN omoccuridentifiers i ON o.occid = i.occid
			WHERE o.collid = '.$this->collid.' GROUP BY i.identifiername, i.identifiervalue
			HAVING cnt > 1 LIMIT '.$start.','.$limit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$dupArr[$r->identifierName][] = $this->cleanInStr($r->identifierValue);
		}
		$rs->free();

		foreach($dupArr as $idName => $idValueArr){
			$sqlFrag = 'o.occid, CONCAT(i.identifierName, IF(i.identifierName = "","",": "), i.identifierValue) as otherCatalogNumbers, CONCAT(i.identifierName, ": ", i.identifierValue) as dupid
				FROM omoccurrences o INNER JOIN omoccuridentifiers i ON o.occid = i.occid
				WHERE o.collid = '.$this->collid.' AND i.identifierName = "'.$this->cleanInStr($idName).'" AND i.identifierValue IN("'.implode('","', $idValueArr).'")
				ORDER BY i.identifierName, i.identifierValue';
			$retArr = array_merge($retArr, $this->getDuplicates($sqlFrag));
		}
		return $retArr;
	}

	public function getDuplicateCollectorNumber($start){
		$retArr = array();
		$sql = '';
		if($this->obsUid){
			$sql = 'SELECT o.occid, o.eventdate, recordedby, o.recordnumber '.
				'FROM omoccurrences o INNER JOIN '.
				'(SELECT eventdate, recordnumber FROM omoccurrences GROUP BY eventdate, recordnumber, collid, observeruid '.
				'HAVING Count(*)>1 AND collid = '.$this->collid.' AND observeruid = '.$this->obsUid.
				' AND eventdate IS NOT NULL AND recordnumber IS NOT NULL '.
				'AND recordnumber NOT IN("sn","s.n.","Not Provided","unknown")) intab '.
				'ON o.eventdate = intab.eventdate AND o.recordnumber = intab.recordnumber '.
				'WHERE collid = '.$this->collid.' AND observeruid = '.$this->obsUid.' ';
		}
		else{
			$sql = 'SELECT o.occid, o.eventdate, recordedby, o.recordnumber '.
				'FROM omoccurrences o INNER JOIN '.
				'(SELECT eventdate, recordnumber FROM omoccurrences GROUP BY eventdate, recordnumber, collid '.
				'HAVING Count(*)>1 AND collid = '.$this->collid.' AND eventdate IS NOT NULL AND recordnumber IS NOT NULL '.
				'AND recordnumber NOT IN("sn","s.n.","Not Provided","unknown")) intab ON o.eventdate = intab.eventdate AND o.recordnumber = intab.recordnumber '.
				'WHERE collid = '.$this->collid.' ';
		}
		$rs = $this->conn->query($sql);
		$collArr = array();
		while($r = $rs->fetch_object()){
			$nameArr = Agent::parseLeadingNameInList($r->recordedby);
			if(isset($nameArr['last']) && $nameArr['last'] && strlen($nameArr['last']) > 2){
				$lastName = $nameArr['last'];
				$collArr[$r->eventdate][$r->recordnumber][$lastName][] = $r->occid;
			}
		}
		$rs->free();

		//Collection duplicate clusters
		$cnt = 0;
		foreach($collArr as $ed => $arr1){
			foreach($arr1 as $rn => $arr2){
				foreach($arr2 as $ln => $dupArr){
					if(count($dupArr) > 1){
						//Skip records until start is reached
						if($cnt >= $start){
							$sqlFragment = $cnt.' AS dupid FROM omoccurrences WHERE occid IN('.implode(',',$dupArr).') ';
							//echo $sql;
							$retArr = array_merge($retArr,$this->getDuplicates($sqlFragment));
						}
						if($cnt > ($start+200)) break 3;
						$cnt++;
					}
				}
			}
		}
		return $retArr;
	}

	private function getDuplicates($sqlFragment){
		$retArr = array();
		$sql = 'SELECT catalognumber, family, sciname, recordedby, recordnumber, associatedcollectors,
			eventdate, verbatimeventdate, country, stateprovince, county, municipality, locality, datelastmodified, '.
			$sqlFragment;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_assoc()){
			$retArr[strtolower($row['dupid'])][$row['occid']] = array_change_key_case($row);
		}
		$rs->free();
		return $retArr;
	}

	public function mergeDupeArr($occidArr){
		$status = true;
		$this->verboseMode = 2;
		$editorManager = new OccurrenceEditorManager($this->conn);
		$editorManager->setCollId($this->collid);
		foreach($occidArr as $target => $occArr){
			$mergeArr = array($target);
			foreach($occArr as $source){
				if($source != $target){
					if($editorManager->mergeRecords($target,$source)){
						$mergeArr[] = $source;
					}
					else{
						$this->logOrEcho(trim($editorManager->getErrorStr(),' ;'),1);
						$status = false;
					}
				}
			}
			if(count($mergeArr) > 1){
				$this->logOrEcho('Merged records: '.implode(', ',$mergeArr),1);
			}
		}
		return $status;
	}

	public function hasDuplicateClusters(){
		$retStatus = false;
		$sql = 'SELECT o.occid FROM omoccurrences o INNER JOIN omoccurduplicatelink d ON o.occid = d.occid WHERE (o.collid = '.$this->collid.') LIMIT 1';
		$rs = $this->conn->query($sql);
		if($rs->num_rows) $retStatus = true;
		$rs->free();
		return $retStatus;
	}

    /** Populate omoccurrences.recordedbyid using data from omoccurrences.recordedby.
     */
	public function indexCollectors(){
		//Try to populate using already linked names
		$sql = 'UPDATE omoccurrences o1 INNER JOIN (SELECT DISTINCT recordedbyid, recordedby FROM omoccurrences WHERE recordedbyid IS NOT NULL) o2 ON o1.recordedby = o2.recordedby '.
			'SET o1.recordedbyid = o2.recordedbyid '.
			'WHERE o1.recordedbyid IS NULL';
		$this->conn->query($sql);

		//Query unlinked specimens and try to parse each collector
		$collArr = array();
		$sql = 'SELECT occid, recordedby FROM omoccurrences WHERE recordedbyid IS NULL';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$collArr[$r->recordedby][] = $r->occid;
		}
		$rs->free();

		foreach($collArr as $collStr => $occidArr){
            // check to see if collector is listed in agents table.
            $sql = 'select distinct agentid from agentname where name = ? ';
            if ($stmt = $this->conn->prepare($sql)) {
               $stmt->bind_param('s',$collStr);
               $stmt->execute();
               $stmt->bind_result($agentid);
               $stmt->store_result();
               $matches = $stmt->num_rows;
               $stmt->fetch();
               $stmt->close();
               if ($matches>0) {
                  $recById= $agentid;
               }
               else {
                  // no matches found to collector, add to agent table.
                  $am = new AgentManager();
                  $agent = $am->constructAgentDetType($collStr);
                  if ($agent!=null) {
                     $am->saveNewAgent($agent);
                     $agentid = $agent->getagentid();
                     $recById= $agentid;
                  }
               }
            }
            else {
               throw new Exception("Error preparing query $sql " . $this->conn->error);
            }

			//Add recordedbyid to omoccurrence table
			if($recById){
				$sql = 'UPDATE omoccurrences SET recordedbyid = '.$recById.' WHERE occid IN('.implode(',',$occidArr).') AND recordedbyid IS NULL ';
				$this->conn->query($sql);
			}
		}
	}

	//Geographic functions
	public function countryCleanFirstStep(){
		//Country cleaning
		echo '<div style="margin-left:15px;">Preparing countries index...</div>';
		flush();
		ob_flush();
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((country LIKE " %") OR (country LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET country = trim(country) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET country = NULL WHERE (country = "")';
		$this->conn->query($sqlEmpty);

		//State cleaning
		echo '<div style="margin-left:15px;">Preparing state index...</div>';
		flush();
		ob_flush();
		unset($occArr);
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((stateprovince LIKE " %") OR (stateprovince LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET stateprovince = trim(stateprovince) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET stateprovince = NULL WHERE (stateprovince = "")';
		$this->conn->query($sqlEmpty);

		//County cleaning
		echo '<div style="margin-left:15px;">Preparing county index...</div>';
		flush();
		ob_flush();
		unset($occArr);
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((county LIKE " %") OR (county LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET county = trim(county) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET county = NULL WHERE (county = "")';
		$this->conn->query($sqlEmpty);

		//Municipality cleaning
		/*
		echo '<div style="margin-left:15px;">Preparing municipality index...</div>';
		flush();
		ob_flush();
		unset($occArr);
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((municipality LIKE " %") OR (municipality LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET municipality = trim(municipality) WHERE (occid IN('.implode(',',$occArr).'))';
			echo $sqlTrim.'<br/>';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET municipality = NULL WHERE (municipality = "")';
		$this->conn->query($sqlEmpty);
		*/
	}

	//Bad countries
	public function getBadCountryCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT country) AS cnt
			FROM omoccurrences
			WHERE country IS NOT NULL AND collid = '.$this->collid.' AND country NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 50)';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadCountryArr(){
		$retArr = array();
		$sql = 'SELECT country, count(occid) as cnt
			FROM omoccurrences
			WHERE country IS NOT NULL AND collid = '.$this->collid.' AND country NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 50)
			GROUP BY country';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->country] = $r->cnt;
		}
		$rs->free();
		$this->featureCount = count($retArr);
		ksort($retArr);
		return $retArr;
	}

	public function getGoodCountryArr($includeStates = false){
		$retArr = array();
		if($includeStates){
			$sql = 'SELECT g1.geoterm as countryName, g2.geoterm AS stateName
				FROM geographicthesaurus g1 INNER JOIN geographicthesaurus g2 ON g1.geoThesID = g2.parentID
				WHERE g1.geoLevel = 50 AND g2.geoLevel = 60';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->countryName][] = $r->stateName;
			}
			$rs->free();
			ksort($retArr);
		}
		else{
			$sql = 'SELECT geoterm FROM geographicthesaurus WHERE geolevel = 50';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->geoterm;
			}
			$rs->free();
			sort($retArr);
			$retArr[] = 'unknown';
		}
		return $retArr;
	}

	public function getNullCountryNotStateCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT stateprovince) AS cnt FROM omoccurrences WHERE (collid = '.$this->collid.') AND (country IS NULL) AND (stateprovince IS NOT NULL)';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullCountryNotStateArr(){
		$retArr = array();
		$sql = 'SELECT stateprovince, COUNT(occid) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collid.') AND (country IS NULL) AND (stateprovince IS NOT NULL) '.
			'GROUP BY stateprovince';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[ucwords(strtolower($r->stateprovince))] = $r->cnt;
		}
		$rs->free();
		$this->featureCount = count($retArr);
		ksort($retArr);
		return $retArr;
	}

	//States cleaning functions
	public function getBadStateCount($country = ''){
		$retCnt = array();
		$sql = 'SELECT COUNT(DISTINCT stateprovince) as cnt
			FROM omoccurrences WHERE (country IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 50)) AND (stateprovince IS NOT NULL)
			AND (collid = '.$this->collid.') AND (stateprovince NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 60)) ';
		if($country) $sql .= 'AND country = "'.$this->cleanInStr($country).'" ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadStateArr(){
		$retArr = array();
		$sql = 'SELECT country, stateprovince, count(DISTINCT occid) as cnt
			FROM omoccurrences
			WHERE (country IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 50)) AND (stateprovince IS NOT NULL)
			AND (collid = '.$this->collid.') AND (stateprovince NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 60))
			GROUP BY stateprovince ';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[$r->country][ucwords(strtolower($r->stateprovince))] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		ksort($retArr);
		return $retArr;
	}

	public function getGoodStateArr($includeCounties = false){
		$retArr = array();
		if($includeCounties){
			$sql = 'SELECT g1.geoterm as countryName, g2.geoterm AS stateName, g3.geoterm AS countyName
				FROM geographicthesaurus g1 INNER JOIN geographicthesaurus g2 ON g1.geoThesID = g2.parentID
				LEFT JOIN geographicthesaurus g3 ON g2.geoThesID = g3.parentID
				WHERE g1.geoLevel = 50 AND g2.geoLevel = 60 AND g3.geoLevel = 70 ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[strtoupper($r->countryName)][ucwords(strtolower($r->stateName))][] = str_ireplace(array(' county',' co.',' co'),'',$r->countyName);
			}
			$rs->free();
		}
		else{
			$sql = 'SELECT g1.geoterm as countryName, g2.geoterm AS stateName
				FROM geographicthesaurus g1 INNER JOIN geographicthesaurus g2 ON g1.geoThesID = g2.parentID
				WHERE g1.geoLevel = 50 AND g2.geoLevel = 60';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->countryName][] = $r->stateName;
			}
			$rs->free();
			foreach ($retArr as &$counties) {
				sort($counties, SORT_STRING);
			}
			unset($counties);
		}
		ksort($retArr);
		$retArr[] = 'unknown';
		return $retArr;
	}

	public function getNullStateNotCountyCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT county) AS cnt FROM omoccurrences WHERE (collid = '.$this->collid.') AND (country IS NOT NULL) AND (stateprovince IS NULL) AND (county IS NOT NULL)';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullStateNotCountyArr(){
		$retArr = array();
		$sql = 'SELECT country, county, COUNT(occid) AS cnt FROM omoccurrences
			WHERE (collid = '.$this->collid.') AND (stateprovince IS NULL) AND (county IS NOT NULL) AND (country IS NOT NULL)
			GROUP BY county';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[strtoupper($r->country)][$r->county] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		ksort($retArr);
		return $retArr;
	}

	//Bad Counties
	public function getBadCountyCount($state = ''){
		$retCnt = array();
		$sql = 'SELECT COUNT(DISTINCT county) as cnt
			FROM omoccurrences WHERE (county IS NOT NULL) AND (country = "USA") AND (stateprovince IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 60)) '.
			'AND (collid = '.$this->collid.') AND (county NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 70)) ';
		if($state) $sql .= 'AND stateprovince = "'.$this->cleanInStr($state).'" ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadCountyArr(){
		$retArr = array();
		$sql = 'SELECT country, stateprovince, county, count(occid) as cnt
			FROM omoccurrences WHERE (county IS NOT NULL) AND (country = "USA") AND (stateprovince IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 60))
			AND (collid = '.$this->collid.') AND (county NOT IN(SELECT geoterm FROM geographicthesaurus WHERE geolevel = 70))
			GROUP BY country, stateprovince, county ';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[strtoupper($r->country)][ucwords(strtolower($r->stateprovince))][$r->county] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		//ksort($retArr);
		return $retArr;
	}

	public function getGoodCountyArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT g1.geoterm as stateName, REPLACE(g2.geoterm," County","") as countyName
			FROM geographicthesaurus g1 INNER JOIN geographicthesaurus g2 ON g1.geoThesID = g2.parentID
			WHERE g1.geoLevel = 60 AND g2.geoLevel = 70
			ORDER BY g2.geoterm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[strtolower($r->stateName)][] = $r->countyName;
		}
		$rs->free();
		$retArr[] = 'unknown';
		return $retArr;
	}

	public function getNullCountyNotLocalityCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT locality) AS cnt FROM omoccurrences
			WHERE (collid = '.$this->collid.') AND (county IS NULL) AND (locality IS NOT NULL)
			AND country IN("USA","United States") AND (stateprovince IS NOT NULL) AND (stateprovince NOT IN("District Of Columbia","DC"))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullCountyNotLocalityArr(){
		$retArr = array();
		$sql = 'SELECT country, stateprovince, locality, COUNT(occid) AS cnt
			FROM omoccurrences
			WHERE (collid = '.$this->collid.') AND (county IS NULL) AND (locality IS NOT NULL)
			AND country IN("USA","United States") AND (stateprovince IS NOT NULL) AND (stateprovince NOT IN("District Of Columbia","DC"))
			GROUP BY country, stateprovince, locality';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$locStr = $r->locality;
			//if(strlen($locStr) > 40) $locStr = substr($locStr,0,40).'...';
			$retArr[$r->country][ucwords(strtolower($r->stateprovince))][$locStr] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		ksort($retArr);
		return $retArr;
	}

	//Coordinate field verifier
	public function getCoordStats(){
		$retArr = array();
		//Get count georeferenced
		$sql = 'SELECT count(*) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid IN('.$this->collid.')) AND (decimallatitude IS NOT NULL) AND (decimallongitude IS NOT NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['coord'] = $r->cnt;
		}
		$rs->free();

		//Get count not georeferenced
		$sql = 'SELECT count(*) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid IN('.$this->collid.')) AND (decimallatitude IS NULL) AND (decimallongitude IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['noCoord'] = $r->cnt;
		}
		$rs->free();

		//Count not georeferenced with verbatimCoordinates info
		$sql = 'SELECT count(*) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid IN('.$this->collid.')) AND (decimallatitude IS NULL) AND (decimallongitude IS NULL) AND (verbatimcoordinates IS NOT NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['noCoord_verbatim'] = $r->cnt;
		}
		$rs->free();

		//Count not georeferenced without verbatimCoordinates info
		$sql = 'SELECT count(*) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid IN('.$this->collid.')) AND (decimallatitude IS NULL) AND (decimallongitude IS NULL) AND (verbatimcoordinates IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['noCoord_noVerbatim'] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getUnverifiedByCountry(){
		$retArr = array();
		$sql = 'SELECT country, count(country) as cnt, COALESCE(acceptedID, geoThesID) as geoThesID FROM omoccurrences o
			JOIN omoccurpoints pts ON pts.occid = o.occid
			LEFT JOIN omoccurverification ov ON ov.occid = o.occid AND category = "coordinate"
			LEFT JOIN geographicthesaurus g ON g.geoterm = country and geoLevel = 50
			WHERE collid = ' . $this->collid . ' AND country IS NOT NULL AND ov.occid IS NULL
			GROUP BY country';

		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->country] = $r;
		}
		$rs->free();
		return $retArr;
	}

	public function questionableRankText(int $rank): string {
		switch($rank) {
			case self::UNVERIFIABLE_NO_POLYGON;
				return $GLOBALS['LANG']['UNVERIFIABLE_NO_POLYGON'];
			case self::HAS_POLYGON_FAILED_TO_VERIFY:
				return $GLOBALS['LANG']['HAS_POLYGON_FAILED_TO_VERIFY'];
			case self::COORDINATE_LOCALITY_MISMATCH:
				return $GLOBALS['LANG']['COUNTRY_DOES_NOT_MATCH_COORDS'];
			case self::COUNTRY_VERIFIED:
				return $GLOBALS['LANG']['STATE_PROVINCE_DOES_NOT_MATCH_COORDS'];
			case self::STATE_PROVINCE_VERIFIED:
				return $GLOBALS['LANG']['COUNTY_DOES_NOT_MATCH_COORDS'];
			default: 
				return $GLOBALS['LANG']['INVALID_RANK'];
		}
	}

	/*
	* Gets Last verified date of a collection by category
	* @param String $category string value of either 'coordinate' or 'identification'
	* @return string | null
	*/
	public function getDateLastVerifiedByCategory(string $category) {
		$sql = 'SELECT DATE(ov.initialtimestamp) lastVerified from omoccurverification ov
			join omoccurrences o on o.occid = ov.occid
			where collid = ? and category = ?
			Group by DATE(ov.initialtimestamp)
			ORDER BY lastVerified DESC';

		$result = QueryUtil::executeQuery($this->conn, $sql, [ $this->collid, $category ]);
		$row = $result->fetch_object();

		return $row->lastVerified ?? null;
	}

	public function removeVerificationByCategory(string $category, $ranking = false) {
		$params = [ $this->collid, $category ];
		$sql = 'DELETE omoccurverification FROM omoccurverification
			INNER JOIN omoccurrences o on o.occid = omoccurverification.occid
			WHERE o.collid = ? and omoccurverification.category = ?';

		if(is_numeric($ranking)) {
			$sql .= ' and omoccurverification.ranking = ?';
			array_push($params, $ranking);
		}

		return QueryUtil::executeQuery($this->conn, $sql, $params);
	}

	public function getQuestionableCoordinateCounts(): array {
		$unions = [];
		$rank_arr = [self::UNVERIFIABLE_NO_POLYGON, self::HAS_POLYGON_FAILED_TO_VERIFY, self::COORDINATE_LOCALITY_MISMATCH, self::COUNTRY_VERIFIED, self::STATE_PROVINCE_VERIFIED ];
		$parameters = [];
		foreach($rank_arr as $rank) {
			$base_sql = 'SELECT count(*) as count, ranking FROM omoccurrences o
				JOIN omoccurverification ov on ov.occid = o.occid where category = "coordinate" and collid = ? and ranking = ?';

			if($rank === self::COORDINATE_LOCALITY_MISMATCH) {
				$base_sql .= ' AND (country is not null or stateProvince is not null or county is not null)';
			} else if($rank === self::COUNTRY_VERIFIED) {
				$base_sql .= ' AND (stateProvince is not null or county is not null)';
			} else if($rank === self::STATE_PROVINCE_VERIFIED) {
				$base_sql .= ' AND (county is not null)';
			}

			$parameters[] = $this->collid;
			$parameters[] = $rank;

			$unions[] = '(' . $base_sql . ')';
		}

		$sql = implode(' UNION ', $unions);
		$result = QueryUtil::executeQuery($this->conn, $sql, $parameters);

		$questionableCounts = [];
		while($row = $result->fetch_object()) {
			if($row->count > 0) {
				$questionableCounts[$row->ranking] = $row->count;
			}
		}

		return $questionableCounts;
	}

	public function findFailedVerificationsOnKnownPolyons() {
		$sql = 'update omoccurverification set ranking = ? where category = "coordinate" and occid in (
		SELECT occid from geographicthesaurus g50
			join geographicthesaurus g60 on g60.geoLevel = 60 and g60.parentID = g50.geoThesID
			join geographicthesaurus g70 on g70.geoLevel = 70 and g70.parentID = g60.geoThesID
			join geographicpolygon gp on gp.geoThesID = g70.geoThesID
			join (
				SELECT o.occid, country, stateProvince, county from omoccurrences o
				join omoccurverification as ov on ov.occid = o.occid
				where ranking = -1 and ov.category = "coordinate" and collid = ?
			) as missing_coords on 
			missing_coords.country = g50.geoterm and 
			missing_coords.stateProvince = g60.geoterm and 
			missing_coords.county = g70.geoterm)';

		$rs = QueryUtil::executeQuery($this->conn, $sql, [self::HAS_POLYGON_FAILED_TO_VERIFY, $this->collid ]);
	}

	public function verifyCoordAgainstPoliticalV2(
		array $countries = [],
		array $targetGeoThesIDs = [],
		bool $populateCountry = false,
		bool $populateStateProvince = false,
		bool $populateCounty = false,
	) {
		// Does no need offset because occurrences that get pulled out will get saved to 
		// omoccurverification table which this query is doing a not in select of so it can 
		// be iterated through with just a function call
		$coord_query = 'SELECT o.occid, country, stateProvince, county FROM omoccurrences o
			LEFT JOIN omoccurverification ov ON ov.occid = o.occid AND category="coordinate"
			JOIN omoccurpoints pts ON pts.occid = o.occid
			WHERE ov.occid IS NULL AND collid = ? ';
		$coord_query_params = [$this->collid];

		if(count($countries)) {
			$parameters = str_repeat('?,', count($countries) - 1) . '?';
			$coord_query .= 'AND o.country in (' . $parameters . ') ';

			$coord_query_params = array_merge($coord_query_params, $countries);
		}

		$coord_query .= 'LIMIT 1000';

		$result = QueryUtil::executeQuery($this->conn, $coord_query, $coord_query_params);
		$occid_arr = [];

		while(($row = $result->fetch_object())) {
			$occid_arr[$row->occid] = [];
			$occid_arr[$row->occid]['country'] = $row->country;
			$occid_arr[$row->occid]['stateProvince'] = $row->stateProvince;
			$occid_arr[$row->occid]['county'] = $row->county;
		}

		if(count($occid_arr) === 0) return $occid_arr;

		QueryUtil::executeQuery($this->conn, 'SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		$this->conn->begin_transaction();

		$resolve_geo_thesaurus = 'SELECT pts.occid, g70.geoterm as county, g60.geoterm as stateProvince, g50.geoterm as country from geographicthesaurus as g50
			left join (
				select g.geoThesID, parentID, geoterm from geographicthesaurus as g
				join geographicpolygon as gp on gp.geoThesID = g.geoThesID and g.geoLevel = 60
			) as g60 on g60.parentID = g50.geoThesID
			left join (
				select g.geoThesID, parentID, geoterm from geographicthesaurus as g
				join geographicpolygon as gp on gp.geoThesID = g.geoThesID and g.geoLevel = 70
			) as g70 on g70.parentID = g60.geoThesID
			join geographicpolygon gp on gp.geoThesID = g70.geoThesID
			join omoccurpoints pts on ST_CONTAINS(gp.footprintPolygon, pts.lngLatPoint)
			where ';
		$resolve_geo_thesaurus_parameters = [];

		if(count($targetGeoThesIDs)) {
			$parameters = str_repeat('?,', count($targetGeoThesIDs) - 1) . '?';
			$resolve_geo_thesaurus .= 'g50.geoThesID in (' . $parameters . ') AND ';
			$resolve_geo_thesaurus_parameters = $targetGeoThesIDs;
		}

		$resolve_geo_thesaurus .= 'g50.geolevel = 50 and pts.occid in (' . implode(',', array_keys($occid_arr)) . ')';

		$geo_check_result = QueryUtil::executeQuery($this->conn, $resolve_geo_thesaurus, $resolve_geo_thesaurus_parameters);

		$this->conn->commit();

		$editorManager = new OccurrenceEditorManager($this->conn);
		$editorManager->setCollId($this->collid);

		while(($row = $geo_check_result->fetch_object())) {
			$editorManager->setOccId($row->occid);

			// Handle Data Population
			if($populateCountry && $occid_arr[$row->occid]['country'] === null && $row->country) {
				$editorManager->editOccurrence(['country' => $row->country, 'occid' => $row->occid, 'editedfields' => 'country'], $GLOBALS['IS_ADMIN'] ?? 0);
				$occid_arr[$row->occid]['country'] = $row->country;
				$occid_arr[$row->occid]['populatedCountry'] = true;
			}

			if($populateStateProvince && $occid_arr[$row->occid]['stateProvince'] === null && $row->stateProvince) {
				$editorManager->editOccurrence(['stateprovince' => $row->stateProvince, 'occid' => $row->occid, 'editedfields' => 'stateprovince'], $GLOBALS['IS_ADMIN'] ?? 0);
				$occid_arr[$row->occid]['stateProvince'] = $row->stateProvince;
				$occid_arr[$row->occid]['populatedStateProvince'] = true;
			}

			if($populateCounty && $occid_arr[$row->occid]['county'] === null && $row->county) {
				$editorManager->editOccurrence(['county' => $row->county, 'occid' => $row->occid, 'editedfields' => 'county'], $GLOBALS['IS_ADMIN'] ?? 0);
				$occid_arr[$row->occid]['county'] = $row->county;
				$occid_arr[$row->occid]['populatedCounty'] = true;
			}
			
			// Build Notes Field
			$occid_arr[$row->occid]['notes'] = 'Coordinate Verified to: ' . 
				$row->country . ' | ' . 
				$row->stateProvince . ' | ' . 
				$row->county;

			// Determine Verification Level
			if($row->county && GeographicThesaurus::unitsEqual(
				$occid_arr[$row->occid]['county'] ?? '',
				$row->county ?? '',
				GeographicThesaurus::COUNTY)
			) {
				$occid_arr[$row->occid]['rank'] = self::COUNTY_VERIFIED;
			} else if($row->stateProvince && GeographicThesaurus::unitsEqual(
				$occid_arr[$row->occid]['stateProvince'] ?? '',
				$row->stateProvince ?? '',
				GeographicThesaurus::STATE_PROVINCE)
			) {
				$occid_arr[$row->occid]['rank'] = self::STATE_PROVINCE_VERIFIED;
			} else if($row->country && GeographicThesaurus::unitsEqual(
				$occid_arr[$row->occid]['country'] ?? '',
				$row->country ?? '',
				GeographicThesaurus::COUNTRY)
			) {
				$occid_arr[$row->occid]['rank'] = self::COUNTRY_VERIFIED;
			} else {
				$occid_arr[$row->occid]['rank'] = self::COORDINATE_LOCALITY_MISMATCH;
			}
		}

		$batch_verification = 'INSERT INTO omoccurverification(occid, category, ranking, protocol, uid, notes) VALUES ';

		$last_occid = array_key_last($occid_arr);
		foreach($occid_arr as $occid => $occurrence) {
			$values = [
				$occid, 
				'"coordinate"', 
				$occurrence['rank'] ?? self::UNVERIFIABLE_NO_POLYGON, 
				'"geographicthesaurus"', 
				$GLOBALS['SYMB_UID'],
				array_key_exists('notes', $occurrence) ? '"' . $occurrence['notes'] .'"': 'NULL' 
			];

			$batch_verification .= '(' . implode(',', $values) . ')';

			if($occid != $last_occid) {
				$batch_verification .= ', ';
			}
		}

		QueryUtil::executeQuery($this->conn, $batch_verification);

		return $occid_arr;
	}

	//General ranking functions
	public function getCategoryList(){
		$retArr = array();
		$sql = 'SELECT DISTINCT v.category '.
			'FROM omoccurverification v INNER JOIN omoccurrences o ON v.occid = o.occid '.
			'WHERE (o.collid IN('.$this->collid.'))';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->category;
		}
		$rs->free();
		sort($retArr);
		return $retArr;
	}

	public function getUnverifiedCount(string $category): int {
		$sql = 'SELECT count(*) as unverified_cnt from omoccurrences o
		JOIN omoccurpoints pts ON pts.occid = o.occid
		LEFT JOIN omoccurverification ov ON ov.occid = o.occid AND category = ?
		WHERE collid = ? AND ov.occid IS NULL';

		$rs = QueryUtil::executeQuery($this->conn, $sql, [ $category, $this->collid]);
		$r = $rs->fetch_object();
		$rs->free();

		return $r->unverified_cnt;
	}

	//TODO (Logan) decide if Deprecate before pull request merged?
	public function getRankingStats($category){
		$retArr = array();
		$category = $this->cleanInStr($category);
		$sql = 'SELECT o.collid, v.category, v.ranking, v.protocol, COUNT(v.occid) as cnt '.
			'FROM omoccurverification v INNER JOIN omoccurrences o ON v.occid = o.occid '.
			'WHERE (o.collid IN('.$this->collid.')) AND v.category = "'.$category.'" '.
			'GROUP BY o.collid, v.category, v.ranking';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->ranking] = intval($r->cnt);
		}
		$rs->free();
		if($category){
			//Get unranked count
			$sql = 'SELECT COUNT(occid) AS cnt '.
				'FROM omoccurrences '.
				'WHERE (collid IN('.$this->collid.')) AND (decimallatitude IS NOT NULL) AND (decimallongitude IS NOT NULL) AND (occid NOT IN(SELECT occid FROM omoccurverification WHERE category = "'.$category.'"))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['unverified'] = intval($r->cnt);
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getOccurList($category, $ceilingRank, $floorRank = 0){
		$retArr = array();
		if(is_numeric($ceilingRank) && is_numeric($floorRank)){
			$sql = 'SELECT v.ovsid, v.occid, v.category, v.ranking, v.protocol, v.source, v.uid, v.notes, v.initialtimestamp '.
				'FROM omoccurverification v INNER JOIN omoccurrences o ON v.occid = o.occid '.
				'WHERE (o.collid IN('.$this->collid.')) AND (v.category = "'.$this->cleanInStr($category).'") '.
				'AND (v.ranking BETWEEN '.$floorRank.' AND '.$ceilingRank.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){

			}
			$rs->free();
		}
		return $retArr;
	}

	public function getOccurrenceRankingArr($category, $ranking){
		$retArr = array();
		if(is_numeric($ranking)){
			$sql = 'SELECT DISTINCT v.occid, u.username, v.initialtimestamp '.
				'FROM omoccurverification v INNER JOIN omoccurrences o ON v.occid = o.occid '.
				'INNER JOIN users u ON v.uid = u.uid '.
				'WHERE (o.collid IN('.$this->collid.')) AND (v.category = "'.$this->cleanInStr($category).'") AND (ranking = '.$ranking.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->occid]['username'] = $r->username;
				$retArr[$r->occid]['ts'] = $r->initialtimestamp;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getRankList(){
		$retArr = array();
		$sql = 'SELECT DISTINCT v.ranking FROM omoccurverification v INNER JOIN omoccurrences o ON v.occid = o.occid WHERE (o.collid IN('.$this->collid.'))';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->ranking;
		}
		$rs->free();
		sort($retArr);
		return $retArr;
	}

	//General field updater
	public function updateField($fieldName, $oldValue, $newValue, $conditionArr = null){
		if(is_numeric($this->collid) && $fieldName && $newValue){
			$editorManager = new OccurrenceEditorManager($this->conn);
			$qryArr = array('cf1'=>'collid','ct1'=>'EQUALS','cv1'=>$this->collid);
			if($conditionArr){
				$cnt = 2;
				foreach($conditionArr as $k => $v){
					$qryArr['cf'.$cnt] = $k;
					if($v == '--ISNULL--'){
						$qryArr['ct'.$cnt] = 'NULL';
						$qryArr['cv'.$cnt] = '';
					}
					else{
						$qryArr['ct'.$cnt] = 'EQUALS';
						$qryArr['cv'.$cnt] = $v;
					}
					$cnt++;
					if($cnt > 4) break;
				}
			}
			$editorManager->setQueryVariables($qryArr);
			$editorManager->batchUpdateField($fieldName,$oldValue,$newValue,false);
		}
		return true;
	}

	//Setters and getters
	public function setCollId($collid){
		if(preg_match('/^[\d,]+$/', $collid)){
			$this->collid = $collid;
		}
	}

	public function setObsuid($obsUid){
		if(is_numeric($obsUid)){
			$this->obsUid = $obsUid;
		}
	}

	public function getFeatureCount(){
		return $this->featureCount;
	}

	//Misc fucntions
	public function getCollMap(){
		$retArr = Array();
		$sql = 'SELECT collid, CONCAT_WS("-",institutioncode, collectioncode) AS code, collectionname, icon, colltype, managementtype FROM omcollections ';
		if($this->collid) $sql .= 'WHERE (collid IN('.$this->collid.')) ';
		$sql .= 'ORDER BY collectionname,institutioncode,collectioncode';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->collid]['code'] = $row->code;
			$retArr[$row->collid]['collectionname'] = $row->collectionname;
			$retArr[$row->collid]['icon'] = $row->icon;
			$retArr[$row->collid]['colltype'] = $row->colltype;
			$retArr[$row->collid]['managementtype'] = $row->managementtype;
		}
		$rs->free();
		return $retArr;
	}
}
?>
