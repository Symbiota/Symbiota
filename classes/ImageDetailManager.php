<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/ImageShared.php');

class ImageDetailManager extends Manager {

	private $imgId;

	public function __construct($id,$conType='readonly'){
		parent::__construct(null, $conType);
		if(is_numeric($id)){
			$this->imgId = $id;
		}
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function getImageMetadata(){
		$retArr = Array();
		if($this->imgId){
			$sql = "SELECT m.mediaID, m.tid, m.url, m.thumbnailurl, m.originalurl, m.creatorUid, m.creator, ".
				"IFNULL(m.creator,CONCAT_WS(' ',u.firstname,u.lastname)) AS creatorDisplay, ".
				"m.caption, m.owner, m.sourceurl, m.copyright, m.rights, m.locality, m.notes, m.occid, m.sortsequence, m.username, ".
				"t.sciname, t.author, t.rankid, m.format, m.mediaType ".
				"FROM media m LEFT JOIN taxa t ON m.tid = t.tid ".
				"LEFT JOIN users u ON m.creatorUid = u.uid ".
				'WHERE (m.mediaID = '.$this->imgId.')';
			//echo "<div>$sql</div>";
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$retArr["tid"] = $row->tid;
				$retArr["sciname"] = $row->sciname;
				$retArr["author"] = $this->cleanOutStr($row->author);
				$retArr["rankid"] = $row->rankid;
				$retArr["url"] = $row->url;
				$retArr["thumbnailurl"] = $row->thumbnailurl;
				$retArr["originalurl"] = $row->originalurl;
				$retArr["creator"] = $this->cleanOutStr($row->creator);
				$retArr["creatorDisplay"] = $row->creatorDisplay;
				$retArr["creatorUid"] = $row->creatorUid;
				$retArr["caption"] = $this->cleanOutStr($row->caption);
				$retArr["owner"] = $this->cleanOutStr($row->owner);
				$retArr["sourceurl"] = $this->cleanOutStr($row->sourceurl);
				$retArr["copyright"] = $this->cleanOutStr($row->copyright);
				$retArr["rights"] = $this->cleanOutStr($row->rights);
				$retArr["locality"] = $this->cleanOutStr($row->locality);
				$retArr["notes"] = $this->cleanOutStr($row->notes);
				$retArr["sortsequence"] = $row->sortsequence;
				$retArr["occid"] = $row->occid;
				$retArr["username"] = $row->username;
				$retArr["mediaType"] = $row->mediaType;
				$retArr["format"] = $row->format;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function changeTaxon($targetTid,$sourceTid){
		$status = '';
		if(is_numeric($targetTid) && is_numeric($sourceTid)){
			$sql = 'UPDATE media SET tid = '.$targetTid.', sortsequence = 50 WHERE mediaID = '.$this->imgId.' and mediaType = "image" AND tid = '.$sourceTid;
			if(!$this->conn->query($sql)){
				$sql = 'SELECT m.mediaID '.
					'FROM media m INNER JOIN media m2 ON m.url = m2.url '.
					'WHERE (m.tid = '.$targetTid.') AND (m2.mediaID = '.$this->imgId.')';
				$rs = $this->conn->query($sql);
				if($rs->num_rows){
					//Transfer is not happening because image is already mapped to that taxon
					$sql2 = 'DELETE FROM media WHERE (mediaID = '.$this->imgId.') AND (tid = '.$sourceTid.')';
					$this->conn->query($sql2);
				}
				$rs->free();
			}
		}
		return $status;
	}

	public function deleteImage($imgIdDel, $removeImg){
		$retStr = '';
		$imgManager = new ImageShared();
		if($imgManager->deleteImage($imgIdDel, $removeImg)){
			$retStr = $imgManager->getTid();
		}
		$errArr = $imgManager->getErrArr();
		if($errArr){
			$retStr .= 'ERROR: ('.implode('; ',$errArr).')';
		}
		return $retStr;
	}

	public function echoCreatorSelect($userId = 0){
		$sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
			"FROM users u ORDER BY u.lastname, u.firstname ";
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			echo "<option value='".$row->uid."' ".($row->uid == $userId?"SELECTED":"").">".$row->fullname."</option>\n";
		}
		$result->free();
	}
}
?>
