<?php
include_once('OccurrenceManager.php');
include_once('OccurrenceAccessStats.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');

class OccurrenceMapManager extends OccurrenceManager {

	private $recordCount = 0;
	private $collArrIndex = 0;

	// Default values for request variables
	private const DEFAULT_GRID_SIZE=60;
	private const MIN_CLUSTER_SETTING=10;
	private const MAP_RECORD_LIMIT=15000;

	public function __construct(){
		parent::__construct();
		$this->readGeoRequestVariables();
		$this->setGeoSqlWhere();
	}

	public function __destruct(){
		parent::__destruct();
	}

	private function readGeoRequestVariables() {
		if(array_key_exists('gridSizeSetting',$_REQUEST)){
			$this->searchTermArr['gridSizeSetting'] = $this->cleanInStr($_REQUEST['gridSizeSetting']);
		}
		if(array_key_exists('minClusterSetting',$_REQUEST)){
			$this->searchTermArr['minClusterSetting'] = $this->cleanInStr($_REQUEST['minClusterSetting']);
		}
		if(array_key_exists('clusterSwitch',$_REQUEST)){
			$this->searchTermArr['clusterSwitch'] = $this->cleanInStr($_REQUEST['clusterSwitch']);
		}
		if(array_key_exists('cltype',$_REQUEST) && $_REQUEST['cltype']){
			if($_REQUEST['cltype'] == 'all') $this->searchTermArr['cltype'] = 'all';
			else $this->searchTermArr['cltype'] = 'vouchers';
		}
	}

	public function buildMapSqlQuery($start = null, $limit = null, $select = null) {
		if(!$select) {
			$select = 'o.occid, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS identifier, o.eventdate, '.
				'o.sciname, IF(ts.family IS NULL, o.family, ts.family) as family, o.tidinterpreted, o.DecimalLatitude, o.DecimalLongitude, o.collid, o.catalogNumber, '.
				'o.othercatalognumbers';
		}

		$sql = 'SELECT ' . $select . ' FROM omoccurrences o ';

		if (!empty($GLOBALS['ACTIVATE_PALEO'])) {
			$sql = $this->getPaleoSqlWith() . $sql;
		}

		$this->sqlWhere .= 'AND (ts.taxauthid = 1 OR ts.taxauthid IS NULL) ';
		$sql .= $this->getTableJoins($this->sqlWhere);
		$sql .= $this->sqlWhere;

		if(is_numeric($start) && $limit){
			$sql .= "LIMIT " . $start . "," . $limit;
		}

		return $sql;
	}

	public function getCollections() {
		$collections = [];
		$colResult= QueryUtil::tryExecuteQuery($this->conn,'SELECT collid, institutionCode, collectionCode, collectionName, CollType IN("Observations","General Observations") as isObservation FROM omcollections');
		while($record = $colResult->fetch_object()) {
			if (!array_key_exists($record->collid, $collections)) {
				$collections[$record->collid] = $record;
			}
		}

		return $collections;
	}

	//Coordinate retrival functions
	public function getCoordinateMap($start, $limit) {
		if(!$this->sqlWhere) {
			return [
				'taxaArr' => [],
				'collArr' => [],
				'recordArr' => []
			];
		}
		
		$statsManager = new OccurrenceAccessStats();

		$result = QueryUtil::tryExecuteQuery($this->conn, $this->buildMapSqlQuery($start, $limit));
		if(!$result) {
			$this->errorMessage = 'ERROR executing coordinate query: ' . $this->conn->error;
			echo json_encode([$this->errorMessage]);
			return array();
		}

		$color = 'e69e67';
		$host = GeneralUtil::getDomain() . $GLOBALS['CLIENT_ROOT'];
		$occidArr = [];
		$recordArr = [];
		$taxaArr = [];
		$collArr = [];
		$collections = $this->getCollections();

		while($record = $result->fetch_object()) {
			$collName = $collections[$record->collid]->collectionName;
			if (!array_key_exists($record->tidinterpreted, $taxaArr)) {
				$taxaArr[$record->tidinterpreted] = [
					'sn' => $record->sciname,
					'tid' => $record->tidinterpreted,
					'family' => $record->family,
					'color' => $color,
				];
			}

			//Collect all Collections
			if (!array_key_exists($record->collid, $collArr)) {
				$collArr[$record->collid] = [
					'name' => $collName,
					'collid' => $record->collid,
					'color' => $color,
				];
			}

			//Collect all records
			array_push($recordArr, [
				'id' => $record->identifier, 
				'tid' => $this->htmlEntities($record->tidinterpreted), 
				'catalogNumber' => $record->catalogNumber, 
				'eventdate' => $record->eventdate, 
				'sciname' => $record->sciname, 
				'collid' => $record->collid, 
				'family' => $record->family,
				'occid' => $record->occid,
				'host' => $host,
				'collname' => $collName,
				'type' => $collections[$record->collid]->isObservation? 'observation' : 'specimen',
				'lat' => $record->DecimalLatitude,
				'lng' => $record->DecimalLongitude,
			]);

			$occidArr[] = $record->occid;
		}

		$result->free();

		$statsManager->recordAccessEventByArr($occidArr, 'map');

		return [
			'taxaArr' => $taxaArr, 
			'collArr' => $collArr, 
			'recordArr' => $recordArr
		];
	}

	// TODO (Logan) Is being used but is like duplicate of `getCoordinateMap`
	public function getMappingData($recLimit, $extraFieldArr = null){
		//Used for simple maps occurrence and taxon maps, and also KML download functions
		$start = 0;
		if(!$this->sqlWhere) $this->setSqlWhere();
		$coordArr = array();
		if($this->sqlWhere){
			$statsManager = new OccurrenceAccessStats();
			$sql = 'SELECT DISTINCT o.occid, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, o.sciname, o.tidinterpreted,
				o.decimallatitude, o.decimallongitude, o.catalognumber, o.othercatalognumbers, c.institutioncode, c.collectioncode, c.colltype ';
			if(isset($extraFieldArr) && is_array($extraFieldArr)){
				foreach($extraFieldArr as $fieldName){
					$sql .= ', o.' . $fieldName . ' ';
				}
			}
			$sql .= 'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid ';
			$sql .= $this->getTableJoins($this->sqlWhere);
			$sql .= $this->sqlWhere;
			if(is_numeric($start) && $recLimit && is_numeric($recLimit)) $sql .= "LIMIT ".$start.",".$recLimit;
			// echo '<div>SQL: ' . $sql . '</div>';
			$rs = QueryUtil::tryExecuteQuery($this->conn, $sql);
			if(!$rs) {
				$this->errorMessage = 'ERROR executing mapping data query: ' . $this->conn->error;
				return array();
			}
			$occidArr = array();
			while($r = $rs->fetch_assoc()){
				$sciname = $r['sciname'];
				if(!$sciname) $sciname = 'undefined';
				$coordArr[$sciname][$r['occid']]['instcode'] = $r['institutioncode'];
				if($r['collectioncode']) $coordArr[$sciname][$r['occid']]['collcode'] = $r['collectioncode'];
				$collType = 'obs';
				if(stripos($r['colltype'],'specimen')) $collType = 'spec';
				$coordArr[$sciname][$r['occid']]['colltype'] = $collType;
				if($r['catalognumber']) $coordArr[$sciname][$r['occid']]['catnum'] = $r['catalognumber'];
				if($r['othercatalognumbers']) $coordArr[$sciname][$r['occid']]['ocatnum'] = $r['othercatalognumbers'];
				if($r['tidinterpreted']) $coordArr[$sciname]['tid'] = $r['tidinterpreted'];
				$coordArr[$sciname][$r['occid']]['collector'] = $r['collector'];
				$coordArr[$sciname][$r['occid']]['lat'] = $r['decimallatitude'];
				$coordArr[$sciname][$r['occid']]['lng'] = $r['decimallongitude'];
				if(isset($extraFieldArr) && is_array($extraFieldArr)){
					reset($extraFieldArr);
					foreach($extraFieldArr as $fieldName){
						if(isset($r[$fieldName])) $coordArr[$sciname][$r['occid']][$fieldName] = $r[$fieldName];
					}
				}
				$occidArr[] = $r['occid'];
			}
			$rs->free();
			$statsManager->recordAccessEventByArr($occidArr, 'map');
		}
		return $coordArr;
	}

	//SQL where functions
	private function setGeoSqlWhere(){
		global $USER_RIGHTS;
		if($sqlWhere = $this->getSqlWhere()){
			if($this->searchTermArr) {
				$sqlWhere .= ($sqlWhere?' AND ':' WHERE ').'(o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ';
				if(!empty($this->searchTermArr['clid'])) {
					if($this->voucherManager->getClFootprint()){
						//Set Footprint for map to load
						$this->setSearchTerm('footprintGeoJson', $this->voucherManager->getClFootprint());
						if(isset($this->searchTermArr['cltype']) && $this->searchTermArr['cltype'] == 'all') {
							$sqlWhere .= "AND (ST_Within(p.lngLatPoint,ST_GeomFromGeoJSON('". $this->voucherManager->getClFootprint()." '))) ";
						}
					}
				}
			}

			//Check and exclude records with sensitive species protections
			if(array_key_exists('SuperAdmin',$USER_RIGHTS) || array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('RareSppAdmin',$USER_RIGHTS) || array_key_exists('RareSppReadAll',$USER_RIGHTS)){
				//Is global rare species reader, thus do nothing to sql and grab all records
			}
			elseif(isset($USER_RIGHTS['RareSppReader']) || isset($USER_RIGHTS['CollEditor'])){
				$securityCollArr = array();
				if(isset($USER_RIGHTS['CollEditor'])) $securityCollArr = $USER_RIGHTS['CollEditor'];
				if(isset($USER_RIGHTS['RareSppReader'])) $securityCollArr = array_unique(array_merge($securityCollArr, $USER_RIGHTS['RareSppReader']));
				$sqlWhere .= ($sqlWhere ? ' AND' : ' WHERE' ) . ' (o.CollId IN ('.implode(',',$securityCollArr).') OR (o.recordSecurity = 0)) ';
			}
			elseif(!empty($sqlWhere)){
				$sqlWhere .= ($sqlWhere ? ' AND' : ' WHERE' ) . ' (o.recordSecurity = 0) ';
			}

			$sqlWhere .=  ' AND ((o.decimallatitude BETWEEN -90 AND 90) AND (o.decimallongitude BETWEEN -180 AND 180)) ';
			$this->sqlWhere = $sqlWhere;
		}
		else{
			//Don't allow someone to query all occurrences if there are no conditions
			$this->sqlWhere = 'WHERE o.occid IS NULL ';
		}
	}

	//Shape functions

	public function writeKMLFile($recLimit, $extraFieldArr = null){
		//Output data
		$fileName = $GLOBALS['DEFAULT_TITLE'];
		if($fileName){
			if(strlen($fileName) > 10) $fileName = substr($fileName,0,10);
			$fileName = str_replace(".","",$fileName);
			$fileName = str_replace(" ","_",$fileName);
		}
		else{
			$fileName = "symbiota";
		}
		$fileName .= time().".kml";
		
		header ('Content-type: text/xml');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');

		$kml = tmpfile();
		
		fwrite($kml, "<?xml version='1.0' encoding='".$GLOBALS['CHARSET']."'?>\n");
		fwrite($kml, "<kml xmlns='http://www.opengis.net/kml/2.2'>\n");
		fwrite($kml, "<Folder>\n<name>".$GLOBALS['DEFAULT_TITLE']." Specimens - ".date('j F Y g:ia')."</name>\n");
		fwrite($kml, "<Document>\n");

		$googleIconArr = array('pushpin/ylw-pushpin','pushpin/blue-pushpin','pushpin/grn-pushpin','pushpin/ltblu-pushpin',
			'pushpin/pink-pushpin','pushpin/purple-pushpin', 'pushpin/red-pushpin','pushpin/wht-pushpin','paddle/blu-blank',
			'paddle/grn-blank','paddle/ltblu-blank','paddle/pink-blank','paddle/wht-blank','paddle/blu-diamond','paddle/grn-diamond',
			'paddle/ltblu-diamond','paddle/pink-diamond','paddle/ylw-diamond','paddle/wht-diamond','paddle/red-diamond','paddle/purple-diamond',
			'paddle/blu-circle','paddle/grn-circle','paddle/ltblu-circle','paddle/pink-circle','paddle/ylw-circle','paddle/wht-circle',
			'paddle/red-circle','paddle/purple-circle','paddle/blu-square','paddle/grn-square','paddle/ltblu-square','paddle/pink-square',
			'paddle/ylw-square','paddle/wht-square','paddle/red-square','paddle/purple-square','paddle/blu-stars','paddle/grn-stars',
			'paddle/ltblu-stars','paddle/pink-stars','paddle/ylw-stars','paddle/wht-stars','paddle/red-stars','paddle/purple-stars');

		$CHUNK_SIZE = 40000;
		$RECORD_CAP = 3000000;

		$collections = $this->getCollections();
		$previousSciname = false;
		// Depending on how the kml goes this could get removed
		$openFolder = false;
		$keepProcessing = true;
		$lastOccid = false;
		$currentCount = 0;

		while($keepProcessing) {
			$queryTime= microtime(true);
			$sql = $this->buildMapSqlQuery() . ($lastOccid? ' AND occid > ' . $lastOccid: '') . ' LIMIT ' . $CHUNK_SIZE;
			$result = QueryUtil::executeQuery($this->conn, $sql);

			$currentCount += $result->num_rows;

			if($currentCount >= $RECORD_CAP) {
				$keepProcessing = false;
			} else if($result->num_rows === $CHUNK_SIZE) {
				$keepProcessing = true;
			} else {
				$keepProcessing = false;
			}

			while($record = $result->fetch_object()) {
				$lastOccid = $record->occid;
				$sciname = $record->sciname ?? 'undefined';

				// TODO figure out how to do this with decent performance
				// count($googleIconArr) === 44?
				// if($previousSciname != $sciname) {
				// 	if($openFolder) {
				// 		fwrite($kml, "</Folder>\n");
				// 		$openFolder = false;
				// 	}
				//
				// 	$iconStr = $googleIconArr[$currentCount % 44];
				// 	fwrite($kml, "<Style id='sn_".$iconStr."'>\n");
				// 	fwrite($kml, "<IconStyle><scale>1.1</scale><Icon>");
				// 	fwrite($kml, "<href>http://maps.google.com/mapfiles/kml/" . htmlspecialchars($iconStr, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . ".png</href>");
				// 	fwrite($kml, "</Icon><hotSpot x='20' y='2' xunits='pixels' yunits='pixels'/></IconStyle>\n</Style>\n");
				// 	fwrite($kml, "<Style id='sh_".$iconStr."'>\n");
				// 	fwrite($kml, "<IconStyle><scale>1.3</scale><Icon>");
				// 	fwrite($kml, "<href>http://maps.google.com/mapfiles/kml/".$iconStr.".png</href>");
				// 	fwrite($kml, "</Icon><hotSpot x='20' y='2' xunits='pixels' yunits='pixels'/></IconStyle>\n</Style>\n");
				// 	fwrite($kml, "<StyleMap id='".htmlspecialchars(str_replace(" ", "_", $sciname), ENT_QUOTES)."'>\n");
				// 	fwrite($kml, "<Pair><key>normal</key><styleUrl>#sn_".$iconStr."</styleUrl></Pair>");
				// 	fwrite($kml, "<Pair><key>highlight</key><styleUrl>#sh_".$iconStr."</styleUrl></Pair>");
				// 	fwrite($kml, "</StyleMap>\n");
				// 	fwrite($kml, "<Folder><name>".htmlspecialchars($sciname, ENT_QUOTES)."</name>\n");
				//
				// 	$openFolder = true;
				// }

				fwrite($kml, '<Placemark>');
				fwrite($kml, '<name>'.htmlspecialchars($record->identifier, ENT_QUOTES).'</name>');
				fwrite($kml, '<ExtendedData>');
				if($record->collid) {
					$collectionCode	= $collections[$record->collid]->collectionCode;
					$institutionCode = $collections[$record->collid]->collectionCode;
					if($collectionCode) {
						fwrite($kml, '<Data name="collectioncode">'.htmlspecialchars($collectionCode, ENT_QUOTES).'</Data>');
					}

					if($institutionCode) {
						fwrite($kml, '<Data name="institutioncode">'.htmlspecialchars($institutionCode, ENT_QUOTES).'</Data>');
					}
				}
				fwrite($kml, '<Data name="catalognumber">'.($record->catalogNumber?htmlspecialchars($record->catalogNumber, ENT_QUOTES):'').'</Data>');
				fwrite($kml, '<Data name="DataSource">Data retrieved from '.$GLOBALS['DEFAULT_TITLE'].' Data Portal</Data>');
				$recUrl = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $recUrl = 'https://';
				$recUrl .= $_SERVER['SERVER_NAME'].$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$record->occid;
				fwrite($kml, '<Data name="RecordURL">' . htmlspecialchars($recUrl, ENT_QUOTES) . '</Data>');

				if(isset($extraFieldArr) && is_array($extraFieldArr)) {
					reset($extraFieldArr);
					foreach($extraFieldArr as $fieldName){
						if(isset($record->$fieldName)) fwrite($kml, '<Data name="'.$fieldName.'">'.htmlspecialchars($record->$fieldName, ENT_QUOTES).'</Data>');
					}
				}

				fwrite($kml, '</ExtendedData>');
				fwrite($kml, '<styleUrl>#'.htmlspecialchars(str_replace(' ','_',$sciname), ENT_QUOTES).'</styleUrl>');
				fwrite($kml, '<Point><coordinates>'.$record->DecimalLongitude.','.$record->DecimalLatitude.'</coordinates></Point>');
				fwrite($kml, "</Placemark>\n");
				$currentCount++;
				$previousSciname = $sciname;
			}

			$result->free();
		}

		if($openFolder) {
			fwrite($kml, "</Folder>\n");
			$openFolder = false;
		}

		fwrite($kml, "</Document>\n</Folder>\n</kml>\n");

		readfile(stream_get_meta_data($kml)['uri']);
		fclose($kml);
	}

	//Misc support functions
	private function htmlEntities($string){
		return htmlspecialchars($string ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
	}
}
?>
