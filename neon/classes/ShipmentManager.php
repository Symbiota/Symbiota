<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class ShipmentManager{

	private $conn;
	private $shipmentPK;
	private $shipmentArr = array();
	private $uploadFileName;
	private $reloadSampleRecs = false;
	private $fieldMap = array();
	private $sourceArr = array();
	private $searchArr = array();
	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 		ini_set('auto_detect_line_endings', true);
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	public function getShipmentArr(){
		if(!$this->shipmentArr) $this->setShipmentArr();
		return $this->shipmentArr;
	}

	private function setShipmentArr(){
		if($this->shipmentPK){
			$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.shippedFrom, s.senderID, s.destinationFacility, s.sentToID, s.shipmentService, s.shipmentMethod, '.
				's.trackingNumber, s.receivedDate, s.receivedBy, s.notes AS shipmentNotes, s.fileName, s.receiptStatus, s.receiptTimestamp, CONCAT_WS(", ", u.lastname, u.firstname) AS importUser,
				CONCAT_WS(", ", u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, CONCAT_WS(", ", u3.lastname, u3.firstname) AS modifiedUser, s.modifiedTimestamp, s.initialtimestamp AS ts '.
				'FROM NeonShipment s INNER JOIN users u ON s.importUid = u.uid '.
				'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
				'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid '.
				'LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
				'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			$headerArr = array();
			while($r = $rs->fetch_assoc()){
				if(!$headerArr){
					$headerArr = array_keys($r);
					unset($headerArr[array_search('shipmentPK', $headerArr)]);
				}
				foreach($headerArr as $colName){
					$this->shipmentArr[$colName] = $r[$colName];
				}
			}
			$rs->free();
			if(isset($this->shipmentArr['domainID']) && $this->shipmentArr['domainID']){
				$domainTitle = $this->getDomainTitle($this->shipmentArr['domainID']);
				if($domainTitle) $this->shipmentArr['domainTitle'] = $domainTitle;
			}
		}
	}

	public function getSampleCount(){
		$retArr = array();
		//Get total sample count
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['all'] = $r->cnt;
		}
		$rs->free();
		//Get sample count not yet checked-in
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[0] = $r->cnt;
		}
		$rs->free();
		//Get count of samples not yet imported
		$sql = 'SELECT COUNT(s.samplepk) AS cnt '.
			'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
			'WHERE (s.shipmentPK = '.$this->shipmentPK.') AND (o.occid IS NULL) ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[1] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getSampleArr($samplePK = null, $filter = null){
		$retArr = array();
		$headerArr = array('sampleID','hashedSampleID','alternativeSampleID','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate','quarantineStatus',
			'sampleReceived','acceptedForAnalysis','sampleCondition','dynamicProperties','symbiotaTarget','sampleNotes','occurErr','occid','harvestTimestamp','checkinUser','checkinRemarks','checkinTimestamp');
		$targetArr = array();
		$sql = 'SELECT s.samplePK, s.sampleID, s.hashedSampleID, s.alternativeSampleID, s.sampleCode, s.sampleClass, s.taxonID, s.individualCount, s.filterVolume, s.namedLocation, s.domainRemarks, s.collectDate, '.
			's.quarantineStatus, s.sampleReceived, s.acceptedForAnalysis, s.sampleCondition, s.dynamicProperties, s.symbiotaTarget, s.notes as sampleNotes, s.errorMessage as occurErr, '.
			'CONCAT_WS(", ", u.lastname, u.firstname) as checkinUser, s.checkinRemarks, s.checkinTimestamp, s.occid, s.harvestTimestamp '.
			'FROM NeonSample s LEFT JOIN users u ON s.checkinuid = u.uid ';
		if($samplePK){
			$sql .= 'WHERE (s.samplePK = '.$samplePK.') ';
		}
		else{
			$sql .= 'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
			if($filter){
				if($filter == 'notCheckedIn'){
					$sql .= 'AND (s.checkinTimestamp IS NULL) ';
				}
				elseif($filter == 'missingOccid'){
					$sql .= 'AND (s.occid IS NULL) ';
				}
				elseif($filter == 'notAccepted'){
					$sql .= 'AND (s.acceptedForAnalysis = 0) ';
				}
				elseif($filter == 'altIds'){
					$sql .= 'AND (s.alternativeSampleID IS NOT NULL) ';
				}
				elseif($filter == 'harvestingError'){
					$sql .= 'AND (s.errorMessage IS NOT NULL) ';
				}
			}
			$sql .= 'ORDER BY s.sampleID,s.sampleCode ';
		}
		$rs = $this->conn->query($sql);
		//Pass through recordset to see which fields have at least one value
		while($r = $rs->fetch_assoc()){
			foreach($headerArr as $fieldName){
				if($r[$fieldName] != '' && !in_array($fieldName, $targetArr)) $targetArr[] = $fieldName;
			}
		}
		if(!array_key_exists('checkinTimestamp', $targetArr)){
			$targetArr[] = 'checkinUser';
			$targetArr[] = 'checkinTimestamp';
		}
		$siteTitleArr = $this->getSiteTitleArr();
		$rs->data_seek(0);
		//Grab data for only columns that have data
		while($r = $rs->fetch_assoc()){
			foreach($targetArr as $fieldName){
				$retArr[$r['samplePK']][$fieldName] = $r[$fieldName];
			}
			if(isset($retArr[$r['samplePK']]['namedLocation'])){
				$namedLocation = $retArr[$r['samplePK']]['namedLocation'];
				if(preg_match('/^([A-Za-z]+)/',$namedLocation,$m)){
					if(array_key_exists($m[1], $siteTitleArr)) $retArr[$r['samplePK']]['siteTitle'] = $siteTitleArr[$m[1]];
				}
			}
		}
		$rs->free();
		if($samplePK) return array_shift($retArr);
		return $retArr;
	}

	//Shipment import functions
	public function uploadManifestFile(){
		$status = false;
		//Load file onto server
		$uploadPath = $this->getContentPath();
		if(array_key_exists("uploadfile",$_FILES)){
			$this->initiateUploadFileName($_FILES['uploadfile']['name'],$uploadPath);
			if(!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadPath.$this->uploadFileName)){
				$this->errorStr = 'ERROR uploading file (code '.$_FILES['uploadfile']['error'].'): ';
				if(!is_writable($uploadPath)) $this->errorStr .= 'Target path ('.$uploadPath.') is not writable ';
				return false;
			}
			//If a zip file, unpackage and assume that last or only file is the occurrrence file
			if($this->uploadFileName && substr($this->uploadFileName,-4) == ".zip"){
				$zipFilePath = $uploadPath.$this->uploadFileName;
				$fileName = '';
				$zip = new ZipArchive;
				$res = $zip->open($zipFilePath);
				if($res === TRUE) {
					for($i = 0; $i < $zip->numFiles; $i++) {
						$name = $zip->getNameIndex($i);
						if(substr($name,0,2) != '._'){
							$ext = strtolower(substr(strrchr($name, '.'), 1));
							if($ext == 'csv'){
								$fileName = $name;
								break;
							}
						}
					}
					if($fileName){
						$this->initiateUploadFileName($fileName,$uploadPath);
						$zip->extractTo($uploadPath,$this->uploadFileName);
					}
					else{
						$this->uploadFileName = '';
					}
				}
				else{
					echo 'failed, code:' . $res;
					return false;
				}
				$zip->close();
				unlink($zipFilePath);
			}
		}
		return $status;
	}

	private function initiateUploadFileName($fileName,$uploadPath){
		$fName = str_replace(array(' ','(',')',';','%','$',","),'_',$fileName);
		$fName = substr($fName,0,strrpos($fName,'.'));
		if(strlen($fName) > 35) $fName = substr($fName,35);
		$ext = substr($fileName,strrpos($fileName,'.')+1);
		$cnt = 1;
		$fileName = $fName.'.'.$ext;
		while(file_exists($uploadPath.$fileName)){
			$fileName = $fName.'_'.$cnt.'.'.$ext;
			$cnt++;
		}
		$this->uploadFileName = $fileName;
	}

	public function analyzeUpload(){
		$status = false;
		//Read first line of file to obtain source field names
		if($this->uploadFileName){
			$fullPath = $this->getContentPath().$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$this->sourceArr = $this->getHeaderArr($fh);
			$status = true;
			//Continue iterating through file to obtain all shipmentIDs, and then insure shipment doesn't already exist
			$shipmentIdIndex = false;
			foreach($this->sourceArr as $k => $colName){
				if(strtolower(str_replace(array(' ','_'),'',$colName)) == 'shipmentid'){
					$shipmentIdIndex = $k;
					break;
				}
			}
			if(is_numeric($shipmentIdIndex)){
				$shipmentIDArr = array();
				while($recordArr = fgetcsv($fh)){
					$shipmentIDArr[$recordArr[$shipmentIdIndex]] = '';
				}
				if($shipmentIDArr){
					//Check to make sure shipments IDs were not previously entered
					$dupeShipmentExists = false;
					$sql = 'SELECT shipmentPK, shipmentID FROM NeonShipment WHERE shipmentid IN("'.implode('","',array_keys($shipmentIDArr)).'")';
					$rs = $this->conn->query($sql);
					while($r = $rs->fetch_object()){
						$shipmentIDArr[$r->shipmentID] = $r->shipmentPK;
						$dupeShipmentExists = true;
					}
					$rs->free();
					if($dupeShipmentExists){
						$status = false;
						$this->errorStr = '<div>ERROR: shipment already in system</div>';
						foreach($shipmentIDArr as $id => $pk){
							if($pk) $this->errorStr .= '<div style="margin-left:10px"><a href="manifestviewer.php?shipmentPK='.$pk.'" target="_blank">'.$id.'</a></div>';
						}
					}
				}
				else{
					$this->errorStr = 'ERROR: failed to return shipmentID ';
					$status = false;
				}
			}
			else{
				$this->errorStr = 'ERROR: Unable to locate shipmentID column (required). Please make sure the column exists and is named appropriately';
				$status = false;
			}
			fclose($fh);
		}
		return $status;
	}

	public function uploadData(){
		$status = true;
		set_time_limit(1800);
		$shipmentArr = array();
		if($this->uploadFileName){
			echo '<li>Initiating import from: '.$this->uploadFileName.'</li>';
			$fullPath = $this->getContentPath().$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$headerArr = $this->getHeaderArr($fh);
			$recCnt = 0;
			//Setup record index array
			$indexMap = array();
			foreach($this->fieldMap as $sourceField => $targetField){
				$indexArr = array_keys($headerArr,$sourceField);
				$index = array_shift($indexArr);
				$indexMap[$targetField][$sourceField] = $index;
			}
			echo '<li>Beginning to load records...</li>';
			ob_flush();
			flush();
			$errCnt = 0;
			while($recordArr = fgetcsv($fh)){
				$recMap = Array();
				$dynPropArr = array();
				$symTargetArr = array();
				foreach($indexMap as $targetField => $indexValueArr){
					foreach($indexValueArr as $sField => $indexValue){
						if(strtolower($targetField) == 'dynamicproperties'){
							if($recordArr[$indexValue]) $dynPropArr[$sField] = $recordArr[$indexValue];
						}
						elseif(substr($targetField,0,5) == 'symb:'){
							$symbValue = $this->cleanInStr($recordArr[$indexValue]);
							if($symbValue !== '') $symTargetArr[substr($targetField,5)] = $symbValue;
						}
						else{
							$recMap[$targetField] = $recordArr[$indexValue];
						}
					}
				}
				if($dynPropArr){
					$recMap['dynamicproperties'] = json_encode($dynPropArr);
				}
				if($symTargetArr){
					$recMap['symbiotatarget'] = json_encode($symTargetArr);
				}
				$recMap = array_change_key_case($recMap);
				if(!array_key_exists($recMap['shipmentid'],$shipmentArr)) $shipmentArr[$recMap['shipmentid']] = $this->loadShipmentRecord($recMap);
				$this->shipmentPK = $shipmentArr[$recMap['shipmentid']];
				if($this->shipmentPK){
					if($this->addSample($recMap,true)){
						$recCnt++;
						if($recCnt%1000 == 0){
							echo '<li>'.$recCnt.' record loaded</li>';
							ob_flush();
							flush();
						}
					}
					else $errCnt++;
				}
				unset($recMap);
			}
			fclose($fh);
			echo '<li>Complete: '.$recCnt.' records loaded '.($errCnt?'('.$errCnt.' errors)':'').'</li>';
		}
		else{
			$this->outputMsg('<li>File Upload FAILED: unable to locate file</li>');
		}
		return $shipmentArr;
	}

	private function loadShipmentRecord($recArr){
		$shipmentPK = 0;
		$trackingId = '';
		if(isset($recArr['trackingnumber'])){
			$trackingId = trim($recArr['trackingnumber'],' #');
			$trackingId = str_replace(array("\n",','), ';', $trackingId);
			$trackingId = preg_replace('/[^a-zA-Z0-9;]+/', '', $trackingId);
			if($trackingId == 'none') $trackingId = '';
		}
		$sql = 'INSERT INTO NeonShipment(shipmentID, domainID, dateShipped, shippedFrom,senderID, destinationFacility, sentToID, shipmentService, shipmentMethod, trackingNumber, notes, fileName, importUid) '.
			'VALUES("'.$this->cleanInStr($recArr['shipmentid']).'",'.(isset($recArr['domainid'])?'"'.$this->cleanInStr($recArr['domainid']).'"':'NULL').','.
			(isset($recArr['dateshipped'])&&$recArr['dateshipped']?'"'.$this->cleanInStr($this->formatDate($recArr['dateshipped'])).'"':'NULL').','.
			(isset($recArr['shippedfrom'])?'"'.$this->cleanInStr($recArr['shippedfrom']).'"':'NULL').','.(isset($recArr['senderid'])?'"'.$this->cleanInStr($recArr['senderid']).'"':'NULL').','.
			(isset($recArr['destinationfacility'])?'"'.$this->cleanInStr($recArr['destinationfacility']).'"':'NULL').','.
			(isset($recArr['senttoid'])?'"'.$this->cleanInStr($recArr['senttoid']).'"':'NULL').','.
			(isset($recArr['shipmentservice'])?'"'.$this->cleanInStr($recArr['shipmentservice']).'"':'NULL').','.
			(isset($recArr['shipmentmethod'])?'"'.$this->cleanInStr($recArr['shipmentmethod']).'"':'NULL').','.
			($trackingId?'"'.$trackingId.'"':'NULL').','.
			(isset($recArr['shipmentnotes'])?'"'.$this->cleanInStr($recArr['shipmentnotes']).'"':'NULL').','.
			($this->uploadFileName?'"'.$this->cleanInStr($this->uploadFileName).'"':'NULL').','.
			$GLOBALS['SYMB_UID'].')';
		//echo '<div>'.$sql.'</div>';
		if($this->conn->query($sql)){
			$shipmentPK = $this->conn->insert_id;
			echo '<li>New shipment created (#<a href="manifestviewer.php?shipmentPK='.$shipmentPK.'" target="_blank">'.$recArr['shipmentid'].'</a>)</li>';
		}
		else{
			if($this->conn->errno == 1062){
				$existingFileName = '';
				$sql = 'SELECT shipmentpk, filename FROM NeonShipment WHERE shipmentID = "'.$this->cleanInStr($recArr['shipmentid']).'"';
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()){
					$shipmentPK = $r->shipmentpk;
					$existingFileName = $r->filename;
				}
				$rs->free();
				if(!in_array($this->uploadFileName,explode('|',$existingFileName))){
					//Append new filename to existing
					$this->conn->query('UPDATE NeonShipment SET filename = "'.trim($this->cleanInStr($existingFileName.'|'.$this->uploadFileName),' |').'" WHERE shipmentpk = '.$shipmentPK);
				}
				echo '<li><span style="color:orange">NOTICE:</span> Samples mapped to existing shipment: <a href="manifestviewer.php?shipmentPK='.$shipmentPK.'" target="_blank">'.$recArr['shipmentid'].'</a></li>';
			}
			else{
				echo '<li style="margin-left:15px"><span style="color:red">ERROR</span> loading shipment record (errNo: '.$this->conn->errno.'): '.$this->conn->error.'</li>';
				echo '<li style="margin-left:15px">SQL: '.$sql.'</li>';
				return 0;
			}
		}
		return $shipmentPK;
	}

	private function getHeaderArr($fHandler){
		$retArr = array();
		$headerArr = fgetcsv($fHandler);
		foreach($headerArr as $field){
			//$fieldStr = strtolower(trim($field));
			$fieldStr = trim($field);
			//if($fieldStr) $retArr[] = $fieldStr;
			$retArr[] = $fieldStr;
		}
		return $retArr;
	}

	//Check-in functions
	public function checkinShipment($postArr){
		$sql = 'UPDATE NeonShipment '.
			'SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), '.
			'receivedDate = '.($postArr['receivedDate']?'"'.$this->cleanInStr($postArr['receivedDate']).' '.$this->cleanInStr($postArr['receivedTime']).'"':'NULL').', '.
			'receivedBy = '.($postArr['receivedBy']?'"'.$this->cleanInStr($postArr['receivedBy']).'"':'NULL').', '.
			'notes = '.($postArr['notes']?'CONCAT_WS("; ",notes,"'.$this->cleanInStr($postArr['notes']).'")':'NULL').' '.
			'WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR checking-in shipment: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function setReceiptStatus($status, $onlyIfNull = false){
		$statusStr = '';
		if($status == 1) $statusStr = 'Downloaded';
		if($status == 2) $statusStr = 'Submitted';
		if($statusStr) $statusStr .= ':'.$GLOBALS['USERNAME'];
		$sql = 'UPDATE NeonShipment
			SET receiptstatus = '.($statusStr?'"'.$statusStr.'"':'NULL').', receiptTimestamp = NOW()
			WHERE (shipmentpk = '.$this->shipmentPK.') AND (receiptstatus IS NULL OR receiptstatus LIKE "Downloaded%")';
		if($onlyIfNull) $sql .= 'AND (receiptstatus IS NULL)';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR tagging receipt as submitted: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function getDeliveryArr(){
		$retArr = array();
		if($this->shipmentArr['shipmentService']){
			if($this->shipmentArr['shipmentService'] == 'FedEx') $retArr = $this->getFedExDeliveryStats();
			//elseif($this->shipmentArr['shipmentService'] == '') $retArr = $this->;
		}
		//if(!isset($retArr['receivedBy']) || !$retArr['receivedBy']) $retArr['receivedBy'] = $GLOBALS['USERNAME'];
		if(!isset($retArr['receivedDate']) || !$retArr['receivedDate']){
			//$retArr['receivedDate'] = date('Y-m-d');
			//$retArr['receivedTime'] = date('H:i:s');
		}
		return $retArr;
	}

	private function getFedExDeliveryStats(){
		$retArr = array();
		$trackingNumber = $this->shipmentArr['trackingNumber'];

		$retArr['receivedBy'] = '';
		$retArr['receivedDate'] = '';
		return $retArr;
	}

	public function checkinSample($sampleID, $sampleReceived, $acceptedForAnalysis, $condition, $alternativeSampleID, $notes){
		$status = 3;
		$samplePK = 0;
		// status: 0 = check-in failed, 1 = check-in success, 2 = sample already checked-in, 3 = sample not found, 4 = shipment not yet checked in
		if($sampleID){
			//Make sure shipment has been checked in
			$sql = 'SELECT p.checkintimestamp '.
				'FROM NeonShipment p INNER JOIN NeonSample s ON p.shipmentpk = s.shipmentpk '.
				'WHERE s.sampleID = "'.$this->cleanInStr($sampleID).'" OR s.sampleCode = "'.$this->cleanInStr($sampleID).'"';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if(!$r->checkintimestamp) $status = 4;
				else $status = 0;
			}
			$rs->free();

			if(!$status){
				//Check to see if sample has already been checked in
				$sql = 'SELECT samplePK, alternativeSampleID, checkinTimestamp FROM NeonSample WHERE (sampleID = "'.$this->cleanInStr($sampleID).'" OR sampleCode = "'.$this->cleanInStr($sampleID).'") ';
				if($this->shipmentPK) $sql .= 'AND (shipmentpk = '.$this->shipmentPK.') ';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$samplePK = $r->samplePK;
					if($alternativeSampleID && $r->alternativeSampleID && $alternativeSampleID != $r->alternativeSampleID) $alternativeSampleID .= '; '.$r->alternativeSampleID;
					if($r->checkinTimestamp) $status = 2;
					else $status = 1;
				}
				$rs->free();
				if($status == 1 && $samplePK){
					//Let's check-in sample
					$sampleReceived = ($sampleReceived?1:0);
					if($acceptedForAnalysis === '') $acceptedForAnalysis = 'NULL';
					else $acceptedForAnalysis = ($acceptedForAnalysis?1:0);
					$sqlUpdate = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), sampleReceived = '.$sampleReceived.', acceptedForAnalysis = '.$acceptedForAnalysis.' ';
					if($condition) $sqlUpdate .= ', sampleCondition = CONCAT_WS("; ",sampleCondition,"'.$this->cleanInStr($condition).'") ';
					if($notes) $sqlUpdate .= ', checkinRemarks = "'.$this->cleanInStr($notes).'" ';
					if($alternativeSampleID) $sqlUpdate .= ', alternativeSampleID = "'.$this->cleanInStr($alternativeSampleID).'" ';
					$sqlUpdate .= 'WHERE (samplePK = "'.$samplePK.'") ';
					if(!$this->conn->query($sqlUpdate)){
						$this->errorStr = 'ERROR checking-in NEON sample: '.$this->conn->error;
						$status = 0;
					}
				}
			}
		}
		$retJson = json_encode(array('status'=>$status,'samplePK'=>$samplePK,'errorMsg'=>$this->errorStr));
		return $retJson;
	}

	public function batchCheckinSamples($postArr){
		if($this->shipmentPK){
			$pkArr = $postArr['scbox'];
			if($pkArr){
				$sampleReceived = ($postArr['sampleReceived']?1:0);
				$acceptedForAnalysis = 'NULL';
				if(isset($postArr['acceptedForAnalysis'])) $acceptedForAnalysis = ($postArr['acceptedForAnalysis']?1:0);
				$sql = 'UPDATE NeonSample SET '.
					'checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), sampleReceived = '.$sampleReceived.', acceptedForAnalysis = '.$acceptedForAnalysis.' '.
					($postArr['sampleCondition']?', sampleCondition = "'.$this->cleanInStr($postArr['sampleCondition']).'" ':'').
					($postArr['checkinRemarks']?', checkinRemarks = "'.$this->cleanInStr($postArr['checkinRemarks']).'" ':'').
					'WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinTimestamp IS NULL) AND (samplePK IN('.implode(',', $pkArr).'))';
				if(!$this->conn->query($sql)){
					$this->errorStr = 'ERROR batch checking-in samples: '.$this->conn->error;
					return false;
				}
				return true;
			}
		}
		return false;
	}

	//Shipment and sample edit functions
	public function editShipment($postArr){
		if($this->shipmentPK){
			$sql = 'UPDATE NeonShipment '.
				'SET domainID = "'.$this->cleanInStr($postArr['domainID']).'", '.
				'dateShipped = '.($postArr['dateShipped']?'"'.$this->cleanInStr($postArr['dateShipped']).'"':'NULL').', '.
				'shippedFrom = '.($postArr['shippedFrom']?'"'.$this->cleanInStr($postArr['shippedFrom']).'"':'NULL').', '.
				'senderID = '.($postArr['senderID']?'"'.$this->cleanInStr($postArr['senderID']).'"':'NULL').', '.
				'destinationFacility = '.($postArr['destinationFacility']?'"'.$this->cleanInStr($postArr['destinationFacility']).'"':'NULL').', '.
				'sentToID = '.($postArr['sentToID']?'"'.$this->cleanInStr($postArr['sentToID']).'"':'NULL').', '.
				'shipmentService = '.($postArr['shipmentService']?'"'.$this->cleanInStr($postArr['shipmentService']).'"':'NULL').', '.
				'shipmentMethod = '.($postArr['shipmentMethod']?'"'.$this->cleanInStr($postArr['shipmentMethod']).'"':'NULL').', '.
				'trackingNumber = '.($postArr['trackingNumber']?'"'.$this->cleanInStr($postArr['trackingNumber']).'"':'NULL').', '.
				'notes = '.($postArr['shipmentNotes']?'"'.$this->cleanInStr($postArr['shipmentNotes']).'"':'NULL').', '.
				'modifiedByUid = '.$GLOBALS['SYMB_UID'].', '.
				'modifiedTimestamp = now() '.
				'WHERE (shipmentpk = '.$this->shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR editing shipment: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function resetShipmentCheckin(){
		if($this->shipmentPK){
			$sql = 'UPDATE NeonShipment SET checkinUid = NULL, checkinTimestamp = NULL, receivedDate = NULL, receivedBy = NULL WHERE (shipmentpk = '.$this->shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR resetting shipment check-in: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function shipmentISDeletable(){
		$status = true;
		if($this->shipmentPK){
			$sql = 'SELECT occid FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.') AND (occid IS NOT NULL) LIMIT 1 ';
			$rs = $this->conn->query($sql);
			if($rs->num_rows) $status = false;
			$rs->free();
		}
		return $status;
	}

	public function deleteShipment($shipmentPK){
		if(is_numeric($shipmentPK)){
			$sql = 'DELETE FROM NeonShipment WHERE (shipmentpk = '.$shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR deleting shipment: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function editSample($postArr){
		$status = false;
		$postArr = array_change_key_case($postArr);
		if(is_numeric($postArr['samplepk'])){
			$sampleID = (isset($postArr['sampleid']) && $postArr['sampleid']?$postArr['sampleid']:NULL);
			$sampleCode = (isset($postArr['samplecode']) && $postArr['samplecode']?$postArr['samplecode']:NULL);
			//Get old values
			$occid = '';
			$identifierArr = array();
			$sql = 'SELECT s.occid, i.idomoccuridentifiers, i.identifierName, i.identifierValue FROM NeonSample s LEFT JOIN omoccuridentifiers i ON s.occid = i.occid WHERE s.samplePK = '.$postArr['samplepk'];
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$occid = $r->occid;
				if(strpos($r->identifierName,'sampleID')){
					$identifierArr[$r->occid]['NEON sampleID']['idKey'] = $r->idomoccuridentifiers;
					$identifierArr[$r->occid]['NEON sampleID']['idValue'] = $r->identifierValue;
				}
				elseif(strpos($r->identifierName,'sampleCode')){
					$identifierArr[$r->occid]['NEON sampleCode (barcode)']['idKey'] = $r->idomoccuridentifiers;
					$identifierArr[$r->occid]['NEON sampleCode (barcode)']['idValue'] = $r->identifierValue;
				}
			}
			$rs->free();
			if($occid){
				$identifierArr[$occid]['NEON sampleID']['neonID'] = $sampleID;
				$identifierArr[$occid]['NEON sampleCode (barcode)']['neonID'] = $sampleCode;
			}

			//Update target record
			$sql = 'UPDATE NeonSample
				SET sampleID = ?, alternativeSampleID = ?, sampleCode = ?, sampleClass = ?, quarantineStatus = ?, namedLocation = ?, collectDate = ?, taxonID = ?,
				individualCount = ?, filterVolume = ?, domainRemarks = ?, notes = ? WHERE (samplepk = ?)';
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($sql);
			if($stmt->error==null) {
				$altID = isset($postArr['alternativesampleid'])&&$postArr['alternativesampleid']?$postArr['alternativesampleid']:NULL;
				$sampleClass = $postArr['sampleclass']?$postArr['sampleclass']:NULL;
				$quarStatus = isset($postArr['quarantinestatus'])&&$postArr['quarantinestatus']?$postArr['quarantinestatus']:NULL;
				$namedLoc = isset($postArr['namedlocation'])&&$postArr['namedlocation']?$postArr['namedlocation']:NULL;
				$collDate = isset($postArr['collectdate'])&&$postArr['collectdate']?$postArr['collectdate']:NULL;
				$taxonID = isset($postArr['taxonid'])&&$postArr['taxonid']?$postArr['taxonid']:NULL;
				$indCnt = isset($postArr['individualcount'])&&$postArr['individualcount']?$postArr['individualcount']:NULL;
				$filterVol = isset($postArr['filtervolume'])&&$postArr['filtervolume']?$postArr['filtervolume']:NULL;
				$domainRemarks = isset($postArr['domainremarks'])&&$postArr['domainremarks']?$postArr['domainremarks']:NULL;
				$sampleNotes = isset($postArr['samplenotes'])&&$postArr['samplenotes']?$postArr['samplenotes']:NULL;
				$stmt->bind_param('ssssssssiissi', $sampleID, $altID, $sampleCode, $sampleClass, $quarStatus, $namedLoc, $collDate, $taxonID,
					$indCnt, $filterVol, $domainRemarks, $sampleNotes, $postArr['samplepk']);
				$stmt->execute();
				if($stmt->error==null) $status = true;
				else{
					$this->errormessage = $stmt->error;
					echo $this->errorStr;
				}
			}
			else{
				$this->errorStr = $stmt->error;
				echo $this->errorStr;
			}
			$stmt->close();

			if($status && $occid){
				//An occurrence record exists, thus update sampleID and sampleCode identifiers if they have been changed
				foreach($identifierArr as $occid => $idArr){
					foreach($idArr as $idName => $unitArr){
						$sql = '';
						if($unitArr['neonID'] && (!isset($unitArr['idValue']) || !$unitArr['idValue'])){
							$sql = 'INSERT INTO omoccuridentifiers(occid, identifierName, identifierValue, modifiedUid) VALUES(?,?,?,?)';
							$stmt = $this->conn->stmt_init();
							$stmt->prepare($sql);
							if($stmt->error==null) {
								$stmt->bind_param('issi', $occid, $idName, $unitArr['neonID'], $GLOBALS['SYMB_UID']);
								$stmt->execute();
								if($stmt->error==null) $status = true;
								else{
									$this->errormessage = 'ERROR inserting '.$idName.' identifier: '.$stmt->error;
									echo $this->errorStr;
								}
							}
							$stmt->close();
						}
						elseif(!$unitArr['neonID'] && isset($unitArr['idValue']) && $unitArr['idValue']){
							$sql = 'DELETE FROM omoccuridentifiers WHERE occid = ? AND idomoccuridentifiers = ?';
							$stmt = $this->conn->stmt_init();
							$stmt->prepare($sql);
							if($stmt->error==null) {
								$stmt->bind_param('ii', $occid, $unitArr['idKey']);
								$stmt->execute();
								if($stmt->error==null) $status = true;
								else{
									$this->errormessage = 'ERROR deleting '.$idName.' identifier: '.$stmt->error;
									echo $this->errorStr;
								}
							}
							$stmt->close();
						}
						elseif($unitArr['neonID'] && $unitArr['neonID'] != $unitArr['idValue']){
							//Update $unitArr['idKey']
							$sql = 'UPDATE omoccuridentifiers SET identifierValue = ? WHERE occid = ? AND idomoccuridentifiers = ?';
							$stmt = $this->conn->stmt_init();
							$stmt->prepare($sql);
							if($stmt->error==null) {
								$stmt->bind_param('sii', $unitArr['neonID'], $occid, $unitArr['idKey']);
								$stmt->execute();
								if($stmt->error==null) $status = true;
								else{
									$this->errormessage = 'ERROR updating '.$idName.' identifier: '.$stmt->error;
									echo $this->errorStr;
								}
							}
							$stmt->close();
						}
					}
				}
			}
		}
		return $status;
	}

	public function addSample($recArr, $verbose = false){
		$status = false;
		$recArr = array_change_key_case($recArr);
		if($this->shipmentPK){
			$sampleID = '';
			if(isset($recArr['sampleid']) && $recArr['sampleid']) $sampleID = $recArr['sampleid'];
			$sampleCode = '';
			if(isset($recArr['samplecode']) && $recArr['samplecode']) $sampleCode = $recArr['samplecode'];
			if($sampleID || $sampleCode){
				$insertRecord = true;
				if($this->reloadSampleRecs){
					if($sampleCode || ($sampleID && $recArr['sampleclass'])){
						$sql = 'SELECT samplepk FROM NeonSample WHERE shipmentpk = '.$this->shipmentPK.' AND (';
						if($sampleID) $sql .= '(sampleid = "'.$sampleID.'" AND sampleclass = "'.$recArr['sampleclass'].'")';
						if($sampleID && $sampleCode) $sql .= ' OR ';
						if($sampleCode) $sql .= 'samplecode = "'.$sampleCode.'"';
						$sql .= ')';
						$rs = $this->conn->query($sql);
						if($r = $rs->fetch_object()){
							$recArr['samplepk'] = $r->samplepk;
							$status = $this->editSample($recArr);
							$insertRecord = false;
							if($verbose){
								if(!$status) echo '<li style="margin-left:15px"><span style="color:orange">NOTICE:</span> '.$this->errorStr.'</li>';
							}
						}
						$rs->free();
					}
				}
				if($insertRecord){
					$duplicateArr = $this->duplicateSampleExists($sampleID, $recArr['sampleclass'], $sampleCode);
					if(!$duplicateArr){
						$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, sampleCode, alternativeSampleID, sampleClass, quarantineStatus, namedLocation, collectDate, '.
							'dynamicproperties, symbiotatarget, taxonID, individualCount, filterVolume, domainRemarks, notes) '.
							'VALUES('.$this->shipmentPK.','.
							($sampleID?'"'.$this->cleanInStr($sampleID).'"':'NULL').','.
							($sampleCode?'"'.$this->cleanInStr($sampleCode).'"':'NULL').','.
							(isset($recArr['alternativesampleid']) && $recArr['alternativesampleid']?'"'.$this->cleanInStr($recArr['alternativesampleid']).'"':'NULL').','.
							(isset($recArr['sampleclass']) && $recArr['sampleclass']?'"'.$this->cleanInStr($recArr['sampleclass']).'"':'NULL').','.
							(isset($recArr['quarantinestatus']) && $recArr['quarantinestatus']?'"'.$this->cleanInStr($recArr['quarantinestatus']).'"':'NULL').','.
							(isset($recArr['namedlocation']) && $recArr['namedlocation']?'"'.$this->cleanInStr($recArr['namedlocation']).'"':'NULL').','.
							(isset($recArr['collectdate']) && $recArr['collectdate']?'"'.$this->cleanInStr($this->formatDate($recArr['collectdate'])).'"':'NULL').','.
							(isset($recArr['dynamicproperties']) && $recArr['dynamicproperties']?'"'.$this->cleanInStr($recArr['dynamicproperties']).'"':'NULL').','.
							(isset($recArr['symbiotatarget']) && $recArr['symbiotatarget']?'"'.$this->cleanInStr($recArr['symbiotatarget']).'"':'NULL').','.
							(isset($recArr['taxonid']) && $recArr['taxonid']?'"'.$this->cleanInStr($recArr['taxonid']).'"':'NULL').','.
							(isset($recArr['individualcount']) && $recArr['individualcount']?'"'.$this->cleanInStr($recArr['individualcount']).'"':'NULL').','.
							(isset($recArr['filtervolume']) && $recArr['filtervolume']?'"'.$this->cleanInStr($recArr['filtervolume']).'"':'NULL').','.
							(isset($recArr['domainremarks']) && $recArr['domainremarks']?'"'.$this->cleanInStr($recArr['domainremarks']).'"':'NULL').','.
							(isset($recArr['samplenotes']) && $recArr['samplenotes']?'"'.$this->cleanInStr($recArr['samplenotes']).'"':'NULL').')';
						if($this->conn->query($sql)){
							$status = true;
							if(isset($recArr['checkinsample']) && $recArr['checkinsample']){
								$sqlUpdate = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), sampleReceived = 1, acceptedForAnalysis = 1, sampleCondition = "ok" WHERE (samplePK = '.$this->conn->insert_id.') ';
								if(!$this->conn->query($sqlUpdate)){
									$this->errorStr = 'ERROR checking-in NEON sample(2): '.$this->conn->error;
									$status = false;
								}
							}
						}
						else{
							if($this->conn->errno == 1062){
								$id = $sampleCode;
								if(!$id) $id = $sampleID;
								$this->errorStr = '<span style="color:red">FAILURE:</span> record already in system with duplicate: <a href="manifestviewer.php?quicksearch='.$id.'" target="_blank">'.$id.'</a>';
								if($verbose) echo '<li>'.$this->errorStr.'</li>';
							}
							else{
								$this->errorStr = '<span style="color:red">ERROR</span> adding sample: '.$this->conn->error;
								if($verbose){
									echo '<li style="margin-left:15px">'.$this->errorStr.'</li>';
									//echo '<li style="margin-left:25px">SQL: '.$sql.'</li>';
								}
							}
							$status = false;
						}
					}
					else{
						$errStr = '';
						foreach($duplicateArr as $dupSamplePK => $idArr){
							$link = 'manifestviewer.php?shipmentPK='.$dupSamplePK.'&sampleFilter=displaySamples&quicksearch=';
							if(isset($idArr['sampleID'])) $errStr .= '<a href="'.$link.$idArr['sampleID'].'#samplePanel" target="_blank">'.$idArr['sampleID'].'</a>, ';
							if(isset($idArr['sampleCode'])) $errStr .= '<a href="'.$link.$idArr['sampleCode'].'#samplePanel" target="_blank">'.$idArr['sampleCode'].'</a>, ';
						}
						$this->errorStr = trim($errStr,', ');
						if($verbose) echo '<li><span style="color:red">ERROR,</span> record already in system with duplicate identifiers: '.$this->errorStr.'</li>';
					}
				}
			}
			else{
				$this->errorStr = '<span style="color:red">ERROR:</span> record skipped due to sampleID and sampleCode being NULL (one is required)';
				if($verbose) echo '<li>'.$this->errorStr.'</li>';
			}
		}
		return $status;
	}

	private function duplicateSampleExists($sampleID, $sampleClass, $sampleCode){
		$retArr = array();
		$sqlCond = '';
		if($sampleID && $sampleClass) $sqlCond .= '(sampleID = "'.$this->cleanInStr($sampleID).'" AND sampleClass = "'.$this->cleanInStr($sampleClass).'") ';
		if($sampleCode) $sqlCond .= ($sqlCond?'OR':'').' (sampleCode = "'.$this->cleanInStr($sampleCode).'")';
		$sql = 'SELECT shipmentPK, sampleID, sampleCode FROM NeonSample WHERE (sampleReceived IS NULL) AND ('.$sqlCond.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->sampleID) $retArr[$r->shipmentPK]['sampleID'] = $r->sampleID;
			if($r->sampleCode) $retArr[$r->shipmentPK]['sampleCode'] = $r->sampleCode;
		}
		$rs->free();
		return $retArr;
	}

	private function getNeonApiArr($url){
		$retArr = array();
		if($url){
			$json = @file_get_contents($url);
			if($json){
				$resultArr = json_decode($json,true);
				if(isset($resultArr['data'])){
					$retArr = $resultArr['data'];
				}
				elseif(isset($resultArr['error'])){
					$this->errorStr = 'ERROR thrown accessing NEON API: url ='.$url;
					if(isset($resultArr['error']['status'])) '; '.$this->errorStr .= $resultArr['error']['status'];
					if(isset($resultArr['error']['detail'])) '; '.$this->errorStr .= $resultArr['error']['detail'];
					$retArr = false;
				}
				else{
					$this->errorStr = 'ERROR retrieving NEON data: '.$url;
					$retArr = false;
				}
			}
			else{
				//$this->errorStr = 'ERROR: unable to access NEON API: '.$url;
				$retArr = false;
			}
		}
		return $retArr;
	}

	public function deleteSample($samplePK){
		$status = false;
		if(is_numeric($samplePK)){
			$sql = 'DELETE FROM NeonSample WHERE samplePK = '.$samplePK;
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR deleting sample: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function editSampleCheckin($postArr){
		$status = false;
		if(is_numeric($postArr['samplePK'])){
			$sampleReceived = ($postArr['sampleReceived']?1:0);
			$acceptedForAnalysis = 'NULL';
			if(isset($postArr['acceptedForAnalysis'])) $acceptedForAnalysis = ($postArr['acceptedForAnalysis']?1:0);
			$sql = 'UPDATE NeonSample '.
				'SET sampleReceived = '.$sampleReceived.', acceptedForAnalysis = '.$acceptedForAnalysis.', '.
				'sampleCondition = '.($postArr['sampleCondition']?'"'.$this->cleanInStr($postArr['sampleCondition']).'"':'NULL').', '.
				'checkinRemarks = '.($postArr['checkinRemarks']?'"'.$this->cleanInStr($postArr['checkinRemarks']).'"':'NULL').' '.
				'WHERE (samplepk = '.$postArr['samplePK'].')';
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR editing sample check-in info: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function resetSampleCheckin($samplePK){
		if(is_numeric($samplePK)){
			$sql = 'UPDATE NeonSample SET checkinUid = NULL, checkinTimestamp = NULL, sampleReceived = NULL, acceptedForAnalysis = NULL, sampleCondition = NULL, checkinRemarks = NULL WHERE (samplepk = '.$samplePK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR resetting sample check-in: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	//Shipment and sample search functions
	public function getShipmentList(){
		$retArr = array();
		$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.initialtimestamp '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
			$this->getFilteredWhereSql().
			'ORDER BY s.shipmentID';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->shipmentPK]['id'] = $r->shipmentID;
			$retArr[$r->shipmentPK]['ts'] = $r->initialtimestamp;
		}
		$rs->free();
		return $retArr;
	}

	private function getFilteredWhereSql(){
		$sqlWhere = '';
		if(isset($_REQUEST['shipmentID'])){
			if(isset($_REQUEST['shipmentID']) && $_REQUEST['shipmentID']){
				$sqlWhere .= 'AND (s.shipmentID LIKE "'.$this->cleanInStr($_REQUEST['shipmentID']).'%") ';
				$this->searchArr['shipmentID'] = $_REQUEST['shipmentID'];
			}
			if(isset($_REQUEST['sampleID']) && $_REQUEST['sampleID']){
				$sqlWhere .= 'AND ((m.sampleID LIKE "%'.$this->cleanInStr($_REQUEST['sampleID']).'%") OR (m.alternativeSampleID LIKE "%'.$this->cleanInStr($_REQUEST['sampleID']).'%")) ';
				$this->searchArr['sampleID'] = $_REQUEST['sampleID'];
			}
			if(isset($_REQUEST['sampleCode']) && $_REQUEST['sampleCode']){
				$sqlWhere .= 'AND (m.sampleCode = "'.$this->cleanInStr($_REQUEST['sampleCode']).'") ';
				$this->searchArr['sampleCode'] = $_REQUEST['sampleCode'];
			}
			if(isset($_REQUEST['domainID']) && $_REQUEST['domainID']){
				$sqlWhere .= 'AND (s.domainID = "'.$_REQUEST['domainID'].'") ';
				$this->searchArr['domainID'] = $_REQUEST['domainID'];
			}
			if(isset($_REQUEST['namedLocation']) && $_REQUEST['namedLocation']){
				$sqlWhere .= 'AND ((m.namedLocation LIKE "'.$_REQUEST['namedLocation'].'%") OR (m.sampleID LIKE "'.$_REQUEST['namedLocation'].'%")) ';
				$this->searchArr['namedLocation'] = $_REQUEST['namedLocation'];
			}
			if(isset($_REQUEST['sampleClass']) && $_REQUEST['sampleClass']){
				$sqlWhere .= 'AND (m.sampleClass LIKE "%'.$this->cleanInStr($_REQUEST['sampleClass']).'%") ';
				$this->searchArr['sampleClass'] = $_REQUEST['sampleClass'];
			}
			if(isset($_REQUEST['taxonID']) && $_REQUEST['taxonID']){
				$sqlWhere .= 'AND (m.taxonID = "'.$_REQUEST['taxonID'].'") ';
				$this->searchArr['taxonID'] = $_REQUEST['taxonID'];
			}
			if(isset($_REQUEST['trackingNumber']) && $_REQUEST['trackingNumber']){
				$trackingId = trim($_REQUEST['trackingNumber'],' #');
				$trackingId = preg_replace('/[^a-zA-Z0-9]+/', '', $trackingId);
				$sqlWhere .= 'AND (s.trackingNumber = "'.$trackingId.'") ';
				$this->searchArr['trackingNumber'] = $_REQUEST['trackingNumber'];
			}
			if(isset($_REQUEST['dateShippedStart']) && $_REQUEST['dateShippedStart']){
				$sqlWhere .= 'AND (s.dateShipped > "'.$_REQUEST['dateShippedStart'].'") ';
				$this->searchArr['dateShippedStart'] = $_REQUEST['dateShippedStart'];
			}
			if(isset($_REQUEST['dateShippedEnd']) && $_REQUEST['dateShippedEnd']){
				$sqlWhere .= 'AND (s.dateShipped < "'.$_REQUEST['dateShippedEnd'].'") ';
				$this->searchArr['dateShippedEnd'] = $_REQUEST['dateShippedEnd'];
			}
			if(isset($_REQUEST['dateCheckinStart']) && $_REQUEST['dateCheckinStart']){
				$sqlWhere .= 'AND (m.checkinTimestamp > "'.$_REQUEST['dateCheckinStart'].'") ';
				$this->searchArr['dateCheckinStart'] = $_REQUEST['dateCheckinStart'];
			}
			if(isset($_REQUEST['dateCheckinEnd']) && $_REQUEST['dateCheckinEnd']){
				$sqlWhere .= 'AND (m.checkinTimestamp < "'.$_REQUEST['dateCheckinEnd'].'") ';
				$this->searchArr['dateCheckinEnd'] = $_REQUEST['dateCheckinEnd'];
			}
			/*
			 if(isset($_REQUEST['senderID']) && $_REQUEST['senderID']){
				 $sqlWhere .= 'AND (s.senderID = "'.$_REQUEST['senderID'].'") ';
				$this->searchArr['senderID'] = $_REQUEST['senderID'];
			 }
			 */
			if(isset($_REQUEST['checkinUid']) && $_REQUEST['checkinUid']){
				$sqlWhere .= 'AND ((s.checkinUid = "'.$_REQUEST['checkinUid'].'") OR (m.checkinUid = "'.$_REQUEST['checkinUid'].'")) ';
				$this->searchArr['checkinUid'] = $_REQUEST['checkinUid'];
			}
			if(isset($_REQUEST['importedUid']) && $_REQUEST['importedUid']){
				$sqlWhere .= 'AND ((s.importUid = "'.$_REQUEST['importedUid'].'") OR (s.modifiedByUid = "'.$_REQUEST['importedUid'].'")) ';
				$this->searchArr['importedUid'] = $_REQUEST['importedUid'];
			}
			/*
			 if(isset($_REQUEST['collectDateStart']) && $_REQUEST['collectDateStart']){
				 $sqlWhere .= 'AND (m.collectDate > "'.$_REQUEST['collectDateStart'].'") ';
				 $this->searchArr['collectDateStart'] = $_REQUEST['collectDateStart'];
			 }
			 if(isset($_REQUEST['collectDateEnd']) && $_REQUEST['collectDateEnd']){
				 $sqlWhere .= 'AND (m.collectDate < "'.$_REQUEST['collectDateEnd'].'") ';
 				 $this->searchArr['collectDateEnd'] = $_REQUEST['collectDateEnd'];
			 }
			 */
			if(isset($_REQUEST['sampleCondition']) && $_REQUEST['sampleCondition']){
				$sqlWhere .= 'AND (m.sampleCondition = "'.$_REQUEST['sampleCondition'].'") ';
				$this->searchArr['sampleCondition'] = $_REQUEST['sampleCondition'];
			}
			if(isset($_REQUEST['manifestStatus'])){
				$statusArr = $_REQUEST['manifestStatus'];
				if(in_array('shipCheck', $statusArr, true)){
					$sqlWhere .= 'AND (s.checkinTimestamp IS NOT NULL) ';
				}
				if(in_array('shipNotCheck', $statusArr, true)){
					$sqlWhere .= 'AND (s.checkinTimestamp IS NULL) ';
				}
				if(in_array('receiptNotSubmitted', $statusArr, true)){
					$sqlWhere .= 'AND (s.receiptstatus IS NULL OR s.receiptstatus NOT LIKE "submitted%") ';
				}
				if(in_array('sampleCheck', $statusArr, true)){
					$sqlWhere .= 'AND (m.checkinTimestamp IS NOT NULL) ';
				}
				if(in_array('sampleNotCheck', $statusArr, true)){
					$sqlWhere .= 'AND (m.checkinTimestamp IS NULL) ';
				}
				if(in_array('notReceivedSamples', $statusArr, true)){
					$sqlWhere .= 'AND (m.sampleReceived = 0) ';
				}
				if(in_array('notAcceptedSamples', $statusArr, true)){
					$sqlWhere .= 'AND (m.acceptedForAnalysis = 0) ';
				}
				if(in_array('occurNotHarvested', $statusArr, true)){
					$sqlWhere .= 'AND (m.occid IS NULL) ';
				}
				$this->searchArr['manifestStatus'] = $_REQUEST['manifestStatus'];
			}
			if($sqlWhere) $sqlWhere = 'WHERE '.subStr($sqlWhere, 3);
		}
		elseif($this->shipmentPK){
			$sqlWhere = 'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
		}
		// echo 'where: '.$sqlWhere; exit;
		return $sqlWhere;
	}

	//Export functions
	public function exportShipmentReceipt(){
		$this->setShipmentArr();
		$fileName = 'receipt_'.$this->shipmentArr['shipmentID'].'_'.date('Y-m-d').'.csv';
		$sql = 'SELECT n.shipmentID, DATE_FORMAT(s.checkinTimestamp,"%Y%m%d") AS shipmentReceivedDate, u.email AS receivedBy, s.sampleID, s.sampleCode, s.sampleClass, '.
			'IF(s.sampleReceived IS NULL,"",IF(s.sampleReceived = 0,"N","Y")) AS sampleReceived, '.
			'IF(s.acceptedForAnalysis IS NULL,"",IF(s.acceptedForAnalysis = 0,"N","Y")) AS acceptedForAnalysis, '.
			'IF(s.acceptedForAnalysis = 0,s.sampleCondition,"") AS sampleCondition, "" AS unknownSamples, CONCAT_WS("; ",s.checkinRemarks,CONCAT("deprecatedSampleID: ",s.alternativeSampleID)) AS remarks '.
			'FROM NeonShipment n INNER JOIN NeonSample s ON n.shipmentPK = s.shipmentPK '.
			'LEFT JOIN users u ON s.checkinUid = u.uid '.
			'WHERE (s.shipmentPK = '.$this->shipmentPK.') AND (s.sampleCondition != "OPAL Sample" OR s.sampleCondition IS NULL) ';
		$this->exportData($fileName, $sql);
		$this->setReceiptStatus(1,true);
	}

	public function exportShipmentList(){
		$fileName = 'shipmentExport_'.date('Y-m-d').'.csv';
		$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.shippedFrom, s.senderID, s.destinationFacility, s.sentToID, s.shipmentService, '.
			's.shipmentMethod, s.trackingNumber, s.receivedDate, s.receivedBy, s.receiptstatus, s.receiptTimestamp, s.notes, CONCAT_WS("; ",u1.lastname, u1.firstname) AS importUser, '.
			'CONCAT_WS("; ",u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, CONCAT_WS("; ",u3.lastname, u3.firstname) AS modifiedByUser, s.modifiedTimestamp, s.initialtimestamp '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
			'LEFT JOIN users u1 ON s.importUid = u1.uid '.
			'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
			'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid ';
		if(isset($_REQUEST['manifestStatus']) && $_REQUEST['manifestStatus'] == 'notAcceptedSamples'){
			$sql .= 'LEFT JOIN omoccurrences o ON m.occid = o.occid ';
		}
		$sql .= $this->getFilteredWhereSql();
		$this->exportData($fileName, $sql);
	}

	public function exportSampleList(){
		$fileName = 'sampleExport_';
		if($this->shipmentPK) $fileName .= $this->shipmentPK.'_';
		$fileName .= date('Y-m-d').'.csv';
		$sql = 'SELECT s.shipmentID, m.samplePK, m.sampleID, m.alternativeSampleID, m.sampleCode, m.sampleClass, m.taxonID, m.individualCount, m.filterVolume, m.namedlocation, '.
			'm.domainremarks, m.collectdate, m.quarantineStatus, m.sampleReceived, m.acceptedForAnalysis, m.sampleCondition, m.dynamicProperties, m.symbiotaTarget, m.notes, m.occid, '.
			'CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, m.checkinTimestamp, m.initialtimestamp '.
			'FROM NeonShipment s INNER JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
			'LEFT JOIN users u ON m.checkinUid = u.uid ';
		$sql .= $this->getFilteredWhereSql();
		$this->exportData($fileName, $sql);
	}

	public function exportOccurrenceList(){
		$fileName = 'occurrenceExport_';
		if($this->shipmentPK) $fileName .= $this->shipmentPK.'_';
		$fileName .= date('Y-m-d').'.csv';
		$sql = 'SELECT m.samplePK, m.sampleID, m.alternativeSampleID, m.sampleCode, m.sampleClass, m.taxonID, m.individualCount, m.filterVolume, m.namedlocation, '.
			'm.domainremarks, m.collectdate, m.quarantineStatus, m.sampleReceived, m.acceptedForAnalysis, m.sampleCondition, m.dynamicProperties, m.symbiotaTarget, '.
			'm.notes, m.occid, CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, m.checkinTimestamp, m.initialtimestamp, '.
			'o.catalogNumber, o.sciname, o.scientificNameAuthorship, o.identifiedBy, o.dateIdentified, o.recordedBy, o.recordNumber, o.eventDate, '.
			'o.country, o.stateProvince, o.county, o.locality, o.decimalLatitude, o.decimalLongitude, o.coordinateUncertaintyInMeters, o.minimumElevationInMeters, '.
			'o.habitat, o.dateEntered, o.dateLastModified '.
			'FROM NeonShipment s INNER JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
			'INNER JOIN omoccurrences o ON m.occid = o.occid '.
			'LEFT JOIN users u ON m.checkinUid = u.uid ';
		$sql .= $this->getFilteredWhereSql();
		$this->exportData($fileName, $sql);
	}

	private function exportData($fileName, $sql){
		//echo "<div>".$sql."</div>"; exit;
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$outstream = fopen("php://output", "w");
		$dataConn = MySQLiConnectionFactory::getCon('readonly');
		if($rs = $dataConn->query($sql,MYSQLI_USE_RESULT)){
			$outHeader = true;
			while($r = $rs->fetch_assoc()){
				if($outHeader){
					fputcsv($outstream,array_keys($r));
					$outHeader = false;
				}
				fputcsv($outstream,$r);
			}
			$rs->free();
		}
		else{
			echo 'ERROR generating recordset';
		}
		fclose($outstream);
		$dataConn->close();
		exit;
	}

	//Various data return functions
	public function getCollectionArr(){
		$retArr = array();
		if($this->shipmentPK){
			$sql = 'SELECT DISTINCT c.collid, CONCAT(c.collectionname,CONCAT_WS("-",c.institutionCode, c.collectionCode)) as collName '.
				'FROM NeonSample s INNER JOIN omoccurrences o ON s.occid = o.occid '.
				'INNER JOIN omcollections c ON o.collid = c.collid '.
				'WHERE s.shipmentPK = '.$this->shipmentPK;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->collid] = $r->collName;
			}
		}
		asort($retArr);
		return $retArr;
	}

	public function getImportUserArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT uid, username '.
			'FROM (SELECT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username '.
			'FROM users u INNER JOIN NeonShipment s ON u.uid = s.importUid '.
			'union SELECT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username '.
			'FROM users u INNER JOIN NeonShipment s ON u.uid = s.modifiedbyUid) u';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
	}

	public function getCheckinUserArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username FROM users u INNER JOIN NeonSample s ON u.uid = s.checkinUid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
	}

	public function getTrackingStr(){
		$retStr = '';
		$trackingArr = explode(';',$this->shipmentArr['trackingNumber']);
		$trackingArr = array_unique($trackingArr);
		foreach($trackingArr as $trackingStr){
			$trackingId = preg_replace('/[^a-zA-Z0-9;]+/', '', $trackingStr);
			$shipService = strtolower($this->shipmentArr['shipmentService']);
			if($shipService == 'fedex' && strlen($trackingId) == 12){
				$retStr .= '<a href="https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers='.$trackingId.'&locale=en_US&cntry_code=us" target="_blank">';
			}
			elseif($shipService == 'ups' && strlen($trackingId) == 18){
				$retStr .= '<a href="https://www.ups.com/track?loc=en_US&tracknum='.$trackingId.'&requester=WT/trackdetails" target="_blank">';
			}
			elseif($shipService == 'usps' && strlen($trackingId) == 22){
				$retStr .= '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels='.$trackingId.'" target="_blank">';
			}
			$retStr .= $trackingId.'</a>; ';
		}
		return trim($retStr,' ;');
	}

	public function getContentPath($pathType='root'){
		$contentPath = $GLOBALS['SERVER_ROOT'];
		if($pathType == 'url') $contentPath = $GLOBALS['CLIENT_ROOT'];
		if(substr($contentPath,-1) != '/') $contentPath .= '/';
		$contentPath .= 'neon/content/manifests/';
		return $contentPath;
	}

	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})(\d{2})(\d{2})\D+/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(20\d{2})-(\d{2})-(\d{2})\D*/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(20\d{2})/', $dateStr, $m)){
			$month = $m[1];
			if(strlen($month) == 1) $month = '0'.$month;
			$day = $m[2];
			if(strlen($day) == 1) $day = '0'.$day;
			$dateStr = $m[3].'-'.$month.'-'.$day;
		}
		return $dateStr;
	}

	public function getConditionArr(){
		//Removed from array on 2019-10-29 by request of NEON: 'ok'=>'OK - No Known Compromise',
		$condArr = array('ok'=>'OK - No Known Compromise', 'cold chain broken'=>'Cold Chain Broken', 'damaged'=>'Damaged - Analysis Affected',
			'sample incomplete'=>'Sample Incomplete','handling error'=>'Handling Error', 'other'=>'Other - Described in Remarks','opal sample'=>'OPAL Sample');
		return $condArr;
	}

	public function getConditionAppliedArr(){
		$queryArr = array();
		$sql = 'SELECT DISTINCT sampleCondition FROM NeonSample ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$queryArr[$r->sampleCondition] = $r->sampleCondition;
		}
		$rs->free();
		return array_intersect_key($this->getConditionArr(), $queryArr);
	}

	private function getDomainTitle($domainID){
		$domainID = trim($domainID);
		$domainArr = array("D01"=>"Northeast", "D02"=>"Mid-Atlantic", "D03"=>"Southeast", "D04"=>"Atlantic Neotropical", "D05"=>"Great Lakes", "D06"=>"Prairie Peninsula",
			"D07"=>"Appalachians & Cumberland Plateau", "D08"=>"Ozarks Complex", "D08"=>"Ozarks Complex", "D09"=>"Northern Plains", "D10"=>"Central Plains", "D11"=>"Southern Plains",
			"D13"=>"Southern Rockies & Colorado Plateau", "D14"=>"Desert Southwest", "D15"=>"Great Basin", "D16"=>"Pacific Northwest", "D17"=>"Pacific Southwest", "D18"=>"Tundra",
			"D19"=>"Taiga", "D20"=>"Pacific Tropical");
		return (array_key_exists($domainID, $domainArr)?$domainArr[$domainID]:'');
	}

	private function getSiteTitleArr(){
		$siteArr = array("ABBY"=>"Abby Road", "BARR"=>"Barrow Environmental Observatory", "BART"=>"Bartlett Experimental Forest", "BLAN"=>"Blandy Experimental Farm",
			"BONA"=>"Caribou Creek - Poker Flats Watershed", "CLBJ"=>"LBJ National Grassland", "CPER"=>"Central Plains Experimental Range", "DCFS"=>"Dakota Coteau Field School",
			"DEJU"=>"Delta Junction", "DELA"=>"Dead Lake", "DSNY"=>"Disney Wilderness Preserve", "GRSM"=>"Great Smoky Mountains National Park, Twin Creeks", "GUAN"=>"Guanica Forest",
			"HARV"=>"Harvard Forest", "HEAL"=>"Healy", "JERC"=>"Jones Ecological Research Center", "JORN"=>"Jornada LTER", "KONA"=>"Konza Prairie Biological Station",
			"KONZ"=>"Konza Prairie Biological Station", "LAJA"=>"Lajas Experimental Station", "LENO"=>"Lenoir Landing", "MLBS"=>"Mountain Lake Biological Station", "MOAB"=>"Moab",
			"NIWO"=>"Niwot Ridge Mountain Research Station", "NOGP"=>"Northern Great Plains Research Laboratory", "OAES"=>"Klemme Range Research Station", "ONAQ"=>"Onaqui-Ault",
			"ORNL"=>"Oak Ridge", "OSBS"=>"Ordway-Swisher Biological Station", "PUUM"=>"Pu'u Maka'ala Natural Area Reserve", "RMNP"=>"Rocky Mountain National Park, CASTNET",
			"SCBI"=>"Smithsonian Conservation Biology Institute", "SERC"=>"Smithsonian Environmental Research Center", "SJER"=>"San Joaquin", "SOAP"=>"Soaproot Saddle",
			"SRER"=>"Santa Rita Experimental Range", "STEI"=>"Steigerwaldt Land Services", "STER"=>"North Sterling, CO", "TALL"=>"Talladega National Forest", "TEAK"=>"Lower Teakettle",
			"TOOL"=>"Toolik Lake", "TREE"=>"Treehaven", "UKFS"=>"The University of Kansas Field Station", "UNDE"=>"UNDERC", "WOOD"=>"Woodworth", "WREF"=>"Wind River Experimental Forest");
		return $siteArr;
	}

	public function confirmCollectionTransfer($samplePK, $classNew){
		$retArr = array('code' => 0);
		if(is_numeric($samplePK)){
			$collid = 0;
			$sql = 'SELECT c.collid, o.occid '.
				'FROM NeonSample s INNER JOIN omoccurrences o ON s.occid = o.occid '.
				'INNER JOIN omcollections c ON o.collid = c.collid '.
				'WHERE s.samplePK = '.$samplePK;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$collid = $r->collid;
				$retArr['occid'] = $r->occid;
			}
			$rs->free();
			if($collid){
				$sql = 'SELECT collid, CONCAT_WS("-",institutioncode,collectioncode) as collcode FROM omcollections WHERE datasetid LIKE "%'.$this->cleanInStr($classNew).'%"';
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()){
					if($collid != $r->collid){
						$retArr['code'] = 1;
						$retArr['collCode'] = $r->collcode;
						$retArr['targetCollid'] = $r->collid;
					}
				}
				else{
					$retArr['code'] = 2;
				}
				$rs->free();
			}
		}
		return json_encode($retArr);
	}

	public function transferOccurrence($occid,$targetCollid){
		$retCode = 0;
		if(is_numeric($occid) && is_numeric($targetCollid)){
			$attrStr = '';
			$sql = 'SELECT datasetname FROM omcollections WHERE collid = '.$targetCollid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$attrStr = $r->datasetname;
			}
			$rs->free();
			$sql2 = 'UPDATE omoccurrences SET collid = '.$targetCollid.', verbatimAttributes = '.($attrStr?'"'.$attrStr.'"':NULL).' WHERE occid = '.$occid;
			if($this->conn->query($sql2)){
				$retCode = 1;
			}
		}
		return $retCode;
	}

	//Setters and getters
	public function setShipmentPK($id){
		if(is_numeric($id)) $this->shipmentPK = $id;
	}

	public function setQuickSearchTerm($term){
		$cleanTerm = $this->cleanInStr($term);
		$sql = 'SELECT s.shipmentPK '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentPK = m.shipmentPK '.
			'WHERE (s.shipmentid LIKE "'.$cleanTerm.'%" OR m.sampleid = "'.$cleanTerm.'" OR m.alternativeSampleID = "'.$cleanTerm.'" OR m.sampleCode = "'.$cleanTerm.'")';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->shipmentPK = $r->shipmentPK;
		}
		$rs->free();
		return $this->shipmentPK;
	}

	public function setUploadFileName($name){
		$this->uploadFileName = $name;
	}

	public function getUploadFileName(){
		return $this->uploadFileName;
	}

	public function setReloadSampleRecs($cond){
		if($cond) $this->reloadSampleRecs = true;
		else $this->reloadSampleRecs = false;
	}

	public function setFieldMap($fieldMap){
		$this->fieldMap = $fieldMap;
	}

	public function getSourceArr(){
		return $this->sourceArr;
	}

	public function getTargetArr(){
		$retArr = array('shipmentID','domainID','dateShipped','shippedFrom','senderID','destinationFacility','sentToID','shipmentService','shipmentMethod','trackingNumber','shipmentNotes',
			'sampleID','alternativeSampleId','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate','quarantineStatus','dynamicProperties');
		sort($retArr);
		return $retArr;
	}

	public function getSymbTargetArr(){
		$retArr = array('catalogNumber','occurrenceID','family','sciname','identifiedby','dateIdentified','identificationRemarks','recordedBy','recordNumber','eventDate','habitat','occurrenceRemarks',
			'verbatimAttributes','behavior','establishmentMeans','lifeStage','sex','individualCount','preparations','country','stateProvince',
			'county','locality','decimalLatitude','decimalLongitude','coordinateUncertaintyInMeters','verbatimCoordinates','minimumElevationInMeters');
		return $retArr;
	}

	public function getSearchArr(){
		return $this->searchArr;
	}

	public function getSearchArgumentStr(){
		$retStr = '';
		if($this->searchArr){
			foreach($this->searchArr as $k => $v){
        if(is_array($v)){
          $v = implode(',',$v);
        }
				$retStr .= '&'.$k.'='.$v;
			}
		}
		return $retStr;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	public function in_iarray($needle, $haystack) {
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

	public function array_key_iexists($key, $array) {
		return array_key_exists(strtolower($key), array_map('strtolower', $array));
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>