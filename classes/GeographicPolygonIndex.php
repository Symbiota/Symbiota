<?php
include_once($SERVER_ROOT . '/classes/Manager.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');

class GeographicPolygonIndex extends Manager{

	public function __construct(mysqli $conn){
		parent::__construct(null, 'write', $conn);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function isPolygonReady(array $polygonIDs): bool{
		if(!$polygonIDs) return false;

		$sql = 'SELECT COUNT(*) AS readyCnt
			FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
			WHERE gp.geoThesID IN('.implode(',', $polygonIDs).') AND gth.isSearchable = 1 AND gp.indexStatus = "ready" ';

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

	public function markPolygonPending(int $geoThesID): bool{
		try {
			$sql = 'UPDATE geographicpolygon SET indexStatus = "pending", indexedAt = NULL, indexRecordCount = NULL, indexError = NULL WHERE geoThesID = ?';

			QueryUtil::executeQuery($this->conn, $sql, [$geoThesID] );
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function deletePolygonIndex(int $geoThesID): bool{
		try {
			$this->deletePolygonIndexInBatches($geoThesID);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function rebuildPolygons(int $geoThesID, int $batchSize = 10000): bool{
		if($batchSize < 1) $batchSize = 10000;
		$stage = 'building polygon index';
		$timeStamp = date('Y-m-d H:i:s');

		try {
			$lastOccid = 0;
			while($pointBatch = $this->getNextOccurrencePointBatch($lastOccid, $batchSize)){
				$stage = 'building polygon index rows for occurrence points: '.$pointBatch['startOccid'].'-'.$pointBatch['endOccid'];
				$this->insertPolygonIndexBatch($geoThesID, $pointBatch['startOccid'], $pointBatch['endOccid'], $timeStamp);
				$lastOccid = $pointBatch['endOccid'];
			}

			$stage = 'removing stale polygon index rows';
			$this->deleteStalePolygons($geoThesID, $timeStamp, $batchSize);
			$indexedCnt = $this->getPolygonIndexCount($geoThesID);

			$stage = 'marking polygon index as ready';
			$sql = 'UPDATE geographicpolygon SET indexStatus = "ready", indexedAt = NOW(), indexRecordCount = ?, indexError = NULL WHERE geoThesID = ?';
			QueryUtil::executeQuery($this->conn, $sql, [$indexedCnt, $geoThesID] );
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $stage.': '.$e->getMessage();
			if($stage !== 'marking polygon index as ready'){
				$error = substr($this->errorMessage, 0, 255);
				try {
					$sql = 'UPDATE geographicpolygon SET indexStatus = "failed", indexError = ? WHERE geoThesID = ?';
					QueryUtil::executeQuery($this->conn, $sql, [$error, $geoThesID]);
				}
				catch(Throwable $ignored){
				}
			}
			return false;
		}
	}

	public function rebuildPendingPolygons(int $limit = 20, int $batchSize = 10000): int{
		if($limit < 1) $limit = 20;
		if($batchSize < 1) $batchSize = 10000;
		$rebuiltCnt = 0;

		$sql = 'SELECT gp.geoThesID
			FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
			WHERE gth.isSearchable = 1 AND gp.indexStatus IN("pending","failed")
			ORDER BY gp.initialTimestamp
			LIMIT ' . $limit;

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

	private function deletePolygonIndexInBatches(int $geoThesID, int $batchSize = 10000): void{
		do {
			$sql = 'DELETE FROM geographicpolygonindex WHERE geoThesID = ? LIMIT ' . $batchSize;
			QueryUtil::executeQuery($this->conn, $sql, [$geoThesID]);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function deleteStalePolygons(int $geoThesID, string $timeStamp, int $batchSize = 10000): void{
		do {
			$sql = 'DELETE FROM geographicpolygonindex WHERE geoThesID = ? AND initialTimestamp <> ? LIMIT ' . $batchSize;
			QueryUtil::executeQuery($this->conn, $sql, [$geoThesID, $timeStamp]);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function getNextOccurrencePointBatch(int $lastOccid, int $batchSize): ?array{
		$sql = 'SELECT MIN(occid) AS startOccid, MAX(occid) AS endOccid, COUNT(*) AS pointCnt
			FROM (SELECT occid FROM omoccurpoints WHERE occid > ? ORDER BY occid LIMIT ' . $batchSize . ') pointBatch';
		$rs = QueryUtil::executeQuery($this->conn, $sql, [$lastOccid]);
		$row = $rs ? $rs->fetch_object() : null;
		if($rs) $rs->free();
		if(!$row || !(int)$row->pointCnt) return null;
		return array(
			'startOccid' => (int)$row->startOccid,
			'endOccid' => (int)$row->endOccid,
			'pointCnt' => (int)$row->pointCnt
		);
	}

	private function getPolygonIndexCount(int $geoThesID): int{
		$sql = 'SELECT COUNT(*) AS cnt FROM geographicpolygonindex WHERE geoThesID = ?';
		$rs = QueryUtil::executeQuery($this->conn, $sql, [$geoThesID]);
		$row = $rs ? $rs->fetch_object() : null;
		if($rs) $rs->free();
		return ($row ? (int)$row->cnt : 0);
	}

	private function insertPolygonIndexBatch(int $geoThesID, int $startOccid, int $endOccid, string $timeStamp): int{
		$sql = 'INSERT INTO geographicpolygonindex(geoThesID, occid, initialTimestamp)
			SELECT gp.geoThesID, p.occid, ?
			FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
			INNER JOIN omoccurpoints p ON p.occid BETWEEN ? AND ?
			WHERE gp.geoThesID = ? AND gth.isSearchable = 1
			AND MBRContains(gp.footprintPolygon, p.lngLatPoint)
			AND ST_Contains(gp.footprintPolygon, p.lngLatPoint)
			ON DUPLICATE KEY UPDATE initialTimestamp = VALUES(initialTimestamp)';
		QueryUtil::executeQuery($this->conn, $sql, [$timeStamp, $startOccid, $endOccid, $geoThesID]);
		return max(0, (int)$this->conn->affected_rows);
	}
}
?>
