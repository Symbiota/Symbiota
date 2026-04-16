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

	public function createChecklist($lat, $lng, $radius, $radiusUnits, $tidFilter){
		if($radiusUnits == "mi") $radius = round($radius*1.6);
		$dynPk = 0;
		$sciname = '';
		if($tidFilter) $sciname = $this->getSciname($tidFilter) . ' within ';
		$name = round($lat,5) . ' ' . round($lng,5) . ' &pm; ' . round($radius,1) . ' km';
		$detailStr = $sciname . $lat . ' ' . $lng . ' &pm; ' . $radius . ' km';
		$expiration = date('Y-m-d',mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')));
		$uid = ($GLOBALS['SYMB_UID'] ? $GLOBALS['SYMB_UID'] : null);
		$sql = 'INSERT INTO fmdynamicchecklists(name,details,expiration,uid) VALUES (?,?,?,?)';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('sssi', $name, $detailStr, $expiration, $uid);
			$stmt->execute();
			if($stmt->affected_rows || !$stmt->error){
				$dynPk = $stmt->insert_id;
				$this->addTaxaChecklist($dynPk, $lat, $lng, $radius, $radiusUnits, $tidFilter);
			}
			$stmt->close();
		}

		return $dynPk;
	}

	private function addTaxaChecklist($dynPk, $lat, $lng, $radius, $radiusUnits, $tidFilter){
		//Add species to checklist
		$latRadius = $radius / 111;
		$lngRadius = cos($lat / 57.3)*($radius / 111);
		$lat1 = $lat - $latRadius;
		$lat2 = $lat + $latRadius;
		$lng1 = $lng - $lngRadius;
		$lng2 = $lng + $lngRadius;

		//$sql = 'SELECT count(o.tid) AS speccnt FROM omoccurgeoindex o '.
		//	'WHERE (o.DecimalLatitude BETWEEN lat1 AND lat2) AND (o.DecimalLongitude BETWEEN lng1 AND lng2)';
		//$this->conn->query($sql);

		$sql = 'INSERT INTO fmdyncltaxalink (dynclid, tid) '.
			'SELECT DISTINCT '.$dynPk.' AS dynpk, IF(t.rankid=220,t.tid,ts2.parenttid) as tid '.
			'FROM omoccurgeoindex o INNER JOIN taxstatus ts ON o.tid = ts.tid '.
			'INNER JOIN taxstatus ts2 ON ts.tidaccepted = ts2.tid '.
			'INNER JOIN taxa t ON ts2.tid = t.tid ';
		if($tidFilter){
			$sql .= 'INNER JOIN taxaenumtree e ON ts2.tid = e.tid ';
		}
		$sql .= 'WHERE (t.rankid IN(220,230,240,260)) AND (ts.taxauthid = 1) AND (ts2.taxauthid = 1) '.
				'AND (o.DecimalLatitude BETWEEN '.$lat1.' AND '.$lat2.') AND (o.DecimalLongitude BETWEEN '.$lng1.' AND '.$lng2.') ';
		if($tidFilter){
			$sql .= 'and e.parentTid = '.$tidFilter;
		}
		$this->conn->query($sql);
	}

	public function createDynamicChecklist($lat, $lng, $radiusUnit, $tidFilter){
		$dynPK = 0;

		$specCnt = 0;
		$radius = 10;
		$lat1 = 0; $lat2 = 0; $lng1 = 0; $lng2 = 0;
		$loopCnt = 1;
		while($specCnt < 2500 && $loopCnt < 10){
			$radius = $radiusUnit*$loopCnt;
			$latRadius = $radius / 69.1;
			$lngRadius = cos($lat / 57.3)*($radius / 69.1);
			$lat1 = $lat - $latRadius;
			$lat2 = $lat + $latRadius;
			$lng1 = $lng - $lngRadius;
			$lng2 = $lng + $lngRadius;

			$sql1 = 'SELECT count(tid) AS speccnt '.
				'FROM omoccurgeoindex '.
				'WHERE (DecimalLatitude BETWEEN '.$lat1.' AND '.$lat2.') AND (DecimalLongitude BETWEEN '.$lng1.' AND '.$lng2.')';
			$rs1 = $this->conn->query($sql1);
			if($r1 = $rs1->fetch_object()){
				$specCnt = $r1->speccnt;
			}
			$rs1->free();
			$loopCnt++;
		}

		$radius = $radius*1.60934;
		$sql2 = 'INSERT INTO fmdynamicchecklists(name,details,expiration,uid) '.
			'VALUES ("'.round($lat,5).' '.round($lng,5).' within '.round($radius,1).' km","'.$lat.' '.$lng.' within '.$radius.' km","'.
			date('Y-m-d',mktime(0, 0, 0, date('m'), date('d') + 7, date('Y'))).'",'.($GLOBALS['SYMB_UID']?$GLOBALS['SYMB_UID']:'NULL').')';
		if($this->conn->query($sql2)){
			$dynPK = $this->conn->insert_id;
			$sql3 = 'INSERT INTO fmdyncltaxalink (dynclid, tid) '.
				'SELECT DISTINCT '.$dynPK.', IF(t.rankid=220,t.tid,ts2.parenttid) as tid '.
				'FROM omoccurgeoindex o INNER JOIN taxstatus ts ON o.tid = ts.tid '.
				'INNER JOIN taxstatus ts2 ON ts.tidaccepted = ts2.tid '.
				'INNER JOIN taxa t ON ts2.tid = t.tid ';
			if($tidFilter){
				$sql3 .= 'INNER JOIN taxaenumtree e ON ts2.tid = e.tid ';
			}
			$sql3 .= 'WHERE (t.rankid >= 220) AND (ts.taxauthid = 1) AND (ts2.taxauthid = 1) '.
				'AND (o.DecimalLatitude BETWEEN '.$lat1.' AND '.$lat2.') AND (o.DecimalLongitude BETWEEN '.$lng1.' AND '.$lng2.')';
			if($tidFilter){
				$sql3 .= 'and e.parentTid = '.$tidFilter;
			}
			if(!$this->conn->query($sql3)){
				$this->errorMessage = 'ERROR adding taxa to checklist: '.$this->conn->error;
				echo $this->errorMessage;
			}
		}
		else {
			$this->errorMessage = 'ERROR building checklist: '.$this->conn->connect_error;
			echo $this->errorMessage;
		}
		return $dynPK;
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

	public function getTid($sciname){
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
		$sql1 = 'DELETE dcl.* '.
			'FROM fmdyncltaxalink dcl INNER JOIN fmdynamicchecklists dc ON dcl.dynclid = dc.dynclid '.
			'WHERE dc.expiration < NOW()';
		$this->conn->query($sql1);
		$sql2 = 'DELETE FROM fmdynamicchecklists WHERE expiration < NOW()';
		$this->conn->query($sql2);
	}
}
?>