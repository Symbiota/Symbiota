<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class AdminProperties extends Manager{

	public function __construct(){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}



	//Misc data support functions
	private function insertProperty($category, $propType, $propName, $propValue, $tableName = null, $tablePK = null){
		$status = false;
		$sql = 'INSERT INTO adminproperties()';


		return $status;
	}

	//System transfer functions
	public function oldVariablesAreImported(){
		$statusCode = false;
		$sql = 'SELECT propID FROM adminproperties LIMIT 1';
		if($rs = $this->conn->query($sql)){
			if($rs->num_rows) $statusCode = true;
			$rs->free();
		}
		return $statusCode;
	}

	public function importConfigurationVariables(){
		//Import selected import variables from symbini.php file

	}

	public function importCollectionDynamicProperties(){
		$status = false;
		$sql = 'SELECT collid, dynamicProperties FROM omcollections WHERE dynamicProperties IS NOT NULL';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$dynPropArr = json_decode($r->dynamicProperties, true);
				if($dynPropArr) $status = true;
				if(isset($dynPropArr['editorProps']['modules-panel'])){
					collectionConfig, propType: MODULE_ACTIVATION, propName: PALEO_MODULE, propValue: 0, 1
					$this->insertProperty('collectionConfig', 'MODULE_ACTIVATION', 'PALEO_MODULE', $dynPropArr['editorProps']['modules-panel'], 'omcollections', $r->collid);
				}
				if(isset($dynPropArr['publicationProps'])){
					$this->insertProperty('publicationProperties', 'Publication Properties', json_encode($dynPropArr['publicationProps']));
				}
				if(isset($dynPropArr['labelFormats'])){
					foreach($dynPropArr['labelFormats'] as $v){
						$this->insertProperty('labelFormat', $v['title'], json_encode($v));
					}
				}
			}
			$rs->free();
		}
		return $status;
	}




	//Setters and getters


}
?>