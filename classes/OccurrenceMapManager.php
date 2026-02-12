<?php
include_once('OccurrenceManager.php');
include_once('OccurrenceAccessStats.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');

class OccurrenceMapManager extends OccurrenceManager {

	private $recordCount = 0;
	private $collArrIndex = 0;

	public const DEFAULT_GRID_SIZE=60;
	public const MIN_CLUSTER_SETTING=10;
	public const MAP_RECORD_LIMIT=30000;
	public const DEFAULT_CLUSTER_SWITCH='y';

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
		} else {
			$this->searchTermArr['gridSizeSetting'] = self::DEFAULT_GRID_SIZE;
		}

		if(array_key_exists('minClusterSetting',$_REQUEST)){
			$this->searchTermArr['minClusterSetting'] = $this->cleanInStr($_REQUEST['minClusterSetting']);
		} else {
			$this->searchTermArr['minClusterSetting'] = self::MIN_CLUSTER_SETTING;
		}

		if(array_key_exists('clusterSwitch',$_REQUEST)){
			$this->searchTermArr['clusterSwitch'] = in_array($_REQUEST['clusterSwitch'], ['n', 'y'])?
				$this->cleanInStr($_REQUEST['clusterSwitch']):
				self::DEFAULT_CLUSTER_SWITCH;
		} else {
			$this->searchTermArr['clusterSwitch'] = self::DEFAULT_CLUSTER_SWITCH;
		}

		if(array_key_exists('reclimit',$_REQUEST) && is_numeric($_REQUEST['reclimit'])){
			$recLimit = intval($_REQUEST['reclimit']);
			$this->searchTermArr['reclimit'] = $recLimit > self::MAP_RECORD_LIMIT? 
				self::MAP_RECORD_LIMIT: 
				$recLimit;
		} else {
			$this->searchTermArr['reclimit'] = self::MAP_RECORD_LIMIT;
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
	public function getCoordinateMap($start = 0) {
		if(!$this->sqlWhere) {
			return [
				'taxaArr' => [],
				'collArr' => [],
				'recordArr' => []
			];
		}
		
		$statsManager = new OccurrenceAccessStats();

		$result = QueryUtil::tryExecuteQuery($this->conn, $this->buildMapSqlQuery($start, $this->searchTermArr['reclimit']));
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

		$KML_CHUNK_SIZE = 40000;
		$KML_RECORD_CAP = 3000000;

		$collections = $this->getCollections();
		$previousSciname = false;
		// Depending on how the kml goes this could get removed
		$openFolder = false;
		$keepProcessing = true;
		$lastOccid = false;
		$currentCount = 0;

		while($keepProcessing) {
			$queryTime= microtime(true);
			$sql = $this->buildMapSqlQuery() . ($lastOccid? ' AND occid > ' . $lastOccid: '') . ' LIMIT ' . $KML_CHUNK_SIZE;
			$result = QueryUtil::executeQuery($this->conn, $sql);

			$currentCount += $result->num_rows;

			if($currentCount >= $KML_RECORD_CAP) {
				$keepProcessing = false;
			} else if($result->num_rows === $KML_CHUNK_SIZE) {
				$keepProcessing = true;
			} else {
				$keepProcessing = false;
			}

			while($record = $result->fetch_object()) {
				$lastOccid = $record->occid;
				$sciname = $record->sciname ?? 'undefined';

				fwrite($kml, '<Placemark>');
				fwrite($kml, '<name>' . htmlspecialchars($record->identifier, ENT_QUOTES) . '</name>');
				fwrite($kml, '<ExtendedData>');
				if($record->collid) {
					$collectionCode	= $collections[$record->collid]->collectionCode;
					$institutionCode = $collections[$record->collid]->collectionCode;
					if($collectionCode) {
						fwrite($kml, '<Data name="collectioncode">' . htmlspecialchars($collectionCode, ENT_QUOTES) . '</Data>');
					}

					if($institutionCode) {
						fwrite($kml, '<Data name="institutioncode">' . htmlspecialchars($institutionCode, ENT_QUOTES) . '</Data>');
					}
				}
				fwrite($kml, '<Data name="catalognumber">' . ($record->catalogNumber? htmlspecialchars($record->catalogNumber, ENT_QUOTES): '') . '</Data>');
				fwrite($kml, '<Data name="DataSource">Data retrieved from ' . $GLOBALS['DEFAULT_TITLE'] . ' Data Portal</Data>');
				$recUrl = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $recUrl = 'https://';
				$recUrl .= $_SERVER['SERVER_NAME'] . $GLOBALS['CLIENT_ROOT'] . '/collections/individual/index.php?occid=' .$record->occid;
				fwrite($kml, '<Data name="RecordURL">' . htmlspecialchars($recUrl, ENT_QUOTES) . '</Data>');

				if(isset($extraFieldArr) && is_array($extraFieldArr)) {
					reset($extraFieldArr);
					foreach($extraFieldArr as $fieldName){
						if(isset($record->$fieldName)) fwrite($kml, '<Data name="'.$fieldName.'">' . htmlspecialchars($record->$fieldName, ENT_QUOTES).'</Data>');
					}
				}

				fwrite($kml, '</ExtendedData>');
				fwrite($kml, '<styleUrl>#' . htmlspecialchars(str_replace(' ','_',$sciname), ENT_QUOTES) . '</styleUrl>');
				fwrite($kml, '<Point><coordinates>' . $record->DecimalLongitude . ',' . $record->DecimalLatitude . '</coordinates></Point>');
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
