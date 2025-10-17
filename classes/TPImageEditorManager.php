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
}
?>
