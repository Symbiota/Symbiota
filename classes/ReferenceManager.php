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

	public function getRefList($keyword,$author){
		$retArr = array();
		$sql = 'SELECT r.refid, r.ReferenceTypeId, r.title, r.secondarytitle, r.tertiarytitle, r.number, r.pubdate, r.edition, r.volume, '.
			'GROUP_CONCAT(CONCAT(a.lastname,", ",CONCAT_WS("",LEFT(a.firstname,1),LEFT(a.middlename,1))) SEPARATOR ", ") AS authline '.
			'FROM referenceobject AS r LEFT JOIN referenceauthorlink AS l ON r.refid = l.refid '.
			'LEFT JOIN referenceauthors AS a ON l.refauthid = a.refauthorid ';
		if($keyword || $author){
			if($keyword && !$author){
				$sql .= 'WHERE r.title LIKE "%'.$keyword.'%" ';
			}
			if(!$keyword && $author){
				$sql .= 'WHERE a.lastname LIKE "%'.$author.'%" ';
			}
			if($keyword && $author){
				$sql .= 'WHERE r.title LIKE "%'.$keyword.'%" AND a.lastname LIKE "%'.$author.'%" ';
			}
		}
		$sql .= 'GROUP BY r.refid ';
		$sql .= 'ORDER BY r.title';
		//echo '<div>'.$sql.'</div>';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid]['refid'] = $r->refid;
				$retArr[$r->refid]['ReferenceTypeId'] = $r->ReferenceTypeId;
				$retArr[$r->refid]['title'] = $r->title;
				$retArr[$r->refid]['secondarytitle'] = $r->secondarytitle;
				$retArr[$r->refid]['tertiarytitle'] = $r->tertiarytitle;
				$retArr[$r->refid]['number'] = $r->number;
				$retArr[$r->refid]['pubdate'] = $r->pubdate;
				$retArr[$r->refid]['edition'] = $r->edition;
				$retArr[$r->refid]['volume'] = $r->volume;
				$retArr[$r->refid]['authline'] = $r->authline;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getAuthList(){
		$retArr = array();
		$sql = 'SELECT a.refauthorid, CONCAT_WS(", ",a.lastname,CONCAT_WS(" ",a.firstname,a.middlename)) AS authorName '.
			'FROM referenceauthors AS a '.
			'ORDER BY authorName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refauthorid]['authorName'] = $r->authorName;
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
	
	public function getAuthInfo($authId){
		$retArr = array();
		$sql = 'SELECT a.refauthorid, a.firstname, a.middlename, a.lastname '.
			'FROM referenceauthors AS a '.
			'WHERE a.refauthorid = '.$authId.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['refauthorid'] = $r->refauthorid;
				$retArr['firstname'] = $r->firstname;
				$retArr['middlename'] = $r->middlename;
				$retArr['lastname'] = $r->lastname;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getAuthPubList($authId){
		$retArr = array();
		$sql = 'SELECT a.refid, a.title, a.secondarytitle, a.shorttitle, a.pubdate '.
			'FROM referenceauthorlink AS l LEFT JOIN referenceobject AS a ON l.refid = a.refid '.
			'WHERE l.refauthid = '.$authId.' '.
			'ORDER BY a.title';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid]['refid'] = $r->refid;
				$retArr[$r->refid]['title'] = $r->title;
				$retArr[$r->refid]['secondarytitle'] = $r->secondarytitle;
				$retArr[$r->refid]['shorttitle'] = $r->shorttitle;
				$retArr[$r->refid]['pubdate'] = $r->pubdate;
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
		$sql = 'SELECT o.refid, o.parentrefid, o.title, o.secondarytitle, o.shorttitle, o.tertiarytitle, o.alternativetitle, o.typework, o.figures, '. 
			'o.pubdate, o.edition, o.volume, o.numbervolumnes, o.number, o.pages, o.section, o.placeofpublication, o.publisher, o.isbn_issn, o.url, '.
			'o.guid, o.ispublished, o.notes, t.ReferenceType, t.ReferenceTypeId '.
			'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
			'WHERE o.refid = '.$refid;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['refid'] = $r->refid;
				if($r->ReferenceTypeId == 3 || $r->ReferenceTypeId == 6){
					$retArr['parentrefid'] = '';
					$retArr['parentrefid2'] = $r->parentrefid;
				}
				else{
					$retArr['parentrefid'] = $r->parentrefid;
					$retArr['parentrefid2'] = '';
				}
				$retArr['title'] = $r->title;
				$retArr['secondarytitle'] = $r->secondarytitle;
				$retArr['shorttitle'] = $r->shorttitle;
				$retArr['tertiarytitle'] = $r->tertiarytitle;
				$retArr['alternativetitle'] = $r->alternativetitle;
				$retArr['typework'] = $r->typework;
				$retArr['figures'] = $r->figures;
				$retArr['pubdate'] = $r->pubdate;
				$retArr['edition'] = $r->edition;
				$retArr['volume'] = $r->volume;
				$retArr['numbervolumnes'] = $r->numbervolumnes;
				$retArr['number'] = $r->number;
				$retArr['pages'] = $r->pages;
				$retArr['section'] = $r->section;
				$retArr['placeofpublication'] = $r->placeofpublication;
				$retArr['publisher'] = $r->publisher;
				$retArr['isbn_issn'] = $r->isbn_issn;
				$retArr['url'] = $r->url;
				$retArr['guid'] = $r->guid;
				$retArr['ispublished'] = $r->ispublished;
				$retArr['notes'] = $r->notes;
				$retArr['ReferenceType'] = $r->ReferenceType;
				$retArr['ReferenceTypeId'] = $r->ReferenceTypeId;
			}
			$rs->close();
		}
		if($retArr['parentrefid']){
			$sql = 'SELECT o.parentrefid, o.title, o.shorttitle, o.alternativetitle, '. 
				'o.pubdate, o.edition, o.volume, o.number, o.placeofpublication, o.publisher, o.isbn_issn '.
				'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
				'WHERE o.refid = '.$retArr['parentrefid'];
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['parentrefid2'] = $r->parentrefid;
					$retArr['secondarytitle'] = $r->title;
					$retArr['alternativetitle'] = $r->alternativetitle;
					$retArr['shorttitle'] = $r->shorttitle;
					$retArr['pubdate'] = $r->pubdate;
					$retArr['edition'] = $r->edition;
					$retArr['volume'] = $r->volume;
					$retArr['number'] = $r->number;
					$retArr['placeofpublication'] = $r->placeofpublication;
					$retArr['publisher'] = $r->publisher;
					$retArr['isbn_issn'] = $r->isbn_issn;
				}
				$rs->close();
			}
		}
		if($retArr['parentrefid2']){
			$sql = 'SELECT o.title, o.edition, o.numbervolumnes, o.placeofpublication, o.publisher '. 
				'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId '.
				'WHERE o.refid = '.$retArr['parentrefid2'];
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['tertiarytitle'] = $r->title;
					$retArr['numbervolumnes'] = $r->numbervolumnes;
					$retArr['edition'] = $r->edition;
					$retArr['placeofpublication'] = $r->placeofpublication;
					$retArr['publisher'] = $r->publisher;
				}
				$rs->close();
			}
		}
		return $retArr;
	}
	
	public function getChildArr($refid){
		$retArr = array();
		$sql = 'SELECT o.refid '.
			'FROM referenceobject AS o '.
			'WHERE o.parentrefid = '.$refid;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refid] = $r->refid;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getRefAuthArr($refid){
		$retArr = array();
		$sql = 'SELECT a.refauthorid, CONCAT_WS(" ",a.firstname,a.middlename,a.lastname) AS authorName '.
			'FROM referenceauthorlink AS l LEFT JOIN referenceauthors AS a ON l.refauthid = a.refauthorid '.
			'WHERE l.refid = '.$refid.' '.
			'ORDER BY authorName';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->refauthorid] = $r->authorName;
			}
			$rs->close();
		}
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
	
	public function addAuthor($refid,$refAuthId){
		$statusStr = '';
		$sql = 'INSERT INTO referenceauthorlink(refid,refauthid) '.
			'VALUES('.$refid.','.$refAuthId.') ';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Success!';
		}
		else{
			$statusStr = 'ERROR: Creation of new reference author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
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
	
	public function deleteRefAuthor($refid,$refAuthId){
		$statusStr = '';
		$sql = 'DELETE FROM referenceauthorlink '.
				'WHERE (refid = '.$refid.') AND (refauthid = '.$refAuthId.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Reference author deleted.';
		}
		else{
			$statusStr = 'ERROR: Deletion of reference author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
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
	
	public function deleteAuthor($authId){
		$statusStr = '';
		$sql = 'DELETE FROM referenceauthors '.
				'WHERE (refauthorid = '.$authId.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'Author deleted.';
		}
		else{
			$statusStr = 'ERROR: Deletion of author failed: '.$this->conn->error.'<br/>';
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
				$sql = 'DELETE FROM referencetaxalink WHERE refid=? AND taxon=?';
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
	
	public function createAuthor($firstName,$middleName,$lastName){
		global $SYMB_UID;
		$statusStr = '';
		$sql = 'INSERT INTO referenceauthors(firstname,middlename,lastname,modifieduid,modifiedtimestamp) '.
			'VALUES("'.$this->cleanInStr($firstName).'","'.$this->cleanInStr($middleName).'","'.$this->cleanInStr($lastName).'",'.$SYMB_UID.',now()) ';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->refAuthId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new author failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}
	
	public function getRefTypeFieldArr($refTypeId){
		$retArr = array();
		$sql = 'SELECT ReferenceTypeId, ReferenceType, Title, SecondaryTitle, PlacePublished, '.
			'Publisher, Volume, NumberVolumes, Number, Pages, Section, TertiaryTitle, Edition, `Date`, TypeWork, ShortTitle, '.
			'AlternativeTitle, ISBN_ISSN '.
			'FROM referencetype '.
			'WHERE ReferenceTypeId = '.$refTypeId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['ReferenceTypeId'] = $r->ReferenceTypeId;
				$retArr['ReferenceType'] = $r->ReferenceType;
				$retArr['Title'] = $r->Title;
				$retArr['SecondaryTitle'] = $r->SecondaryTitle;
				$retArr['PlacePublished'] = $r->PlacePublished;
				$retArr['Publisher'] = $r->Publisher;
				$retArr['Volume'] = $r->Volume;
				$retArr['NumberVolumes'] = $r->NumberVolumes;
				$retArr['Number'] = $r->Number;
				$retArr['Pages'] = $r->Pages;
				$retArr['Section'] = $r->Section;
				$retArr['TertiaryTitle'] = $r->TertiaryTitle;
				$retArr['Edition'] = $r->Edition;
				$retArr['Date'] = $r->Date;
				$retArr['TypeWork'] = $r->TypeWork;
				$retArr['ShortTitle'] = $r->ShortTitle;
				$retArr['AlternativeTitle'] = $r->AlternativeTitle;
				$retArr['ISBN_ISSN'] = $r->ISBN_ISSN;
			}
			$rs->close();
		}
		return $retArr;
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
	
	public function editBookReference($pArr){
		global $SYMB_UID;
		$statusStr = '';
		$refid = $pArr['refid'];
		$parentrefid = '';
		$parentrefid2 = '';
		unset($pArr['parentrefid2']);
		unset($pArr['refGroup']);
		$pArr = $this->formatInArr($pArr);
		if(is_numeric($refid)){
			if($pArr['ReferenceTypeId'] == 4){
				$serTitle = '';
				if($pArr['volume'] || $pArr['number']){
					if($pArr['tertiarytitle']){
						$serTitle = $pArr['tertiarytitle'];
					}
					else{
						$serTitle = $pArr['secondarytitle'];
					}
					$sql = "";
					$sql = "SELECT refid ".
						"FROM referenceobject ".
						"WHERE (title LIKE '%".$pArr['secondarytitle']."%' OR title LIKE '%".$pArr['tertiarytitle']."%') ".
						"AND publisher = '".$pArr['publisher']."' AND ReferenceTypeId = 27 ".
						"LIMIT 1 ";
					if($result = $this->conn->query($sql)){
						while($row = $result->fetch_object()){
							$parentrefid2 = $row->refid;
						}
					}
					if($parentrefid2){
						$sql = "";
						$sql = 'UPDATE referenceobject '.
							'SET placeofpublication = '.$this->cleanTxtStr($pArr['placeofpublication']).',numbervolumnes = '.$this->cleanTxtStr($pArr['numbervolumnes']).','.
							'edition = '.$this->cleanTxtStr($pArr['edition']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
							'WHERE refid = '.$parentrefid2;
						if($this->conn->query($sql)){
							$statusStr = 'SUCCESS: information saved';
						}
					}
					else{
						$sql = "";
						$sql = 'INSERT INTO referenceobject(ReferenceTypeId,title,placeofpublication,publisher,numbervolumnes,edition,ispublished,modifieduid,modifiedtimestamp) '.
							'VALUES(27,'.$this->cleanTxtStr($serTitle).','.$this->cleanTxtStr($pArr['placeofpublication']).','.$this->cleanTxtStr($pArr['publisher']).','.
							$this->cleanTxtStr($pArr['numbervolumnes']).','.$this->cleanTxtStr($pArr['edition']).','.$this->cleanTxtStr($pArr['ispublished']).','.$SYMB_UID.',now()) ';
						if($this->conn->query($sql)){
							$parentrefid2 = $this->conn->insert_id;
						}
					}
				}
				$bookTitle = '';
				if($pArr['secondarytitle']){
					$bookTitle = $pArr['secondarytitle'];
				}
				else{
					$bookTitle = $pArr['tertiarytitle'];
				}
				$sql = "";
				$sql = "SELECT refid ".
					"FROM referenceobject ".
					"WHERE title LIKE '%".$pArr['secondarytitle']."%' ";
				if($pArr['volume']){
					$sql .= "AND volume = '".$pArr['volume']."' ";
				}
				if($pArr['number']){
					$sql .= "AND number = '".$pArr['number']."' ";
				}
				if($pArr['edition']){
					$sql .= "AND edition = '".$pArr['edition']."' ";
				}
				$sql .= "AND (ReferenceTypeId = 3 OR ReferenceTypeId = 6) ".
					"LIMIT 1 ";
				//echo $sql;
				if($result = $this->conn->query($sql)){
					while ($row = $result->fetch_object()){
						$parentrefid = $row->refid;
					}
				}
				if($parentrefid){
					$sql = "";
					if($parentrefid2){
						$sql = 'UPDATE referenceobject '.
							'SET parentrefid = '.$parentrefid2.',secondarytitle = '.$this->cleanTxtStr($serTitle).',pubdate = '.$this->cleanTxtStr($pArr['pubdate']).',shorttitle = '.$this->cleanTxtStr($pArr['shorttitle']).','.
							'isbn_issn = '.$this->cleanTxtStr($pArr['isbn_issn']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
							'WHERE refid = '.$parentrefid;
					}
					else{
						$sql = 'UPDATE referenceobject '.
							'SET pubdate = '.$this->cleanTxtStr($pArr['pubdate']).',edition = '.$this->cleanTxtStr($pArr['edition']).',shorttitle = '.$this->cleanTxtStr($pArr['shorttitle']).','.
							'publisher = '.$this->cleanTxtStr($pArr['publisher']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',placeofpublication = '.$this->cleanTxtStr($pArr['placeofpublication']).',isbn_issn = '.$this->cleanTxtStr($pArr['isbn_issn']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
							'WHERE refid = '.$parentrefid;
					}
					//echo $sql;
					if($this->conn->query($sql)){
						$statusStr = 'SUCCESS: information saved';
					}
				}
				else{
					$sql = "";
					if($parentrefid2){
						$sql = 'INSERT INTO referenceobject(parentrefid,ReferenceTypeId,title,secondarytitle,volume,number,pubdate,ispublished,shorttitle,isbn_issn,modifieduid,modifiedtimestamp) '.
							'VALUES('.$parentrefid2.',3,'.$this->cleanTxtStr($bookTitle).','.$this->cleanTxtStr($serTitle).','.$this->cleanTxtStr($pArr['volume']).','.$this->cleanTxtStr($pArr['number']).','.
							$this->cleanTxtStr($pArr['pubdate']).','.$this->cleanTxtStr($pArr['ispublished']).','.$this->cleanTxtStr($pArr['shorttitle']).','.$this->cleanTxtStr($pArr['isbn_issn']).','.$SYMB_UID.',now()) ';
					}
					else{
						$sql = 'INSERT INTO referenceobject(ReferenceTypeId,title,volume,number,edition,pubdate,ispublished,shorttitle,publisher,placeofpublication,isbn_issn,modifieduid,modifiedtimestamp) '.
							'VALUES(3,'.$this->cleanTxtStr($bookTitle).','.$this->cleanTxtStr($pArr['volume']).','.$this->cleanTxtStr($pArr['number']).','.$this->cleanTxtStr($pArr['edition']).','.
							$this->cleanTxtStr($pArr['pubdate']).','.$this->cleanTxtStr($pArr['ispublished']).','.$this->cleanTxtStr($pArr['shorttitle']).','.$this->cleanTxtStr($pArr['publisher']).','.$this->cleanTxtStr($pArr['placeofpublication']).','.$this->cleanTxtStr($pArr['isbn_issn']).','.$SYMB_UID.',now()) ';
					}
					//echo $sql;
					if($this->conn->query($sql)){
						$parentrefid = $this->conn->insert_id;
					}
				}
				$sql = "";
				$sql = 'UPDATE referenceobject '.
					'SET parentrefid = '.($parentrefid?$parentrefid:($parentrefid2?$parentrefid2:'NULL')).',ReferenceTypeId = '.$this->cleanTxtStr($pArr['ReferenceTypeId']).','.
					'title = '.$this->cleanTxtStr($pArr['title']).',secondarytitle = '.$this->cleanTxtStr($bookTitle).',tertiarytitle = '.$this->cleanTxtStr($serTitle).',pages = '.$this->cleanTxtStr($pArr['pages']).',guid = '.$this->cleanTxtStr($pArr['guid']).',url = '.$this->cleanTxtStr($pArr['url']).',notes = '.$this->cleanTxtStr($pArr['notes']).','.
					'modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
					'WHERE refid = '.$refid;
				if($this->conn->query($sql)){
					$statusStr = 'SUCCESS: information saved';
				}
			}
			if($pArr['ReferenceTypeId'] == 3 || $pArr['ReferenceTypeId'] == 6){
				if($pArr['volume'] || $pArr['number']){
					$serTitle = $pArr['secondarytitle'];
					$sql = "";
					$sql = "SELECT refid ".
						"FROM referenceobject ".
						"WHERE (title LIKE '%".$pArr['secondarytitle']."%') ".
						"AND publisher = '".$pArr['publisher']."' AND ReferenceTypeId = 27 ".
						"LIMIT 1 ";
					if($result = $this->conn->query($sql)){
						while($row = $result->fetch_object()){
							$parentrefid = $row->refid;
						}
					}
					if($parentrefid){
						$sql = "";
						$sql = 'UPDATE referenceobject '.
							'SET placeofpublication = '.$this->cleanTxtStr($pArr['placeofpublication']).',numbervolumnes = '.$this->cleanTxtStr($pArr['numbervolumnes']).','.
							'edition = '.$this->cleanTxtStr($pArr['edition']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
							'WHERE refid = '.$parentrefid;
						if($this->conn->query($sql)){
							$statusStr = 'SUCCESS: information saved';
						}
					}
					else{
						$sql = "";
						$sql = 'INSERT INTO referenceobject(ReferenceTypeId,title,placeofpublication,publisher,numbervolumnes,edition,ispublished,modifieduid,modifiedtimestamp) '.
							'VALUES(27,'.$this->cleanTxtStr($serTitle).','.$this->cleanTxtStr($pArr['placeofpublication']).','.$this->cleanTxtStr($pArr['publisher']).','.
							$this->cleanTxtStr($pArr['numbervolumnes']).','.$this->cleanTxtStr($pArr['edition']).','.$this->cleanTxtStr($pArr['ispublished']).','.$SYMB_UID.',now()) ';
						if($this->conn->query($sql)){
							$parentrefid = $this->conn->insert_id;
						}
					}
					$sql = "";
					$sql = "";
					$sql = 'UPDATE referenceobject '.
						'SET parentrefid = '.($parentrefid?$parentrefid:'NULL').',ReferenceTypeId = '.$this->cleanTxtStr($pArr['ReferenceTypeId']).',title = '.$this->cleanTxtStr($pArr['title']).','.
						'secondarytitle = '.$this->cleanTxtStr($serTitle).',volume = '.$this->cleanTxtStr($pArr['volume']).',number = '.$this->cleanTxtStr($pArr['number']).','.
						'pages = '.$this->cleanTxtStr($pArr['pages']).',pubdate = '.$this->cleanTxtStr($pArr['pubdate']).',shorttitle = '.$this->cleanTxtStr($pArr['shorttitle']).','.
						'isbn_issn = '.$this->cleanTxtStr($pArr['isbn_issn']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
						'WHERE refid = '.$refid;
					if($this->conn->query($sql)){
						$statusStr = 'SUCCESS: information saved';
					}
				}
				else{
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
			}
			if($pArr['ReferenceTypeId'] == 27){
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
		}
		return $statusStr;
	}
	
	public function editPerReference($pArr){
		global $SYMB_UID;
		$statusStr = '';
		$refid = $pArr['refid'];
		$parentrefid = '';
		$parentrefid2 = '';
		unset($pArr['parentrefid2']);
		unset($pArr['refGroup']);
		$pArr = $this->formatInArr($pArr);
		if(is_numeric($refid)){
			if($pArr['ReferenceTypeId'] == 2 || $pArr['ReferenceTypeId'] == 7 || $pArr['ReferenceTypeId'] == 8){
				if($pArr['volume'] || $pArr['number'] || $pArr['edition']){
					$sql = "";
					$sql = "SELECT refid ".
						"FROM referenceobject ".
						"WHERE title LIKE '%".$pArr['secondarytitle']."%' ";
					if($pArr['volume']){
						$sql .= "AND volume = '".$pArr['volume']."' ";
					}
					if($pArr['number']){
						$sql .= "AND number = '".$pArr['number']."' ";
					}
					if($pArr['edition']){
						$sql .= "AND edition = '".$pArr['edition']."' ";
					}
					if($pArr['ReferenceTypeId'] == 8 && $pArr['pubdate']){
						$sql .= "AND pubdate = '".$pArr['pubdate']."' ";
					}
					$sql .= "AND (ReferenceTypeId = 30) ".
						"LIMIT 1 ";
					if($result = $this->conn->query($sql)){
						while($row = $result->fetch_object()){
							$parentrefid = $row->refid;
						}
					}
					if($parentrefid){
						$sql = "";
						$sql = 'UPDATE referenceobject '.
							'SET placeofpublication = '.$this->cleanTxtStr($pArr['placeofpublication']).',pubdate = '.$this->cleanTxtStr($pArr['pubdate']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).','.
							'shorttitle = '.$this->cleanTxtStr($pArr['shorttitle']).',alternativetitle = '.$this->cleanTxtStr($pArr['alternativetitle']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
							'WHERE refid = '.$parentrefid;
						if($this->conn->query($sql)){
							$statusStr = 'SUCCESS: information saved';
						}
					}
					else{
						$sql = "";
						$sql = 'INSERT INTO referenceobject(ReferenceTypeId,ispublished,title,placeofpublication,pubdate,volume,number,edition,shorttitle,alternativetitle,modifieduid,modifiedtimestamp) '.
							'VALUES(30,'.$this->cleanTxtStr($pArr['ispublished']).','.$this->cleanTxtStr($pArr['secondarytitle']).','.$this->cleanTxtStr($pArr['placeofpublication']).','.$this->cleanTxtStr($pArr['pubdate']).','.$this->cleanTxtStr($pArr['volume']).','.
							$this->cleanTxtStr($pArr['number']).','.$this->cleanTxtStr($pArr['edition']).','.$this->cleanTxtStr($pArr['shorttitle']).','.$this->cleanTxtStr($pArr['alternativetitle']).','.$SYMB_UID.',now()) ';
						if($this->conn->query($sql)){
							$parentrefid = $this->conn->insert_id;
						}
					}
					$sql = "";
					$sql = "";
					$sql = 'UPDATE referenceobject '.
						'SET parentrefid = '.($parentrefid?$parentrefid:'NULL').',ReferenceTypeId = '.$this->cleanTxtStr($pArr['ReferenceTypeId']).',ispublished = '.$this->cleanTxtStr($pArr['ispublished']).',title = '.$this->cleanTxtStr($pArr['title']).','.
						'pages = '.$this->cleanTxtStr($pArr['pages']).',section = '.$this->cleanTxtStr($pArr['section']).',url = '.$this->cleanTxtStr($pArr['url']).',guid = '.$this->cleanTxtStr($pArr['guid']).','.
						'notes = '.$this->cleanTxtStr($pArr['notes']).',secondarytitle = '.$this->cleanTxtStr($pArr['secondarytitle']).',typework = '.$this->cleanTxtStr($pArr['typework']).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() '.
						'WHERE refid = '.$refid;
					if($this->conn->query($sql)){
						$statusStr = 'SUCCESS: information saved';
					}
				}
				else{
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
			}
			if($pArr['ReferenceTypeId'] == 30){
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
	
	public function editAuthor($pArr){
		global $SYMB_UID;
		$statusStr = '';
		$authId = $pArr['authid'];
		if(is_numeric($authId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k != 'formsubmit' && $k != 'authid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE referenceauthors SET '.substr($sql,1).',modifieduid='.$SYMB_UID.',modifiedtimestamp=now() WHERE (refauthorid = '.$authId.')';
			//echo $sql;
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of author failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}
	
	//Get and set functions 
	public function getrefid(){
		return $this->refid;
	}
	
	public function getRefAuthId(){
		return $this->refAuthId;
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