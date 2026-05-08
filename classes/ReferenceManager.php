<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class ReferenceManager{

	private $conn;
	private $refid = 0;
	private $refAuthId = 0;

	public $errorMessage = '';
    public $warningArr = [];

	function __construct() {
		$this->conn = MySQLiConnectionFactory::getCon("write");
	}

	function __destruct(){
 		if($this->conn) $this->conn->close();
	}

	public function getRefList($keyword){
		$retArr = array();
		$sql = 'SELECT r.refid, r.bibliographicCitation '.
			'FROM referenceobject AS r ORDER BY r.bibliographicCitation';

			if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid]['refid'] = $r->refid;
				$retArr[$r->refid]['bibliographicCitation'] = $r->bibliographicCitation;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function createReference($pArr){
		global $SYMB_UID;
		$statusStr = '';

		$sql = 'INSERT INTO referenceobject (
			bibliographicCitation,
			identifier,
			title,
			creator,
			date,
			source,
			description,
			subject,
			language,
			rights,
			`type`,
			taxonRemarks,
			datasetID,
			url,
			modifiedByUid,
			modifiedtimestamp
		) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())';

		print_r($pArr);

		if($stmt = $this->conn->prepare($sql)){

			$stmt->bind_param(
				"ssssssssssssssi",
				$pArr['bibliographicCitation'],
				$pArr['identifier'],
				$pArr['title'],
				$pArr['creator'],
				$pArr['date'],
				$pArr['source'],
				$pArr['description'],
				$pArr['subject'],
				$pArr['language'],
				$pArr['rights'],
				$pArr['type'],
				$pArr['taxonRemarks'],
				$pArr['datasetID'],
				$pArr['url'],
				$SYMB_UID
			);

			if($stmt->execute()){
				$this->refid = $stmt->insert_id;
			}
			else{
				$statusStr = 'ERROR: Creation failed: '.$stmt->error;
			}

			$stmt->close();
		}
		else{
			$statusStr = 'ERROR: Prepare failed: '.$this->conn->error;
		}

		return $statusStr;
	}

	public function getRefArr($refid){
		$retArr = array();

		$sql = "SELECT o.refid, o.identifier, o.bibliographicCitation, o.title, o.creator,
				o.date, o.source, o.description, o.subject, o.language, o.rights, o.taxonRemarks,
				o.type, o.datasetID, o.url
				FROM referenceobject AS o
				WHERE o.refid = ?";

		$stmt = $this->conn->prepare($sql);

		if(!$stmt){
			return $retArr;
		}

		$stmt->bind_param("i", $refid);
		$stmt->execute();

		$result = $stmt->get_result();

		if($result && $row = $result->fetch_assoc()){
			$retArr = [
				'refid' => $row['refid'],
				'identifier' => $row['identifier'],
				'bibliographicCitation' => $row['bibliographicCitation'],
				'title' => $row['title'],
				'creator' => $row['creator'],
				'date' => $row['date'],
				'source' => $row['source'],
				'description' => $row['description'],
				'subject' => $row['subject'],
				'language' => $row['language'],
				'rights' => $row['rights'],
				'taxonRemarks' => $row['taxonRemarks'],
				'type' => $row['type'],
				'datasetID' => $row['datasetID'],
				'url' => $row['url']

			];
		}

		$stmt->close();

		return $retArr;
	}

	public function getRefChecklistArr($refid){
		$retArr = array();
		$sql = 'SELECT l.clid, a.Name '.
			'FROM referencechecklistlink AS l LEFT JOIN fmchecklists AS a ON l.clid = a.CLID '.
			'WHERE l.refid = '.$refid.' '.
			'ORDER BY a.Name';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->clid] = $r->Name;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getRefDatasetArr($refid){
		$retArr = array();
		$sql = 'SELECT l.datasetid, a.name '.
			'FROM referencedatasetlink AS l LEFT JOIN omoccurdatasets AS a ON l.datasetid = a.datasetID '.
			'WHERE l.refid = '.$refid.' '.
			'ORDER BY a.Name';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->datasetid] = $r->name;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getRefCollArr($refid){
		$retArr = array();
		$sql = 'SELECT l.collid, a.collectionName '.
			'FROM referencecollectionlink AS l LEFT JOIN omcollections AS a ON l.collid = a.collID '.
			'WHERE l.refid = ? '.
			'ORDER BY a.collectionName';

		$stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->errorMessage = "Database error: " . $this->conn->error;
            return $retArr;
        }
        $stmt->bind_param("i", $refid);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $retArr[] = $row['collectionName'];
        }

        $stmt->close();

        return $retArr;
	}

	public function getRefOccArr($refid){
		$retArr = array();
		$sql = 'SELECT c.collectionCode, a.catalogNumber, a.sciname, a.recordedBy, a.eventDate, l.occid
			FROM referenceoccurlink AS l LEFT JOIN omoccurrences AS a ON l.occid = a.occid LEFT JOIN omcollections AS c ON a.collid = c.collid
			WHERE l.refid = ?';

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->errorMessage = "Database error: " . $this->conn->error;
            return $retArr;
        }

        $stmt->bind_param("i", $refid);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $retArr[] = $row;
        }

        $stmt->close();

        return $retArr;
	}

	public function getRefTaxaArr($refid){
		$retArr = array();
		$sql = 'SELECT l.tid, a.SciName '.
			'FROM referencetaxalink AS l LEFT JOIN taxa AS a ON l.tid = a.TID '.
			'WHERE l.refid = ? '.
			'ORDER BY a.SciName';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->errorMessage = "Database error: " . $this->conn->error;
            return $retArr;
        }

        $stmt->bind_param("i", $refid);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
        	$retArr[] = $row['SciName'];
        }

        $stmt->close();

        return $retArr;
	}

	public function addRefLink($refid,$targetid,$type){
		$statusStr = '';

		$sql = '';

		switch($type){
			case 'checklist':
				$sql = 'INSERT IGNORE INTO referencechecklistlink(refid,clid) VALUES (?,?)';
				break;

			case 'collection':
				$sql = 'INSERT IGNORE INTO referencecollectionlink(refid,collid) VALUES (?,?)';
				break;

			case 'dataset':
				$sql = 'INSERT IGNORE INTO referencedatasetlink(refid,datasetid) VALUES (?,?)';
				break;

			case 'taxon':
				$sql = 'INSERT IGNORE INTO referencetaxalink(refid,tid) VALUES (?,?)';
				break;

			default:
				return 'ERROR: Invalid link type';
		}

		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $refid, $targetid);

			if($stmt->execute()){
				$statusStr = 'Success adding reference link';
			} else {
				$statusStr = 'ERROR: '.$stmt->error;
			}

			$stmt->close();
		}

		return $statusStr;
	}

	public function deleteReference($refid){
		$statusStr = '';
		$sql = 'DELETE FROM referenceauthorlink '.
				'WHERE (refid = '.$refid.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$sql = 'DELETE FROM referenceobject '.
					'WHERE (refid = '.$refid.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'Reference deleted.';
			}
		}
		else{
			$statusStr = 'ERROR: Deletion of reference failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}

	public function deleteRefLink($refid, $targetid, $type){

		$statusStr = '';

		$sql = '';

		switch($type){
			case 'checklist':
				$sql = 'DELETE FROM referencechecklistlink WHERE refid=? AND clid=?';
				break;

			case 'collection':
				$sql = 'DELETE FROM referencecollectionlink WHERE refid=? AND collid=?';
				break;

			case 'dataset':
				$sql = 'DELETE FROM referencedatasetlink WHERE refid=? AND datasetid=?';
				break;

			case 'taxon':
				$sql = 'DELETE FROM referencetaxalink WHERE refid=? AND tid=?';
				break;

			case 'occurrence':
				$sql = 'DELETE FROM referenceoccurlink WHERE refid=? AND occid=?';
				break;

			default:
				return 'ERROR: Invalid link type';
		}

		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $refid, $targetid);

			if($stmt->execute()){
				$statusStr = 'Success deleting reference link';
			} else {
				$statusStr = 'ERROR: '.$stmt->error;
			}

			$stmt->close();
		}

		return $statusStr;
	}

	public function editReference($pArr){
		global $SYMB_UID;
		$statusStr = '';

		$refid = isset($pArr['refid']) ? (int)$pArr['refid'] : 0;

		if(!$refid){
			return 'ERROR: Invalid reference ID';
		}

		$bibliographicCitation = $pArr['bibliographicCitation'] ?? '';
		$identifier = $pArr['identifier'] ?? '';
		$title = $pArr['title'] ?? '';
		$creator = $pArr['creator'] ?? '';
		$date = $pArr['date'] ?? '';
		$source = $pArr['source'] ?? '';
		$description = $pArr['description'] ?? '';
		$subject = $pArr['subject'] ?? '';
		$language = $pArr['language'] ?? '';
		$rights = $pArr['rights'] ?? '';
		$type = $pArr['type'] ?? '';
		$taxonRemarks = $pArr['taxonRemarks'] ?? '';
		$datasetID = $pArr['datasetID'] ?? '';
		$url = $pArr['url'] ?? '';

		$sql = 'UPDATE referenceobject SET
			bibliographicCitation = ?,
			identifier = ?,
			title = ?,
			creator = ?,
			date = ?,
			source = ?,
			description = ?,
			subject = ?,
			language = ?,
			rights = ?,
			`type` = ?,
			taxonRemarks = ?,
			datasetID = ?,
			url = ?,
			modifiedByUid = ?,
			modifiedtimestamp = NOW()
			WHERE refid = ?';

		if($stmt = $this->conn->prepare($sql)){

			$stmt->bind_param(
				"ssssssssssssssii",
				$bibliographicCitation,
				$identifier,
				$title,
				$creator,
				$date,
				$source,
				$description,
				$subject,
				$language,
				$rights,
				$type,
				$taxonRemarks,
				$datasetID,
				$url,
				$SYMB_UID,
				$refid
			);

			if($stmt->execute()){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing failed: '.$stmt->error;
			}

			$stmt->close();
		}
		else{
			$statusStr = 'ERROR: Prepare failed: '.$this->conn->error;
		}

		return $statusStr;
	}

	public function batchAddLink($postArr){
		$cnt = 0;
		if($postArr['catalogNumbers']){
			$catNumStr = str_replace(array("\n", "\r\n", ";"), ",", $postArr['catalogNumbers']);
			$catArr = array_unique(explode(',',$catNumStr));
			foreach($catArr as $catStr){
				$catStr = trim($catStr);

				if(!$catStr) continue;

				$occArr = $this->getOccid($catStr, $postArr['targetidentifier']);

				if($occArr && count($occArr)){
					if(count($occArr) > 1){
						$this->warningArr['multiple'][] = $catStr;
					}

					foreach($occArr as $occid){
						if($this->addOccurrence($postArr['refid'], $occid)){
							$cnt++;
						}
						else{
							if(strpos($this->errorMessage,'Duplicate entry') === 0){
								$this->warningArr['dupe'][] = $catStr;
							}
							else{
								$this->warningArr['error'][] = $this->errorMessage;
							}
						}
					}
				}
				else{
					$this->warningArr['missing'][] = $catStr;
				}
			}
		}
		return [
			'success' => $cnt,
			'missing' => $this->warningArr['missing'] ?? [],
			'duplicate' => $this->warningArr['dupe'] ?? [],
			'multiple' => $this->warningArr['multiple'] ?? [],
			'errors' => $this->warningArr['error'] ?? []
		];
	}

	public function linkOccurFromResource($refid, $targetid, $type){
		$statusStr = '';

		switch($type){
			case 'checklist':
				$sql = 'INSERT IGNORE INTO referenceoccurlink (refid, occid)
					SELECT ?, occid
					FROM fmvouchers
					WHERE clid = ?';
				break;

			case 'dataset':
				$sql = 'INSERT IGNORE INTO referenceoccurlink (refid, occid)
					SELECT ?, occid
					FROM omoccurdatasetlink
					WHERE datasetid = ?';
				break;

			default:
				return 'ERROR: Invalid link type';
		}

		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $refid, $targetid);

			if($stmt->execute()){
				$affected = $stmt->affected_rows;
				$statusStr = "Success: $affected occurrence links added";
			} else {
				$statusStr = 'ERROR: ' . $stmt->error;
			}

			$stmt->close();
		} else {
			$statusStr = 'ERROR: Failed to prepare statement';
		}

		return $statusStr;
	}

	private function getOccid($catNum, $method){
		$occArr = array();
		if(!$method || !in_array($method,array('allid','catnum','other'))) $method = 'allid';
		$sql = 'SELECT DISTINCT o.occid FROM omoccurrences o ';
		$sqlWhere = '';
		if($method == 'allid' || $method == 'other'){
			$sql .= 'LEFT JOIN omoccuridentifiers i ON o.occid = i.occid ';
			$catNum = $this->cleanInStr($catNum);
			$sqlWhere = 'OR (o.othercatalognumbers = "'.$catNum.'") OR (i.identifierValue = "'.$catNum.'") ';
		}
		if($method == 'allid' || $method == 'catnum') $sqlWhere .= 'OR (o.catalognumber = "'.$this->cleanInStr($catNum).'") ';
		if($sqlWhere){
			$sql .= 'WHERE ('.substr($sqlWhere,2).') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()) {
				$occArr[] = $r->occid;
			}
			$rs->free();
		}
		return $occArr;
	}

	private function addOccurrence($refid,$occid){
		$status = false;
		$sql = 'INSERT INTO referenceoccurlink(refid,occid) VALUES (?,?) ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $refid, $occid);
			try{
				if($stmt->execute()){
					$status = true;
				}
			} catch (mysqli_sql_exception $e){
				$this->errorMessage = $stmt->error;
			} catch (Exception $e){
				$this->errorMessage = 'unknown error';
			}
			$stmt->close();
		}
		return $status;
	}

	public function linkCollFromOccur($refid){

		$sql = 'INSERT IGNORE INTO referencecollectionlink (refid, collid)
			SELECT DISTINCT ?, o.collid
			FROM referenceoccurlink r
			JOIN omoccurrences o ON r.occid = o.occid
			WHERE r.refid = ?';

		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $refid, $refid);

			if($stmt->execute()){
				$affected = $stmt->affected_rows;
				$statusStr = "Success: $affected collection links added";
			} else {
				$statusStr = 'ERROR: ' . $stmt->error;
			}

			$stmt->close();
		} else {
			$statusStr = 'ERROR: Failed to prepare statement';
		}

		return $statusStr;
	}

		//Get and set functions
	public function getrefid(){
		return $this->refid;
	}

	private function cleanOutStr($str){
		$newStr = str_replace('"',"&quot;",$str);
		$newStr = str_replace("'","&apos;",$newStr);
		//$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}

	private function cleanTxtStr($str){
		if($str){
			$newStr = trim($str);
			$newStr = preg_replace('/\s\s+/', ' ',$newStr);
			$newStr = $this->conn->real_escape_string($newStr);
			$newStr = '"'.$newStr.'"';
		}
		else{
			$newStr = 'NULL';
		}
		return $newStr;
	}
}
?>