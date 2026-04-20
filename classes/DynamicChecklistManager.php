<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT.'/classes/Manager.php');

class DynamicChecklistManager extends Manager {

	public function __construct(){
		parent::__construct(null,'write');
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function createChecklist($lat, $lng, $taxon, $radius){
		$dynPk = 0;
		$radiusDisplay = 'RADIUS';
		if($radius) $radiusDisplay = round($radius, 1);
		$name =  round($lat,5) . ' ' . round($lng,5) . ' radius ' . $radiusDisplay . 'km';
		$detailStr = $name;
		$tidFilter = 0;
		if($taxon){
			if(is_numeric($taxon)){
				$tidFilter = $taxon;
				$detailStr = $this->getSciname($tidFilter) . ' ' . $name;
			}
			else{
				$tidFilter = $this->getTid($taxon);
				$detailStr = $taxon . ' ' . $name;
			}
		}
		$expiration = date('Y-m-d',mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')));
		$uid = ($GLOBALS['SYMB_UID'] ? $GLOBALS['SYMB_UID'] : null);
		$sql = 'INSERT INTO fmdynamicchecklists(name,details,expiration,uid) VALUES (?,?,?,?)';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('sssi', $name, $detailStr, $expiration, $uid);
			$stmt->execute();
			if($stmt->affected_rows || !$stmt->error){
				$dynPk = $stmt->insert_id;
				if($radius){
					$this->addTaxaChecklist($dynPk, $lat, $lng, $radius, $tidFilter);
				}
				else{
					$specCnt = 0;
					$radiusUnit = $GLOBALS['DYN_CHECKLIST_RADIUS'] ?? 10;
					$radius = $radiusUnit;
					$loopCnt = 1;
					while($specCnt < 2500 && $loopCnt < 10){
						$radius += $radiusUnit;
						$specCnt += $this->addTaxaChecklist($dynPk, $lat, $lng, $radius, $tidFilter);
						$loopCnt++;
					}
					$this->updateTitle($dynPk, $radius);
				}
			}
			$stmt->close();
		}
		return $dynPk;
	}

	private function addTaxaChecklist($dynPk, $lat, $lng, $radius, $tidFilter){
		$recordCnt = 0;
		if($lat && $lng){
			$latRadius = $radius / 111;
			$lat1 = $lat - $latRadius;
			$lat2 = $lat + $latRadius;
			$lngRadius = cos($lat / 57.3)*($radius / 111);
			$lng1 = $lng - $lngRadius;
			$lng2 = $lng + $lngRadius;

			$typeStr = 'dddd';
			$paramArr = array($lat1, $lat2, $lng1, $lng2);
			$sql = 'INSERT IGNORE INTO fmdyncltaxalink (dynclid, tid)
				SELECT DISTINCT ' . $dynPk . ' AS dynpk, IF(t.rankid=220,t.tid,ts2.parenttid) as tid
				FROM omoccurgeoindex o INNER JOIN taxstatus ts ON o.tid = ts.tid
				INNER JOIN taxstatus ts2 ON ts.tidaccepted = ts2.tid
				INNER JOIN taxa t ON ts2.tid = t.tid ';
			if($tidFilter){
				$sql .= 'INNER JOIN taxaenumtree e ON ts2.tid = e.tid ';
			}
			$sql .= 'WHERE (t.rankid >= 220) AND (ts.taxauthid = 1) AND (ts2.taxauthid = 1)
				AND (o.DecimalLatitude BETWEEN ? AND ?) AND (o.DecimalLongitude BETWEEN ? AND ?) ';
			if($tidFilter){
				$typeStr .= 'i';
				$paramArr[] = $tidFilter;
				$sql .= 'AND e.taxauthid = 1 AND e.parentTid = ?';
			}
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($typeStr, ...$paramArr);
				$stmt->execute();
				$recordCnt = $stmt->affected_rows;
				if(!$recordCnt && $stmt->error) $this->errorMessage = $stmt->error;
				$stmt->close();
			}
		}
		return $recordCnt;
	}

	private function getSciname($tid){
		$sciname = 0;
		$sql = 'SELECT sciname FROM taxa WHERE tid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $tid);
			$stmt->execute();
			$stmt->bind_result($sciname);
			$stmt->fetch();
			$stmt->close();
		}
		return $sciname;
	}

	private function updateTitle($dynPk, $radius){
		$sql = 'UPDATE fmdynamicchecklists SET name = REPLACE(name, "RADIUS", "' . $radius . '"), details = REPLACE(details, "RADIUS", "' . $radius . '") WHERE dynclid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $dynPk);
			$stmt->execute();
			$stmt->close();
		}
	}

	private function getTid($sciname){
		$tid = 0;
		$sql = 'SELECT tid FROM taxa WHERE sciname = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('s', $sciname);
			$stmt->execute();
			$stmt->bind_result($tid);
			$stmt->fetch();
			$stmt->close();
		}
		return $tid;
	}

	public function removeOldChecklists(){
		//Remove any old checklists
		$sql = 'DELETE dcl.*
			FROM fmdyncltaxalink dcl INNER JOIN fmdynamicchecklists dc ON dcl.dynclid = dc.dynclid
			WHERE dc.expiration < NOW()';
		$this->conn->query($sql);
		$this->conn->query('DELETE FROM fmdynamicchecklists WHERE expiration < NOW()');
	}
}
?>