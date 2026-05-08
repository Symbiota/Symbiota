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

	public function areGeographicPolygonsReady($polygonIDs){
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
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE geoThesID = ?',
				[(int)$geoThesID]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function rebuildGeographicPolygonIndex($geoThesID){
		if(!is_numeric($geoThesID)) return false;
		$geoThesID = (int)$geoThesID;

		try {
			QueryUtil::executeQuery($this->conn,
				'UPDATE geographicpolygon '.
				'SET polygonIndexStatus = "building", polygonIndexedAt = NULL, polygonIndexRecordCount = NULL, polygonIndexError = NULL '.
				'WHERE geoThesID = ?',
				[$geoThesID]
			);

			$this->deleteGeographicPolygonIndex($geoThesID);

			QueryUtil::executeQuery($this->conn,
				'INSERT IGNORE INTO occurrencepolygonindex(geoThesID, occid, indexedAt) '.
				'SELECT gp.geoThesID, p.occid, NOW() '.
				'FROM geographicpolygon gp '.
				'INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID '.
				'INNER JOIN omoccurpoints p ON TRUE '.
				'WHERE gp.geoThesID = ? '.
				'AND gth.isSearchable = 1 '.
				'AND MBRContains(gp.footprintPolygon, p.lngLatPoint) '.
				'AND ST_Contains(gp.footprintPolygon, p.lngLatPoint)',
				[$geoThesID]
			);

			//example for quicker count:
			$count = $this->getGeographicPolygonIndexCount($geoThesID);

			QueryUtil::executeQuery($this->conn,
				'UPDATE geographicpolygon '.
				'SET polygonIndexStatus = "ready", polygonIndexedAt = NOW(), polygonIndexRecordCount = ?, polygonIndexError = NULL '.
				'WHERE geoThesID = ?',
				[$count, $geoThesID]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			$error = substr($this->errorMessage, 0, 255);
			try {
				QueryUtil::executeQuery($this->conn,
					'UPDATE geographicpolygon SET polygonIndexStatus = "failed", polygonIndexError = ? WHERE geoThesID = ?',
					[$error, $geoThesID]
				);
			}
			catch(Throwable $ignored){
			}
			return false;
		}
	}

	public function rebuildPendingGeographicPolygonIndexes($limit = 10){
		$limit = is_numeric($limit) ? max(1, (int)$limit) : 10;
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
				if($this->rebuildGeographicPolygonIndex($geoThesID)) $rebuiltCnt++;
			}
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
		}

		return $rebuiltCnt;
	}

	public function refreshOccurrence($occid){
		if(!is_numeric($occid)) return false;
		$occid = (int)$occid;

		try {
			QueryUtil::executeQuery($this->conn,
				'DELETE FROM occurrencepolygonindex WHERE occid = ?',
				[$occid]
			);

			QueryUtil::executeQuery($this->conn,
				'INSERT IGNORE INTO occurrencepolygonindex(geoThesID, occid, indexedAt) '.
				'SELECT gp.geoThesID, p.occid, NOW() '.
				'FROM omoccurpoints p '.
				'INNER JOIN geographicpolygon gp ON TRUE '.
				'INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID '.
				'WHERE p.occid = ? '.
				'AND gth.isSearchable = 1 '.
				'AND gp.polygonIndexStatus = "ready" '.
				'AND MBRContains(gp.footprintPolygon, p.lngLatPoint) '.
				'AND ST_Contains(gp.footprintPolygon, p.lngLatPoint)',
				[$occid]
			);
			return true;
		}
		catch(Throwable $e){
			$this->errorMessage = $e->getMessage();
			return false;
		}
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
}
?>
