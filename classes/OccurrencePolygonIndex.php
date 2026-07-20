<?php
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');

class OccurrencePolygonIndex {

	private $conn;
	private $errorMessage = '';

	public function __construct(mysqli $conn){
		$this->conn = $conn;
	}

	public function getErrorMessage(): string{
		return $this->errorMessage;
	}

	public function isPolygonReady(array $polygonIDs): bool{
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

	public function markGeographicPolygonPending(int $geoThesID): bool{
		try {
			QueryUtil::executeQuery($this->conn,
				'UPDATE geographicpolygon '.
				'SET polygonIndexStatus = "pending", polygonIndexedAt = NULL, polygonIndexRecordCount = NULL, polygonIndexError = NULL '.
				'WHERE geoThesID = ?',
				[$geoThesID]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function deleteGeographicPolygonIndex(int $geoThesID): bool{
		try {
			$this->deleteGeographicPolygonIndexInBatches($geoThesID);
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

	public function rebuildPendingPolygons(int $limit = 20, int $batchSize = 10000): int{
		if($limit < 1) $limit = 20;
		if($batchSize < 1) $batchSize = 10000;
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

	private function deleteGeographicPolygonIndexInBatches(int $geoThesID, int $batchSize = 10000): void{
		do {
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE geoThesID = ? LIMIT '.$batchSize,
				[$geoThesID]
			);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function deleteStalePolygons(int $geoThesID, string $timeStamp, int $batchSize = 10000): void{
		do {
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE geoThesID = ? AND indexedAt <> ? LIMIT '.$batchSize,
				[$geoThesID, $timeStamp]
			);
			$deletedCnt = max(0, (int)$this->conn->affected_rows);
		} while($deletedCnt === $batchSize);
	}

	private function getNextOccurrencePointBatch(int $lastOccid, int $batchSize): ?array{
		$rs = QueryUtil::executeQuery($this->conn,
			'SELECT MIN(occid) AS startOccid, MAX(occid) AS endOccid, COUNT(*) AS pointCnt '.
			'FROM ('.
			'SELECT occid FROM omoccurpoints WHERE occid > ? ORDER BY occid LIMIT '.$batchSize.
			') pointBatch',
			[$lastOccid]
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

	private function getGeographicPolygonIndexCount(int $geoThesID): int{
		$rs = QueryUtil::executeQuery($this->conn,
			'SELECT COUNT(*) AS cnt FROM occurrencepolygonindex WHERE geoThesID = ?',
			[$geoThesID]
		);
		$row = $rs ? $rs->fetch_object() : null;
		if($rs) $rs->free();
		return ($row ? (int)$row->cnt : 0);
	}

	private function insertGeographicPolygonIndexBatch(int $geoThesID, int $startOccid, int $endOccid, string $timeStamp): int{
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
			[$timeStamp, $startOccid, $endOccid, $geoThesID]
		);
		return max(0, (int)$this->conn->affected_rows);
	}
}
?>
