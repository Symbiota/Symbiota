<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class NpsReport{

	private $conn;
	private $siteArr;
	private $occurArr;
	private $itisTsnArr = array();
	private $datasetID;
	private $targetYear;
	private $debugMode = false;
	private $errorStr;

 	public function __construct(){
 		set_time_limit(500);
		$this->conn = MySQLiConnectionFactory::getCon("write");
		$this->siteArr = array('40'=>'GRSM','99'=>'LECO','110'=>'BLDE','131'=>'YELL');
		$this->debugMode = false;
		if($this->debugMode) echo '<ul>';
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
		if($this->debugMode) echo '</ul>';
 	}

	public function generateNpsReport(){
		$status = false;
		if($this->datasetID){
			$status = $this->setOccurArr();
			if($status) $this->exportData();
			else{
				header("Content-Type: text/html; charset=".$GLOBALS['CHARSET']);
				echo '<h2>Dataset does not exist for that year</h2>';
			}
		}
		return $status;
	}

	private function setOccurArr(){
		$status = false;
		$sql = 'SELECT o.occid, o.family, o.tidInterpreted, o.sciname, o.scientificNameAuthorship, t.unitname1, CONCAT_WS(" ",t.unitname2,t.unitind3,t.unitname3) as species, t.rankid, '.
			'o.individualCount, o.verbatimAttributes, o.habitat, o.recordedBy, o.recordNumber, o.eventDate, occurrenceID, '.
			'o.identifiedBy, o.dateIdentified, o.locality, o.stateProvince, o.county, o.geodeticDatum, o.decimalLatitude, o.decimalLongitude, o.verbatimCoordinates, '.
			'o.minimumElevationInMeters, o.associatedTaxa, o.lifeStage, o.sex, o.dateEntered '.
			'FROM omoccurdatasetlink d INNER JOIN omoccurrences o ON d.occid = o.occid '.
			'LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
			'WHERE (d.datasetid = '.$this->datasetID.') ';
		if($this->targetYear) $sql .= 'AND (o.dateEntered BETWEEN "'.($this->targetYear).'-01-01" AND "'.$this->targetYear.'-12-31")';
		if($this->debugMode) echo '<li>'.$sql.'</li>';
		$rs = $this->conn->query($sql);
		if($this->debugMode) echo '<li>Starting to output '.$rs->num_rows.' records</li>';
		$tidArr = array();
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$status = true;
			if($this->debugMode) echo '<li>#'.++$cnt.': Processing record: '.$r->occid.' ('.date('h:i:s').')</li>';
			$this->occurArr[$r->occid]['nspCatNum'] = '';
			$this->occurArr[$r->occid]['npsAccNumb'] = '';
			$this->occurArr[$r->occid]['class1'] = 'BIOLOGY';
			$this->occurArr[$r->occid]['kingdom'] = '';
			$this->occurArr[$r->occid]['phylum'] = '';
			$this->occurArr[$r->occid]['class'] = '';
			$this->occurArr[$r->occid]['order'] = '';
			$this->occurArr[$r->occid]['family'] = $r->family;
			$this->occurArr[$r->occid]['genus'] = ($r->rankid > 179?$r->unitname1:'');
			$this->occurArr[$r->occid]['species'] = $r->species;
			$this->occurArr[$r->occid]['author'] = $r->scientificNameAuthorship;
			$this->occurArr[$r->occid]['common'] = (!$r->tidInterpreted || !$r->rankid?$r->sciname:'');
			$this->occurArr[$r->occid]['tsn'] = $this->getItisTSN($r->sciname,$r->tidInterpreted);
			$this->occurArr[$r->occid]['count'] = $r->individualCount;
			$this->occurArr[$r->occid]['quantity'] = '0.0';
			$this->occurArr[$r->occid]['unit'] = 'EA';
			$this->occurArr[$r->occid]['desc'] = $r->verbatimAttributes;
			$this->occurArr[$r->occid]['dim'] = '';
			$this->occurArr[$r->occid]['coll'] = $r->recordedBy;
			$this->occurArr[$r->occid]['collNum'] = $r->recordNumber;
			$this->occurArr[$r->occid]['collDate'] = $r->eventDate;
			$this->occurArr[$r->occid]['mainCycle'] = '';
			$this->occurArr[$r->occid]['cond'] = '';
			$this->occurArr[$r->occid]['condDesc'] = '';
			$this->occurArr[$r->occid]['studyNum'] = '';
			$this->occurArr[$r->occid]['otherNum'] = $r->occurrenceID;
			$this->occurArr[$r->occid]['EminentFig'] = '';
			$this->occurArr[$r->occid]['EminentOrg'] = '';
			$this->occurArr[$r->occid]['Cataloger'] = '';
			$this->occurArr[$r->occid]['CatDate'] = $r->dateEntered;
			$this->occurArr[$r->occid]['idBy'] = $r->identifiedBy;
			$this->occurArr[$r->occid]['dateId'] = $r->dateIdentified;
			$this->occurArr[$r->occid]['ReproMethod'] = '';
			$this->occurArr[$r->occid]['locality'] = $r->locality;
			$this->occurArr[$r->occid]['Unit'] = 'PARK';
			$this->occurArr[$r->occid]['TRS'] = '';
			$this->occurArr[$r->occid]['County'] = $r->county;
			$this->occurArr[$r->occid]['State'] = $r->stateProvince;
			$this->occurArr[$r->occid]['Datum'] = $r->geodeticDatum;
			$this->occurArr[$r->occid]['Watrbody'] = '';
			$this->occurArr[$r->occid]['Drainage'] = '';
			$this->occurArr[$r->occid]['utm'] = $r->verbatimCoordinates;
			$this->occurArr[$r->occid]['latDeg'] = '';
			$this->occurArr[$r->occid]['latMin'] = '';
			$this->occurArr[$r->occid]['latSec'] = '';
			if($r->decimalLatitude){
				$latArr = $this->parseCoord($r->decimalLatitude);
				$this->occurArr[$r->occid]['latDeg'] = $latArr['deg'];
				$this->occurArr[$r->occid]['latMin'] = $latArr['min'];
				$this->occurArr[$r->occid]['latSec'] = $latArr['sec'];
			}
			$this->occurArr[$r->occid]['lngDeg'] = '';
			$this->occurArr[$r->occid]['lngMin'] = '';
			$this->occurArr[$r->occid]['lngSec'] = '';
			if($r->decimalLongitude){
				$lngArr = $this->parseCoord($r->decimalLongitude);
				$this->occurArr[$r->occid]['lngDeg'] = $lngArr['deg'];
				$this->occurArr[$r->occid]['lngMin'] = $lngArr['min'];
				$this->occurArr[$r->occid]['lngSec'] = $lngArr['sec'];
			}
			$this->occurArr[$r->occid]['elev'] = $r->minimumElevationInMeters;
			$this->occurArr[$r->occid]['depth'] = '';
			$this->occurArr[$r->occid]['Depos'] = '';
			$this->occurArr[$r->occid]['habComm'] = '';
			$habitatArr = explode(';',$r->habitat);
			$habitatStr = '';
			$slopeStr = '';
			$aspectStr = '';
			$soilStr = '';
			foreach($habitatArr as $habStr){
				if(stripos($habStr,'slope gradient') !== false) $slopeStr = trim($habStr);
				elseif(stripos($habStr,'slope aspect') !== false) $aspectStr = trim($habStr);
				elseif(stripos($habStr,'soil type order') !== false) $soilStr = trim($habStr);
				else $habitatStr .= $habStr.'; ';
			}

			$this->occurArr[$r->occid]['habitat'] = trim($habitatStr,'; ');
			$this->occurArr[$r->occid]['slope'] = $slopeStr;
			$this->occurArr[$r->occid]['aspect'] = $aspectStr;
			$this->occurArr[$r->occid]['soilType'] = $soilStr;
			$this->occurArr[$r->occid]['forPerSub'] = '';
			$this->occurArr[$r->occid]['assocSp'] = $r->associatedTaxa;
			$this->occurArr[$r->occid]['typeSpec'] = '';
			$this->occurArr[$r->occid]['Endang'] = '';
			$this->occurArr[$r->occid]['teDate'] = '';
			$this->occurArr[$r->occid]['Rare'] = '';
			$this->occurArr[$r->occid]['ExoticNative'] = '';
			$this->occurArr[$r->occid]['age'] = $r->lifeStage;
			$this->occurArr[$r->occid]['sex'] = $r->sex;
			$this->occurArr[$r->occid]['ctrlProp'] = '';
			$this->occurArr[$r->occid]['location'] = 'ASU - Alameda Facility';
			$this->occurArr[$r->occid]['objStatus'] = 'STORAGE';
			$this->occurArr[$r->occid]['statusDate'] = $r->dateEntered;
			$this->occurArr[$r->occid]['catFolder'] = '';
			if($r->tidInterpreted) $tidArr[$r->tidInterpreted][] = $r->occid;
			if($this->debugMode){
				ob_flush();
				flush();
			}
		}
		$rs->free();
		$this->setTaxonomy($tidArr);
		$this->setVernacular($tidArr);
		$this->setAdditionalIdentifiers();
		return $status;
	}

	private function parseCoord($decCoord){
		$retArr = array();
		if($decCoord < 0) $decCoord = -1*$decCoord;
		$deg = floor($decCoord);
		$retArr['deg'] = $deg;
		$fraction = $decCoord - $deg;
		$min = $fraction*60;
		$retArr['min'] = floor($min);
		$retArr['sec'] = ($min-$retArr['min'])*60;
		return $retArr;
	}

	private function getItisTSN($sciname, $tid){
		if(isset($this->itisTsnArr[$sciname])) return $this->itisTsnArr[$sciname];
		$tsn = $this->getTsnFromDatabase($tid);
		if(!$tsn && strtolower($sciname) != 'epilithon'){
			//Grab TSN via ITIS web services
			$url = 'https://www.itis.gov/ITISWebService/services/ITISService/searchByScientificName?srchKey='.str_replace(' ','%20',$sciname);
			if($this->debugMode) echo '<li style="margin-left:15px">'.$url.'</li>';
			$retArr = $this->getContentString($url);
			if(isset($retArr['str'])){
				$xmlContent = $retArr['str'];
				$doc = new DOMDocument();
				if(@$doc->loadXML($xmlContent)){
					$nodes = $doc->getElementsByTagName('tsn');
					if($nodes->length) $tsn = $nodes->item(0)->nodeValue;
					if($tsn) $this->loadTsnIntoDatabase($tid, $tsn);
				}
			}
		}
		$this->itisTsnArr[$sciname] = $tsn;
		return $tsn;
	}

	private function getTsnFromDatabase($tid){
		$tsn = '';
		if($tid){
			$sql = 'SELECT sourceIdentifier FROM taxaresourcelinks WHERE sourceName = "ITIS" AND tid = '.$tid;
			if($this->debugMode) echo '<li style="margin-left:15px">'.$sql.'</li>';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$tsn = $r->sourceIdentifier;
			}
			$rs->free();
		}
		return $tsn;
	}

	private function loadTsnIntoDatabase($tid, $tsn){
		if($tid && $tsn){
			$sql = 'INSERT INTO taxaresourcelinks(tid,sourceName,sourceIdentifier) VALUES('.$tid.',"ITIS","'.$tsn.'")';
			if($this->debugMode) echo '<li style="margin-left:15px">'.$sql.'</li>';
			if(!$this->conn->query($sql)){
				if($this->debugMode) echo '<li style="margin-left:30px">ERROR adding ITIS TSN to database: '.$this->conn->error.'</li>';
			}
		}
	}

	private function getContentString($url){
		$retArr = array();
		if($url){
			if($fh = @fopen($url, 'r')){
				stream_set_timeout($fh, 5);
				$contentStr = '';
				while($line = fread($fh, 1024)){
					$contentStr .= trim($line);
				}
				fclose($fh);
				$retArr['str'] = $contentStr;
			}
			if(isset($http_response_header[0])){
				//Get error code
				$statusStr = $http_response_header[0];
				if(preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$statusStr, $out)){
					$retArr['code'] = intval($out[1]);
				}
			}
		}
		return $retArr;
	}

	private function setTaxonomy(&$tidArr){
		if($tidArr){
			$taxaArr = array();
			$sql = 'SELECT e.tid, t.sciname, t.rankid FROM taxaenumtree e INNER JOIN taxa t ON e.parentTid = t.tid WHERE e.tid IN('.implode(',',array_keys($tidArr)).')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$taxaArr[$r->tid][$r->rankid] = $r->sciname;
			}
			$rs->free();
			//Add taxonomic hierarchy to occurrence array
			foreach($tidArr as $tid => $occidArr){
				foreach($occidArr as $occid){
					if(isset($taxaArr[$tid][10])) $this->occurArr[$occid]['kingdom'] = $taxaArr[$tid][10];
					if(isset($taxaArr[$tid][30])) $this->occurArr[$occid]['phylum'] = $taxaArr[$tid][30];
					if(isset($taxaArr[$tid][60])) $this->occurArr[$occid]['class'] = $taxaArr[$tid][60];
					if(isset($taxaArr[$tid][100])) $this->occurArr[$occid]['order'] = $taxaArr[$tid][100];
				}
			}
		}
	}

	private function setVernacular(&$tidArr){
		if($tidArr){
			$vernArr = array();
			$sql = 'SELECT tid, vernacularName FROM taxavernaculars WHERE tid IN('.implode(',',array_keys($tidArr)).') AND langid = 1 ORDER BY TID, sortSequence';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$vernArr[$r->tid][] = $r->vernacularName;
			}
			$rs->free();
			//Add taxonomic hierarchy to occurrence array
			foreach($tidArr as $tid => $occidArr){
				foreach($occidArr as $occid){
					if(isset($vernArr[$tid])) $this->occurArr[$occid]['common'] = implode('; ',$vernArr[$tid]);
				}
			}
		}
	}

	private function setAdditionalIdentifiers(){
		$sql = 'SELECT occid, identifierName, identifierValue FROM omoccuridentifiers WHERE occid IN('.implode(',',array_keys($this->occurArr)).')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->occurArr[$r->occid]['otherNum'] .= '; '.($r->identifierName?$r->identifierName.': ':'').$r->identifierValue;
		}
		$rs->free();
	}

	private function exportData(){
		if($this->occurArr){
			$fileName = 'NPS_Report_'.$this->siteArr[$this->datasetID].'_'.date('Y-m-d').'.csv';
			$fieldArr = array('Catalog #', 'Accession #', 'Class 1', 'Kingdom', 'Phylum/Division', 'Class', 'Order', 'Family', 'Sci. Name:Genus', 'Sci. Name:Species',
				'Sci. Name:Species Authority', 'Common Name', 'TSN', 'Item Count', 'Quantity', 'Storage Unit', 'Description', 'Dimens/Weight', 'Collector','Collection #','Collection Date',
				'Maint. Cycle',	'Condition', 'Condition Desc', 'Study #', 'Other Numbers', 'Eminent Figure','Eminent Org', 'Cataloger', 'Catalog Date', 'Identified By', 'Ident Date',
				'Repro Method', 'Locality', 'Unit', 'TRS', 'County', 'State', 'Reference Datum', 'Watrbody/Drain:Waterbody', 'Watrbody/Drain:Drainage', 'UTM Z/E/N',
				'Lat LongN/W:Latitude Degree', 'Lat LongN/W:Latitude Minutes', 'Lat LongN/W:Latitude Seconds', 'Lat LongN/W:Longitude Degree', 'Lat LongN/W:Longitude Minutes', 'Lat LongN/W:Longitude Seconds',
				'Elevation', 'Depth', 'Depos Environ', 'Habitat/Comm', 'Habitat', 'Slope', 'Aspect', 'Soil Type', 'For/Per/Sub', 'Assoc Spec', 'Type Specimen', 'Threat/Endang', 'T/E Date',
				'Rare','Exotic/Native', 'Age', 'Sex', 'Ctrl Prop', 'Location', 'Object Status', 'Status Date', 'Catalog Folder');
			header ('Content-Type: text/csv');
			header ('Content-Disposition: attachment; filename="'.$fileName.'"');
			$outstream = fopen("php://output", "w");
			fputcsv($outstream,$fieldArr);
			foreach($this->occurArr as $occid => $occArr){
				fputcsv($outstream,$occArr);
			}
			fclose($outstream);
		}
	}

	//Setters and getters
	public function setDatasetID($id){
		if(is_numeric($id)) $this->datasetID = $id;
	}

	public function setTargetYear($y){
		if(is_numeric($y)) $this->targetYear = $y;
	}

	public function setDebugMode(){
		if($bool) $this->debugMode = true;
		else $this->debugMode = false;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>