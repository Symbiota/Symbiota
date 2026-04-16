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

	public function getChecklists(){
		$retArr = array();
		$sql = 'SELECT a.clid, a.name '.
			'FROM fmchecklists AS a '.
			'ORDER BY name';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->clid]['name'] = $r->name;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getDatasets(){
		$retArr = array();
		$sql = 'SELECT a.datasetID, a.name '.
			'FROM omoccurdatasets AS a '.
			'ORDER BY name';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->datasetID]['name'] = $r->name;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getCollections(){
		$retArr = array();
		$sql = 'SELECT a.collID, a.collectionName '.
			'FROM omcollections AS a '.
			'ORDER BY collectionName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->collID]['collectionName'] = $r->collectionName;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getRefTypeArr(){
		$retArr = array();
		$sql = 'SELECT ReferenceTypeId, ReferenceType '.
			'FROM referencetype '.
			'ORDER BY ReferenceType';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->ReferenceTypeId] = $r->ReferenceType;
			}
		}
		return $retArr;
	}

	public function createReference($pArr){
		global $SYMB_UID;
		$statusStr = '';
		$sql = 'INSERT INTO referenceobject(title,ReferenceTypeId,ispublished,modifieduid,modifiedtimestamp) '.
			'VALUES("'.$this->cleanInStr($pArr['newreftitle']).'","'.$this->cleanInStr($pArr['newreftype']).'","'.$this->cleanInStr($pArr['ispublished']).'",'.$SYMB_UID.',now()) ';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->refid = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new reference failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}

	public function getRefArr($refid){
		$retArr = array();

		$sql = "SELECT o.refid, o.identifier, o.bibliographicCitation, o.title, o.creator,
				o.date, o.source, o.description, o.subject, o.language, o.rights, o.taxonRemarks,
				o.type, o.datasetID
				FROM referenceobject AS o
				WHERE o.refid = ?";

		$stmt = $this->conn->prepare($sql);

		if(!$stmt){
			return $retArr; // or log error
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
				'datasetID' => $row['datasetID']
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
			'WHERE l.refid = '.$refid.' '.
			'ORDER BY a.collectionName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->collid] = $r->collectionName;
			}
			$rs->close();
		}
		return $retArr;
	}

	public function getRefOccArr($refid){
		$retArr = array();
		$sql = 'SELECT c.collectionCode, a.catalogNumber, a.sciname, a.recordedBy, a.eventDate, l.occid
			FROM referenceoccurlink AS l LEFT JOIN omoccurrences AS a ON l.occid = a.occid LEFT JOIN omcollections AS c ON a.collid = c.collid
			WHERE l.refid = ?';

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->errorMessage = "Dababase error: " . $this->conn->error;
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
			'WHERE l.refid = '.$refid.' '.
			'ORDER BY a.SciName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->tid] = $r->SciName;
			}
			$rs->close();
		}
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
		$refid = $pArr['refid'];
		unset($pArr['parentrefid2']);
		unset($pArr['refGroup']);
		$pArr = $this->formatInArr($pArr);
		if(is_numeric($refid)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k != 'formsubmit' && $k != 'refid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE referenceobject SET '.substr($sql,1).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() WHERE (refid = '.$refid.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of reference failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}


	public function formatInArr($pArr){
		if(!array_key_exists('secondarytitle',$pArr)){
			$pArr['secondarytitle'] = '';
		}
		if(!array_key_exists('shorttitle',$pArr)){
			$pArr['shorttitle'] = '';
		}
		if(!array_key_exists('tertiarytitle',$pArr)){
			$pArr['tertiarytitle'] = '';
		}
		if(!array_key_exists('alternativetitle',$pArr)){
			$pArr['alternativetitle'] = '';
		}
		if(!array_key_exists('typework',$pArr)){
			$pArr['typework'] = '';
		}
		if(!array_key_exists('pubdate',$pArr)){
			$pArr['pubdate'] = '';
		}
		if(!array_key_exists('figures',$pArr)){
			$pArr['figures'] = '';
		}
		if(!array_key_exists('edition',$pArr)){
			$pArr['edition'] = '';
		}
		if(!array_key_exists('volume',$pArr)){
			$pArr['volume'] = '';
		}
		if(!array_key_exists('numbervolumnes',$pArr)){
			$pArr['numbervolumnes'] = '';
		}
		if(!array_key_exists('number',$pArr)){
			$pArr['number'] = '';
		}
		if(!array_key_exists('pages',$pArr)){
			$pArr['pages'] = '';
		}
		if(!array_key_exists('section',$pArr)){
			$pArr['section'] = '';
		}
		if(!array_key_exists('placeofpublication',$pArr)){
			$pArr['placeofpublication'] = '';
		}
		if(!array_key_exists('publisher',$pArr)){
			$pArr['publisher'] = '';
		}
		if(!array_key_exists('isbn_issn',$pArr)){
			$pArr['isbn_issn'] = '';
		}
		return $pArr;
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
}
?>