<?php
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');

class OccurrencePolygonIndex {

	private $conn;
	private $errorMessage = '';

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function getErrorMessage(){
		return $this->errorMessage;
	}
	public static function sanitizePolygonIds($polygonIDs){
		if(!is_array($polygonIDs)) $polygonIDs = [$polygonIDs];
		$idArr = array();
		foreach($polygonIDs as $id){
			if(is_numeric($id)) $idArr[] = (int)$id;
		}
		return array_values(array_unique(array_filter($idArr)));
	}

	public function isPolygonReady($polygonIDs){
		$polygonIDs = self::sanitizePolygonIds($polygonIDs);
		if(!$polygonIDs) return false;

		$sql = 'SELECT COUNT(*) AS readyCnt '.
			'FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID '.
			'WHERE gp.geoThesID IN('.implode(',', $polygonIDs).') '.
			'AND gth.isSearchable = 1 '.
			'AND gp.polygonIndexStatus = "ready" ';

		try {
			$rs = QueryUtil::executeQuery($this->conn, $sql);
			$row = $rs ? $rs->fetch_object() : null;
			if($rs) $rs->free();
			return ($row && (int)$row->readyCnt === count($polygonIDs));
		}
		catch(Throwable $e){
			return false;
		}
	}

	public function markGeographicPolygonPending($geoThesID){
		if(!is_numeric($geoThesID)) return false;
		try {
			QueryUtil::executeQuery($this->conn,
				'UPDATE geographicpolygon '.
				'SET polygonIndexStatus = "pending", polygonIndexedAt = NULL, polygonIndexRecordCount = NULL, polygonIndexError = NULL '.
				'WHERE geoThesID = ?',
				[(int)$geoThesID]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function deleteGeographicPolygonIndex($geoThesID){
		if(!is_numeric($geoThesID)) return false;
		try {
			$this->deleteGeographicPolygonIndexInBatches((int)$geoThesID);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function rebuildPolygons($geoThesID, $batchSize = 50000){
		if(!is_numeric($geoThesID)) return false;
		$geoThesID = (int)$geoThesID;
		$batchSize = (int)$batchSize;
		if($batchSize < 1) $batchSize = 50000;
		$stage = 'building polygon index';
		$timeStamp = date('Y-m-d H:i:s');

		try {
			$lastOccid = 0;
			while($pointBatch = $this->getNextOccurrencePointBatch($lastOccid, $batchSize)){
				$stage = 'building polygon index rows for occurrence points: '.$pointBatch['startOccid'].'-'.$pointBatch['endOccid'];
				$this->insertGeographicPolygonIndexBatch($geoThesID, $pointBatch['startOccid'], $pointBatch['endOccid'], $timeStamp);
				$lastOccid = $pointBatch['endOccid'];
			}

			$stage = 'removing stale polygon index rows';
			$this->deleteStalePolygons($geoThesID, $timeStamp, $batchSize);
			$indexedCnt = $this->getGeographicPolygonIndexCount($geoThesID);

			$stage = 'marking polygon index as ready';
			QueryUtil::executeQuery($this->conn,
				'UPDATE geographicpolygon '.
				'SET polygonIndexStatus = "ready", polygonIndexedAt = NOW(), polygonIndexRecordCount = ?, polygonIndexError = NULL '.
				'WHERE geoThesID = ?',
				[$indexedCnt, $geoThesID]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $stage.': '.$e->getMessage();
			if($stage !== 'marking polygon index as ready'){
				$error = substr($this->errorMessage, 0, 255);
				try {
					QueryUtil::executeQuery($this->conn,
						'UPDATE geographicpolygon SET polygonIndexStatus = "failed", polygonIndexError = ? WHERE geoThesID = ?',
						[$error, $geoThesID]
					);
				}
				catch(Throwable $ignored){
				}
			}
			return false;
		}
	}
	//current default limit is 20
	public function rebuildPendingPolygons($limit = 20, $batchSize = 50000){
		$limit = (int)$limit;
		if($limit < 1) $limit = 20;
		$batchSize = (int)$batchSize;
		if($batchSize < 1) $batchSize = 50000;
		$rebuiltCnt = 0;

		$sql = 'SELECT gp.geoThesID '.
			'FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID '.
			'WHERE gth.isSearchable = 1 '.
			'AND gp.polygonIndexStatus IN("pending","failed") '.
			'ORDER BY gp.initialTimestamp '.
			'LIMIT '.$limit;

		try {
			$rs = QueryUtil::executeQuery($this->conn, $sql);
			$geoThesIDs = array();
			while($rs && $row = $rs->fetch_object()){
				$geoThesIDs[] = (int)$row->geoThesID;
			}
			if($rs) $rs->free();

			foreach($geoThesIDs as $geoThesID){
				if($this->rebuildPolygons($geoThesID, $batchSize)) $rebuiltCnt++;
			}
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
		}

		return $rebuiltCnt;
	}

	private function deleteGeographicPolygonIndexInBatches($geoThesID, $batchSize = 50000){
		do {
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE geoThesID = ? LIMIT '.$batchSize,
				[(int)$geoThesID]
			);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function deleteStalePolygons($geoThesID, $timeStamp, $batchSize = 50000){
		do {
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE geoThesID = ? AND indexedAt <> ? LIMIT '.$batchSize,
				[(int)$geoThesID, $timeStamp]
			);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function getNextOccurrencePointBatch($lastOccid, $batchSize){
		$rs = QueryUtil::executeQuery($this->conn,
			'SELECT MIN(occid) AS startOccid, MAX(occid) AS endOccid, COUNT(*) AS pointCnt '.
			'FROM ('.
			'SELECT occid FROM omoccurpoints WHERE occid > ? ORDER BY occid LIMIT '.$batchSize.
			') pointBatch',
			[(int)$lastOccid]
		);
		$row = $rs ? $rs->fetch_object() : null;
		if($rs) $rs->free();
		if(!$row || !(int)$row->pointCnt) return null;
		return array(
			'startOccid' => (int)$row->startOccid,
			'endOccid' => (int)$row->endOccid,
			'pointCnt' => (int)$row->pointCnt
		);
	}

	private function getGeographicPolygonIndexCount($geoThesID){
		$rs = QueryUtil::executeQuery($this->conn,
			'SELECT COUNT(*) AS cnt FROM occurrencepolygonindex WHERE geoThesID = ?',
			[(int)$geoThesID]
		);
		$row = $rs ? $rs->fetch_object() : null;
		if($rs) $rs->free();
		return ($row ? (int)$row->cnt : 0);
	}

	private function insertGeographicPolygonIndexBatch($geoThesID, $startOccid, $endOccid, $timeStamp){
		QueryUtil::executeQuery($this->conn,
			'INSERT INTO occurrencepolygonindex(geoThesID, occid, indexedAt) '.
			'SELECT gp.geoThesID, p.occid, ? '.
			'FROM geographicpolygon gp '.
			'INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID '.
			'INNER JOIN omoccurpoints p ON p.occid BETWEEN ? AND ? '.
			'WHERE gp.geoThesID = ? '.
			'AND gth.isSearchable = 1 '.
			'AND MBRContains(gp.footprintPolygon, p.lngLatPoint) '.
			'AND ST_Contains(gp.footprintPolygon, p.lngLatPoint) '.
			'ON DUPLICATE KEY UPDATE indexedAt = VALUES(indexedAt)',
			[$timeStamp, (int)$startOccid, (int)$endOccid, (int)$geoThesID]
		);
		return max(0, (int)$this->conn->affected_rows);
	}
}
?>
