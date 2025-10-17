<?php
include_once('TPEditorManager.php');
include_once('ImageShared.php');

class TPImageEditorManager extends TPEditorManager{

 	public function __construct(){
 		parent::__construct();
		set_time_limit(120);
		ini_set('max_input_time',120);
 	}

 	public function __destruct(){
 		parent::__destruct();
 	}

	public function echoCreatorSelect($userId = 0){
		$sql = 'SELECT u.uid, CONCAT_WS(", ",u.lastname,u.firstname) AS fullname FROM users u ORDER BY u.lastname, u.firstname ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			echo '<option value="'.$r->uid.'" '.($r->uid == $userId?'SELECTED':'').'>'.$r->fullname.'</option>';
		}
		$rs->free();
	}

	public function editImageSort($imgSortEdits){
		$status = "";
		foreach($imgSortEdits as $editKey => $editValue){
			if(is_numeric($editKey) && is_numeric($editValue)){
				$sql = 'UPDATE media SET sortsequence = '.$editValue.' WHERE mediaID = '.$editKey;
				//echo $sql;
				if(!$this->conn->query($sql)){
					$status .= $this->conn->error."\nSQL: ".$sql."; ";
				}
			}
		}
		if($status) $status = "with editImageSort method: ".$status;
		return $status;
	}

	// TODO (Logan) deprecate function
	// commenting out for now as instructed
	// public function loadImage($postArr){
	// 	$status = true;
	// 	$imgManager = new ImageShared();
	// 	$imgManager->setTid($this->tid);
	// 	$imgManager->setCaption($postArr['caption']);
	// 	if($postArr['creator']){
	// 		$imgManager->setCreator($postArr['creator']);
	// 	} else {
	// 		$imgManager->setCreatorUid($postArr['creatoruid']);
	// 	}
	// 	$imgManager->setSourceUrl($postArr['sourceurl']);
	// 	$imgManager->setCopyright($postArr['copyright']);
	// 	$imgManager->setOwner($postArr['owner']);
	// 	$imgManager->setLocality($postArr['locality']);
	// 	$imgManager->setOccid($postArr['occid']);
	// 	$imgManager->setNotes($postArr['notes']);
	// 	$sort = $postArr['sortsequence'];
	// 	if(!$sort) $sort = 40;
	// 	$imgManager->setSortSeq($sort);
	//
	// 	$imgManager->setTargetPath(($this->family?$this->family.'/':'').date('Ym').'/');
	// 	$imgPath = $postArr['filepath'];
	// 	if($imgPath){
	// 		$imgManager->setMapLargeImg(true);
	// 		$imgManager->parseUrl($imgPath);
	// 		$importUrl = (array_key_exists('importurl',$postArr) && $postArr['importurl']==1?true:false);
	// 		if($importUrl) $imgManager->copyImageFromUrl();
	// 	}
	// 	else{
	// 		$createLargeImg = false;
	// 		if(array_key_exists('createlargeimg',$postArr) && $postArr['createlargeimg']==1) $createLargeImg = true;
	// 		$imgManager->setMapLargeImg($createLargeImg);
	// 		if(!$imgManager->uploadImage()){
	// 			//echo implode('; ',$imgManager->getErrArr());
	// 		}
	// 	}
	// 	if(!$imgManager->processImage()){
	// 		$this->errorMessage = implode('<br/>',$imgManager->getErrArr());
	// 		$status = false;
	// 	}
	// 	return $status;
	// }
}
?>
