<?php
include_once ($SERVER_ROOT . '/classes/Manager.php');

class GeographicThesaurus extends Manager{

	function __construct(){
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getGeograpicList($conditionTerm = null){
		$retArr = array();
		$sql = 'SELECT t.geoThesID, t.geoTerm, t.abbreviation, t.iso2, t.iso3, t.numCode, t.category, t.geoLevel, t.termStatus, t.acceptedID, a.geoterm as acceptedTerm
			FROM geographicthesaurus t LEFT JOIN geographicthesaurus a ON t.acceptedID = a.geoThesID ';
		if($conditionTerm && is_numeric($conditionTerm)) $sql .= 'WHERE (t.parentID = '.$conditionTerm.') ';
		else $sql .= 'WHERE (t.parentID IS NULL) ';
		$sql .= 'ORDER BY t.geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID]['geoTerm'] = $r->geoTerm;
			$retArr[$r->geoThesID]['abbreviation'] = $r->abbreviation;
			$retArr[$r->geoThesID]['iso2'] = $r->iso2;
			$retArr[$r->geoThesID]['iso3'] = $r->iso3;
			$retArr[$r->geoThesID]['numCode'] = $r->numCode;
			$retArr[$r->geoThesID]['category'] = $r->category;
			$retArr[$r->geoThesID]['geoLevel'] = $r->geoLevel;
			$retArr[$r->geoThesID]['termStatus'] = $r->termStatus;
			$retArr[$r->geoThesID]['acceptedID'] = $r->acceptedID;
			$retArr[$r->geoThesID]['acceptedTerm'] = $r->acceptedTerm;
		}
		$rs->free();

		if($retArr){
			$childCntArr = $this->setChildCnt(implode(',',array_keys($retArr)));
			foreach($childCntArr as $id => $cnt){
				$retArr[$id]['childCnt'] = $cnt;
			}
		}
		return $retArr;
	}

	public function getGeograpicUnit($geoThesID){
		$retArr = array();
		if(is_numeric($geoThesID)){
			$sql = 'SELECT t.geoThesID, t.geoTerm, t.abbreviation, t.iso2, t.iso3, t.numCode, t.category, t.geoLevel, t.parentID, p.geoTerm as parentTerm, t.notes, t.termStatus,
				t.acceptedID, a.geoterm as acceptedTerm
				FROM geographicthesaurus t LEFT JOIN geographicthesaurus a ON t.acceptedID = a.geoThesID
				LEFT JOIN geographicthesaurus p ON t.parentID = p.geoThesID
				WHERE t.geoThesID = '.$geoThesID;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['geoThesID'] = $r->geoThesID;
				$retArr['geoTerm'] = $r->geoTerm;
				$retArr['abbreviation'] = $r->abbreviation;
				$retArr['iso2'] = $r->iso2;
				$retArr['iso3'] = $r->iso3;
				$retArr['numCode'] = $r->numCode;
				$retArr['category'] = $r->category;
				$retArr['geoLevel'] = $r->geoLevel;
				$retArr['acceptedID'] = $r->acceptedID;
				$retArr['acceptedTerm'] = $r->acceptedTerm;
				$retArr['parentID'] = $r->parentID;
				$retArr['parentTerm'] = $r->parentTerm;
				$retArr['notes'] = $r->notes;
				$retArr['termStatus'] = $r->termStatus;
			}
			$rs->free();
			if($retArr){
				$childArr = $this->setChildCnt($retArr['geoThesID']);
				$cnt = 0;
				if($childArr) $cnt = current($childArr);
				$retArr['childCnt'] = $cnt;
			}
		}
		return $retArr;
	}

	public function editGeoUnit($postArr){
		if(is_numeric($postArr['geoThesID'])){
			if(!$postArr['geoTerm']){
				$this->errorMessage = 'ERROR editing geoUnit: geographic term must have a value';
				return false;
			}
			$sql = 'UPDATE geographicthesaurus '.
				'SET geoterm = "'.$this->cleanInStr($postArr['geoTerm']).'", '.
				'abbreviation = '.($postArr['abbreviation']?'"'.$this->cleanInStr($postArr['abbreviation']).'"':'NULL').', '.
				'iso2 = '.($postArr['iso2']?'"'.$this->cleanInStr($postArr['iso2']).'"':'NULL').', '.
				'iso3 = '.($postArr['iso3']?'"'.$this->cleanInStr($postArr['iso3']).'"':'NULL').', '.
				'numcode = '.(is_numeric($postArr['numCode'])?'"'.$this->cleanInStr($postArr['numCode']).'"':'NULL').', '.
				'geoLevel = '.(is_numeric($postArr['geoLevel'])?$this->cleanInStr($postArr['geoLevel']):'NULL').', '.
				'acceptedID = '.(is_numeric($postArr['acceptedID'])?'"'.$this->cleanInStr($postArr['acceptedID']).'"':'NULL').', '.
				'parentID = '.(is_numeric($postArr['parentID'])?'"'.$this->cleanInStr($postArr['parentID']).'"':'NULL').', '.
				'notes = '.($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').' '.
				'WHERE (geoThesID = '.$postArr['geoThesID'].')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR saving edits: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	public function addGeoUnit($postArr){
		if(!$postArr['geoTerm']){
			$this->errorMessage = 'ERROR adding geoUnit: geographic term must have a value';
			return false;
		}
		else{
			$sql = 'INSERT INTO geographicthesaurus(geoterm, abbreviation, iso2, iso3, numcode, geoLevel, acceptedID, parentID, notes) '.
				'VALUES("'.$this->cleanInStr($postArr['geoTerm']).'", '.
				($postArr['abbreviation']?'"'.$this->cleanInStr($postArr['abbreviation']).'"':'NULL').', '.
				($postArr['iso2']?'"'.$this->cleanInStr($postArr['iso2']).'"':'NULL').', '.
				($postArr['iso3']?'"'.$this->cleanInStr($postArr['iso3']).'"':'NULL').', '.
				(is_numeric($postArr['numCode'])?'"'.$this->cleanInStr($postArr['numCode']).'"':'NULL').', '.
				(is_numeric($postArr['geoLevel'])?$this->cleanInStr($postArr['geoLevel']):'NULL').', '.
				(is_numeric($postArr['acceptedID'])?'"'.$this->cleanInStr($postArr['acceptedID']).'"':'NULL').', '.
				(is_numeric($postArr['parentID'])?'"'.$this->cleanInStr($postArr['parentID']).'"':'NULL').', '.
				($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').')';
			echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR adding unit: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	public function addChildGeoUnit($postArr){
		//Add new child
		//Uses an INSERT INTO sql statement

		/* 	$statusStr = '';
		if(is_numeric($postArr['geoThesID'])){
			$sql = 'UPDATE geographicthesaurus '.
				'SET geoterm = '.($postArr['geoterm']?'"'.$postArr['geoterm'].'"':'NULL').
				', iso2 = '.($postArr['iso2']?'"'.$postArr['iso2'].'"':'NULL').
				', iso3 = '.($postArr['iso3']?'"'.$postArr['iso3'].'"':'NULL').
				' WHERE (geoThesID = '.$postArr['geoThesID'].')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: changes saved';
			}
			else{
				$statusStr = 'ERROR: changes not saved'.$this->conn->error;
			}
		}
		return $statusStr; */
	}

	public function deleteGeoUnit($geoThesID){
		if(is_numeric($geoThesID)){
			$sql = 'DELETE FROM geographicthesaurus WHERE (geoThesID = '.$geoThesID.')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR deleting geoUnit: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	private function setChildCnt($geoIdStr){
		$retArr = array();
		$sql = 'SELECT parentID, count(*) as cnt FROM geographicthesaurus WHERE parentID IN('.$geoIdStr.') GROUP BY parentID ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->parentID] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getCoordStatistics(){
		$retArr = array();
		$totalCnt = 0;
		$sql = 'SELECT COUNT(*) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . '))';
		$rs = $this->conn->query($sql);
		while ($r = $rs->fetch_object()) {
			$totalCnt = $r->cnt;
		}
		$rs->free();

		// Full count
		$sql2 = 'SELECT COUNT(occid) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . ')) AND (decimalLatitude IS NULL) AND (georeferenceVerificationStatus IS NULL) ';
		if ($rs2 = $this->conn->query($sql2)) {
			if ($r2 = $rs2->fetch_object()) {
				$retArr['total'] = $r2->cnt;
				$retArr['percent'] = round($r2->cnt * 100 / $totalCnt, 1);
			}
			$rs2->free();
		}

		return $retArr;
	}

	//Misc data retrieval functions
	public function getParentGeoTermArr($geoLevelMax = 0){
		$retArr = array();
		$sql = 'SELECT t.geoThesID, CONCAT_WS(" ",t.geoTerm,CONCAT(" (",p.geoTerm,")")) AS geoTerm FROM geographicthesaurus t LEFT JOIN geographicthesaurus p ON t.parentID = p.geoThesID ';
		if($geoLevelMax) $sql .= 'WHERE t.geoLevel < '.$geoLevelMax.' ';
		$sql .= 'ORDER BY t.geoLevel, t.geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID] = $r->geoTerm;
		}
		$rs->free();
		return $retArr;
	}

	public function getAcceptedGeoTermArr($geoLevelMax = 0){
		$retArr = array();
		$sql = 'SELECT geoThesID, geoTerm FROM geographicthesaurus ';
		if($geoLevelMax) $sql .= 'WHERE (geoLevel = '.$geoLevelMax.') ';
		$sql .= 'ORDER BY geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID] = $r->geoTerm;
		}
		$rs->free();
		return $retArr;
	}

	public function getGeoRankArr(){
		$rankArr = array();
		if(isset($GLOBALS['GEO_THESAURUS_RANKING']) && is_array($GLOBALS['GEO_THESAURUS_RANKING'])){
			$rankArr = $GLOBALS['GEO_THESAURUS_RANKING'];
		}
		else{
			$rankArr = array(10 => 'Oceans', 20 => 'Island Group', 30 => 'Island', 40 => 'Continent/Region', 50 => 'Country', 60 => 'ADM1', 70 => 'ADM2', 80 => 'ADM3',
				100 => 'City/Town', 110 => 'Place Name', 150 => 'Lake/Pond', 160 => 'River/Creek');
		}
		return $rankArr;
	}

	//Reporting and data transfer functions
	public function getThesaurusStatus(){
		$retArr = false;
		$fullCnt = 0;
		$sql = 'SELECT geoLevel, COUNT(*) as cnt FROM geographicthesaurus GROUP BY geoLevel';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['active'][$r->geoLevel] = $r->cnt;
			$fullCnt += $r->cnt;
		}
		$rs->free();

		if($fullCnt < 100){
			$sql = 'SELECT COUNT(*) as cnt FROM lkupcountry ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['lkup']['country'] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT COUNT(*) as cnt FROM lkupstateprovince ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['lkup']['state'] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT COUNT(*) as cnt FROM lkupcounty ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['lkup']['county'] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT COUNT(*) as cnt FROM lkupmunicipality ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['lkup']['municipality'] = $r->cnt;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function transferDeprecatedThesaurus(){
		$status = true;
		$sqlArr = array();
		$sqlArr[] = 'INSERT INTO geographicthesaurus(geoterm,iso2,iso3,numcode,category,geoLevel,termstatus)
			SELECT countryName, iso, iso3, numcode, "Country", 50 as geoLevel, 1 as termStatus FROM lkupcountry WHERE iso IS NOT NULL';

		$sqlArr[] = 'UPDATE geographicthesaurus SET acceptedID = (SELECT geoThesID FROM geographicthesaurus WHERE geoTerm = "United States") WHERE geoterm IN("USA","U.S.A.","United States of America")';

		$sqlArr[] = 'INSERT INTO geographicthesaurus(geoterm,abbreviation,parentID,category,geoLevel,termStatus)
			SELECT DISTINCT s.stateName, s.abbrev, t.geoThesID, "State", 60 as geoLevel, 1 as termStatus
			FROM lkupcountry c INNER JOIN lkupstateprovince s ON c.countryid = s.countryid
			INNER JOIN geographicthesaurus t ON c.iso = t.iso2
	        WHERE t.category = "country" AND t.termstatus = 1 AND t.acceptedID IS NULL';

		$sqlArr[] = 'INSERT INTO geographicthesaurus(geoterm,parentID,category,geoLevel,termStatus)
			SELECT DISTINCT REPLACE(REPLACE(REPLACE(c.countyName," Co.","")," County","")," Parish",""), t.geoThesID, "County", 70 as geoLevel, 1 as termStatus
			FROM lkupstateprovince s INNER JOIN lkupcounty c ON s.stateid = c.stateid
			INNER JOIN geographicthesaurus t ON s.stateName = t.geoterm
			WHERE t.category = "State" AND t.termstatus = 1';

		foreach($sqlArr as $sql){
			if(!$this->conn->query($sql)){
				$status = false;
				$this->warningArr[] = $this->conn->error;
			}
		}
		return $status;
	}

	//geoBoundary harvesting functions
	public function getGBCountryList(){
		$retArr = array();
		$contArr = $this->getContinentArr();
		$url = 'https://www.geoboundaries.org/api/current/gbOpen/ALL/ADM0/';
		$json = $this->getGeoboundariesJSON($url);
		$obj = json_decode($json);
		if($obj){
			foreach($obj as $countryObj){
				$key = $countryObj->boundaryISO;
				$retArr[$key]['id'] = $countryObj->boundaryID;
				$retArr[$key]['name'] = $countryObj->boundaryName;
				$retArr[$key]['canonical'] = $countryObj->boundaryCanonical;
				$retArr[$key]['license'] = $this->licenseTranslate($countryObj->boundaryLicense);
				$region = '';
				if(in_array($countryObj->Continent,$contArr)) $region = $countryObj->Continent;
				elseif(in_array($countryObj->{'UNSDG-subregion'},$contArr)) $region = $countryObj->{'UNSDG-subregion'};
				else $region = $countryObj->Continent.'/'.$countryObj->{'UNSDG-subregion'};
				if($region == 'Northern America') $region == 'North America';
				if($key == 'ATA') $region = 'Antartica';
				$retArr[$key]['region'] = $region;
				//$retArr[$key]['geoJSON'] = $countryObj->gjDownloadURL;
				//$retArr[$key]['simplifiedGeoJSON'] = $countryObj->simplifiedGeometryGeoJSON;
				$retArr[$key]['link'] = $countryObj->apiURL;
				$retArr[$key]['img'] = $countryObj->imagePreview;
			}
			ksort($retArr);
			//Check to see if country is already in thesaurus
			$sql = 'SELECT g.geoThesID, g.iso3, p.geoThesID AS polygonID
				FROM geographicthesaurus g LEFT JOIN geographicpolygon p ON g.geoThesID = p.geoThesID
				WHERE g.geoLevel = 50 AND g.acceptedID IS NULL AND g.iso3 IN("'.implode('","',array_keys($retArr)).'")';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->iso3]['geoThesID'] = $r->geoThesID;
				if($r->polygonID) $retArr[$r->iso3]['polygon'] = 1;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getGBGeoList($countryCode){
		$retArr = array();
		$contArr = $this->getContinentArr();
		$url = 'https://www.geoboundaries.org/api/current/gbOpen/'.$countryCode.'/ALL/';
		$json = $this->getGeoboundariesJSON($url);
		$obj = json_decode($json);
		if($obj){
			foreach($obj as $boundaryObj){
				$type = $boundaryObj->boundaryType;
				if(preg_match('/^ADM[0-3]{1}$/',$type)){
					$retArr[$type]['id'] = $boundaryObj->boundaryID;
					$retArr[$type]['canonical'] = $boundaryObj->boundaryCanonical;
					$retArr[$type]['year'] = $boundaryObj->boundaryYearRepresented;
					$retArr[$type]['license'] = $this->licenseTranslate($boundaryObj->boundaryLicense);
					$retArr[$type]['licenseSource'] = $boundaryObj->licenseSource;
					$retArr[$type]['sourceURL'] = $boundaryObj->boundarySourceURL;
					$region = '';
					if(in_array($boundaryObj->Continent,$contArr)) $region = $boundaryObj->Continent;
					elseif(in_array($boundaryObj->{'UNSDG-subregion'},$contArr)) $region = $boundaryObj->{'UNSDG-subregion'};
					else $region = $boundaryObj->Continent.'/'.$boundaryObj->{'UNSDG-subregion'};
					if($region == 'Northern America') $region == 'North America';
					if($countryCode == 'ATA') $region = 'Antartica';
					$retArr[$type]['region'] = $region;
					$retArr[$type]['geoJson'] = $boundaryObj->gjDownloadURL;
					$retArr[$type]['simpleGeoJson'] = $boundaryObj->simplifiedGeometryGeoJSON;
					$retArr[$type]['link'] = $boundaryObj->apiURL;
					$retArr[$type]['img'] = $boundaryObj->imagePreview;
				}
			}
			ksort($retArr);
			//Check to see if country is already in thesaurus
			$sql = 'SELECT g.geoThesID, g.iso3, p.geoThesID AS polygonID
				FROM geographicthesaurus g LEFT JOIN geographicpolygon p ON g.geoThesID = p.geoThesID
				WHERE g.geoLevel = 50 AND g.acceptedID IS NULL AND g.iso3 IN("'.$countryCode.'")';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['ADM0']['geoThesID'] = $r->geoThesID;
				if($r->polygonID) $retArr['ADM0']['polygon'] = 1;
			}
			$rs->free();
			$this->checkLowerDivision($retArr);
		}
		return $retArr;
	}

	private function licenseTranslate($licenseStr){
		$retStr = $licenseStr;
		if($licenseStr == 'Public Domain') $retStr = 'CC0';
		elseif($licenseStr == 'CC0 1.0 Universal (CC0 1.0) Public Domain Dedication') $retStr = 'CC0 1.0';
		elseif($licenseStr == 'Creative Commons Attribution 3.0 License') $retStr = 'CC BY 3.0';
		elseif($licenseStr == 'Creative Commons Attribution 3.0 Intergovernmental Organisations (CC BY 3.0 IGO)') $retStr = 'CC BY 3.0 IGO';
		elseif($licenseStr == 'Attribuzione 3.0 Italia (CC BY 3.0 IT)') $retStr = 'CC BY 3.0 IT';
		elseif($licenseStr == 'Creative Commons Attribution 4.0 (CC BY 4.0)') $retStr = 'CC BY 4.0';
		elseif($licenseStr == 'Creative Commons Attribution 4.0 International (CC BY 4.0)') $retStr = 'CC BY 4.0';
		elseif($licenseStr == 'Creative Commons Attribution-ShareAlike 2.0') $retStr = 'CC BY-SA 2.0';
		elseif($licenseStr == 'Creative Commons Attribution-ShareAlike 3.0 Unported') $retStr = 'CC BY-SA 3.0';
		elseif($licenseStr == 'Creative Commons Attribution-ShareAlike 4.0 International License') $retStr = 'CC BY-SA 4.0';
		elseif($licenseStr == 'Open Data Commons Open Database License 1.0') $retStr = 'ODbL';
		elseif($licenseStr == 'Open Government Licence v3.0') $retStr = 'OGL 3.0';

		return $retStr;
	}

	private function checkLowerDivision(&$retArr, $type = 'ADM1'){
		$admLevel = substr($type,-1);
		$geoLevel = 0;
		if($admLevel == 1) $geoLevel = 60;
		elseif($admLevel == 2) $geoLevel = 70;
		elseif($admLevel == 3) $geoLevel = 80;
		elseif($admLevel > 3) return false;
		if($geoLevel){
			$admNext = 'ADM'.($admLevel+1);
			if(isset($retArr[$type]['geoThesID']) && isset($retArr[$admNext])){
				$sql = 'SELECT g.geoThesID, g.iso3, p.geoThesID AS polygonID
					FROM geographicthesaurus g LEFT JOIN geographicpolygon p ON g.geoThesID = p.geoThesID
					WHERE g.geoLevel = '.$geoLevel.' AND g.acceptedID IS NULL AND g.parentID = '.$retArr[$type]['geoThesID'];
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$admNext]['geoThesID'] = $r->geoThesID;
					if($r->polygonID) $retArr[$admNext]['polygon'] = 1;
					$this->checkLowerDivision($retArr, $admNext);
				}
				$rs->free();
			}
		}
	}

	public function addGeoBoundary($gbID){
		$status = false;
		$url = 'https://www.geoboundaries.org/api/gbID/'.$gbID.'/';
		$json = $this->getGeoboundariesJSON($url);
		$obj = json_decode($json);



		return $status;
	}

	private function getGeoThesID($iso3, $type){
		$countryGeoThesID = 0;
		$sql = 'SELECT geoThesID FROM geographicthesaurus WHERE iso3 = "'.$iso3.'"';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$countryGeoThesID = $r->geoThesID;
		}
		$rs->free();
		if(!$countryGeoThesID) $countryGeoThesID = $this->insertGeoBoundary();
		if($type = 'ADM0') return $countryGeoThesID;
		if($countryGeoThesID){
			if($type = 'ADM1'){
				$this->getGeoThesID($iso3, 'ADM1');
			}
		}
	}

	private function insertGeoBoundary(){
		$retID = 0;
		$sql = '';
		if($this->conn->query($sql)){
			$retID = $this->conn->insert_id;
		}
		else{
			echo '<div>ERROR inserting geoBoundary: '.$this->conn->query().'</div>';
		}
		return $retID;
	}

	private function addGBPolygon($gbID){
		$status = false;
		$url = 'https://www.geoboundaries.org/api/gbID/'.$gbID.'/';
		$json = $this->getGeoboundariesJSON($url);
		$obj = json_decode($json);
		if(isset($obj->simplifiedGeometryGeoJSON)){
			$geoJson = $this->getGeoboundariesJSON($obj->simplifiedGeometryGeoJSON);
			$geoJson = $this->cleanGeoJson($geoJson);
			$sql = 'REPLACE INTO geographicpolygon(geoThesID, footprintPolygon, geoJson)
				SELECT geoThesID, ST_GeomFromGeoJSON(\''.$geoJson.'\'),\''.$geoJson.'\' FROM geographicthesaurus WHERE acceptedID IS NULL AND iso3 = "'.$obj->boundaryISO.'"';
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorMessage = $this->conn->error;
				echo 'ERROR adding geoJSON to database: '.$this->conn->error;
			}
		}
		return $status;
	}

	private function cleanGeoJson($geoJson){
		$jsonObj = json_decode($geoJson);
		$retObj = [];
		foreach($jsonObj->features as $fKey => $featureObj){
			foreach($featureObj->geometry->coordinates as $coordKey1 => $coordObj1){
				foreach($coordObj1 as $coordKey2 => $coordObj2){
					$jsonObj->features[$fKey]->geometry->coordinates[$coordKey1][$coordKey2][0] = round($coordObj2[0],6);
					$jsonObj->features[$fKey]->geometry->coordinates[$coordKey1][$coordKey2][1] = round($coordObj2[1],6);
				}
			}

		}
		return json_encode($jsonObj->features[0]->geometry);
	}

	private function getGeoboundariesJSON($url){
		$resJson = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$resJson = curl_exec($ch);
		if(!$resJson){
			$this->errorMessage = 'FATAL CURL ERROR: '.curl_error($ch).' (#'.curl_errno($ch).')';
			echo 'ERROR: '.$this->errorMessage;
			//$header = curl_getinfo($ch);
		}
		curl_close($ch);
		return $resJson;
	}

	private function getContinentArr(){
		return array('Asia','Caribbean','Oceania','Africa','Europe','Central America','Northern America','South America');
	}

	// Setters and getters




}
?>