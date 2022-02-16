<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMaintenance.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');

class OccurrenceCollectionProfile extends Manager {

	private $collid;
	private $collMeta;
	private $organizationKey;
	private $installationKey;
	private $datasetKey;
	private $endpointKey;
	private $idigbioKey;
	private $materialSampleIsActive = false;

	public function __construct($connType = 'readonly'){
		parent::__construct(null,$connType);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function setCollid($collid){
		if(is_numeric($collid)){
			$this->collid = $collid;
			return true;
		}
		return false;
	}

	public function getCollectionMetadata(){
		if(!$this->collMeta) $this->setCollectionMeta();
		return $this->collMeta;
	}

	private function setCollectionMeta(){
		$sql = 'SELECT c.collid, c.institutioncode, c.CollectionCode, c.CollectionName, c.collectionid, c.FullDescription, c.resourceJson, c.contactJson, c.individualurl, '.
			'c.latitudedecimal, c.longitudedecimal, c.icon, c.colltype, c.managementtype, c.publicedits, c.guidtarget, c.rights, c.rightsholder, c.accessrights, '.
			'c.dwcaurl, c.sortseq, c.securitykey AS skey, c.collectionguid AS recordid, c.publishtogbif, c.publishtoidigbio, c.aggkeysstr, c.dynamicProperties, s.uploaddate '.
			'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid ';
		if($this->collid) $sql .= 'WHERE (c.collid = '.$this->collid.') ';
		else $sql .= 'WHERE s.recordcnt > 0 ORDER BY c.SortSeq, c.CollectionName';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			foreach($r as $k => $v){
				if($k != 'dynamicProperties') $this->collMeta[$r['collid']][strtolower($k)] = $v;
			}
			if($r['dynamicProperties'] && strpos($r['dynamicProperties'],'matSample":{"status":1')){
				$this->collMeta[$r['collid']]['matSample'] = 1;
				$this->materialSampleIsActive = true;
			}
			$uDate = '';
			if($r['uploaddate']){
				$uDate = $r['uploaddate'];
				$month = substr($uDate,5,2);
				$day = substr($uDate,8,2);
				$year = substr($uDate,0,4);
				$uDate = date("j F Y",mktime(0,0,0,$month,$day,$year));
			}
			$this->collMeta[$r['collid']]['uploaddate'] = $uDate;
		}
		$rs->free();
		if($this->collid){
			//Check to make sure Security Key and collection GUIDs exist
			if(!$this->collMeta[$this->collid]['recordid']){
				$guid= UuidFactory::getUuidV4();
				$this->collMeta[$this->collid]['recordid'] = $guid;
				$conn = MySQLiConnectionFactory::getCon('write');
				$sql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" WHERE collectionguid IS NULL AND collid = '.$this->collid;
				$conn->query($sql);
			}
			if(!$this->collMeta[$this->collid]['skey']){
				$guid2 = UuidFactory::getUuidV4();
				$this->collMeta[$this->collid]['skey'] = $guid2;
				$conn = MySQLiConnectionFactory::getCon('write');
				$sql = 'UPDATE omcollections SET securitykey = "'.$guid2.'" WHERE securitykey IS NULL AND collid = '.$this->collid;
				$conn->query($sql);
			}
			if(isset($this->collMeta[$this->collid]['aggkeysstr']) && $this->collMeta[$this->collid]['aggkeysstr']){
				$this->setAggKeys(json_decode($this->collMeta[$this->collid]['aggkeysstr'],true));
			}
		}
	}

	public function getCollectionCategories(){
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT c.ccpk, c.category FROM omcollcatlink l INNER JOIN omcollcategories c ON l.ccpk = c.ccpk WHERE (l.collid = '.$this->collid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->ccpk] = $r->ccpk;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getMetadataHtml($LANG, $LANG_TAG){
		$outStr = '<div class="coll-description">'.$this->collMeta[$this->collid]["fulldescription"].'</div>';
		if(isset($this->collMeta[$this->collid]['contactjson'])){
			if($contactArr = json_decode($this->collMeta[$this->collid]['contactjson'],true)){
				foreach($contactArr as $cArr){
					$title = (isset($LANG['CONTACT'])?$LANG['CONTACT']:'Contact');
					if(isset($cArr['role']) && $cArr['role']) $title = $cArr['role'];
					$outStr .= '<div class="field-div"><span class="label">'.$title.':</span> ';
					$outStr .= $cArr['firstName'].' '.$cArr['lastName'];
					if(isset($cArr['email']) && $cArr['email']) $outStr .= ', '.$cArr['email'];
					if(isset($cArr['phone']) && $cArr['phone']) $outStr .= ', '.$cArr['phone'];
					if(isset($cArr['orcid']) && $cArr['orcid']) $outStr .= ' (ORCID #: <a href="https://orcid.org/'.$cArr['orcid'].'" target="_blank">'.$cArr['orcid'].'</a>)';
					$outStr .= '</div>';
				}
			}
		}
		if(isset($this->collMeta[$this->collid]['resourcejson'])){
			if($resourceArr = json_decode($this->collMeta[$this->collid]['resourcejson'],true)){
				$title = (isset($LANG['HOMEPAGE'])?$LANG['HOMEPAGE']:'Homepage');
				foreach($resourceArr as $rArr){
					if(isset($rArr['title'][$LANG_TAG]) && $rArr['title'][$LANG_TAG]) $title = $rArr['title'][$LANG_TAG];
					$outStr .= '<div class="field-div"><span class="label">'.$title.':</span> ';
					$outStr .= '<a href="'.$rArr['url'].'" target="_blank">'.$rArr['url'].'</a>';
					$outStr .= '</div>';
				}
			}
		}
		$outStr .= '<div class="field-div">';
		$outStr .= '<span class="label">'.$LANG['COLLECTION_TYPE'].':</span> '.$this->collMeta[$this->collid]['colltype'];
		$outStr .= '</div>';
		$outStr .= '<div class="field-div">';
		$outStr .= '<span class="label">'.$LANG['MANAGEMENT'].':</span> ';
		if($this->collMeta[$this->collid]['managementtype'] == 'Live Data'){
			$outStr .= (isset($LANG['LIVE_DATA'])?$LANG['LIVE_DATA']:'Live Data managed directly within data portal');
		}
		else{
			if($this->collMeta[$this->collid]['managementtype'] == 'Aggregate'){
				$outStr .= (isset($LANG['DATA_AGGREGATE'])?$LANG['DATA_AGGREGATE']:'Data harvested from a data aggregator');
			}
			else{
				$outStr .= (isset($LANG['DATA_SNAPSHOT'])?$LANG['DATA_SNAPSHOT']:'Data snapshot of local collection database ');
			}
		}
		$outStr .= '</div>';
		if($this->collMeta[$this->collid]['managementtype'] != 'Live Data') $outStr .= '<div class="field-div"><span class="label">'.$LANG['LAST_UPDATE'].':</span> '.$this->collMeta[$this->collid]['uploaddate'].'</div>';
		if($this->collMeta[$this->collid]['managementtype'] == 'Live Data'){
			$outStr .= '<div class="field-div">';
			$outStr .= '<span class="label">'.$LANG['GLOBAL_UNIQUE_ID'].':</span> '.$this->collMeta[$this->collid]['recordid'];
			$outStr .= '</div>';
		}
		if($this->collMeta[$this->collid]['dwcaurl']){
			$dwcaUrl = $this->collMeta[$this->collid]['dwcaurl'];
			$outStr .= '<div class="field-div">';
			$outStr .= '<span class="label">'.(isset($LANG['DWCA_PUB'])?$LANG['DWCA_PUB']:'DwC-Archive Access Point').':</span> ';
			$outStr .= '<a href="'.$dwcaUrl.'">'.$dwcaUrl.'</a>';
			$outStr .= '</div>';
		}
		$outStr .= '<div class="field-div">';
		if($this->collMeta[$this->collid]['managementtype'] == 'Live Data'){
			if($GLOBALS['SYMB_UID']){
				$outStr .= '<span class="label">'.(isset($LANG['LIVE_DOWNLOAD'])?$LANG['LIVE_DOWNLOAD']:'Live Data Download').':</span> ';
				$outStr .= '<a href="../../webservices/dwc/dwcapubhandler.php?collid='.$this->collMeta[$this->collid]['collid'].'">'.(isset($LANG['FULL_DATA'])?$LANG['FULL_DATA']:'DwC-Archive File').'</a>';
			}
		}
		elseif($this->collMeta[$this->collid]['managementtype'] == 'Snapshot'){
			$pathArr = $this->getDwcaPath($this->collMeta[$this->collid]['collid']);
			if($pathArr){
				$outStr .= '<div style="float:left"><span class="label">'.(isset($LANG['IPT_SOURCE'])?$LANG['IPT_SOURCE']:'IPT / DwC-A Source').':</span> </div>';
				$outStr .= '<div style="float:left;margin-left:5px;">';
				foreach($pathArr as $titleStr => $pathStr){
					$outStr .= '<a href="'.$pathStr.'" target="_blank">'.$titleStr.'</a><br/>';
				}
				$outStr .= '</div>';
			}
		}
		$outStr .= '</div>';
		$outStr .= '<div class="field-div"><span class="label">'.(isset($LANG['DIGITAL_METADATA'])?$LANG['DIGITAL_METADATA']:'Digital Metadata').':</span> <a href="../datasets/emlhandler.php?collid='.$this->collMeta[$this->collid]['collid'].'" target="_blank">EML File</a></div>';
		$outStr .= '<div class="field-div"><span class="label">'.$LANG['USAGE_RIGHTS'].':</span> ';
		if($this->collMeta[$this->collid]['rights']){
			$rights = $this->collMeta[$this->collid]['rights'];
			$rightsUrl = '';
			if(substr($rights,0,4) == 'http'){
				$rightsUrl = $rights;
				if($GLOBALS['RIGHTS_TERMS']){
					if($rightsArr = array_keys($GLOBALS['RIGHTS_TERMS'],$rights)){
						$rights = current($rightsArr);
					}
				}
			}
			if($rightsUrl) $outStr .= '<a href="'.$rightsUrl.'" target="_blank">';
			$outStr .= $rights;
			if($rightsUrl) $outStr .= '</a>';
		}
		elseif(file_exists('../../includes/usagepolicy.php')){
			$outStr .= '<a href="../../includes/usagepolicy.php" target="_blank">'.(isset($LANG['USAGE_POLICY'])?$LANG['USAGE_POLICY']:'Usage policy').'</a>';
		}
		$outStr .= '</div>';
		if($this->collMeta[$this->collid]['rightsholder']){
			$outStr .= '<div class="field-div">';
			$outStr .= '<span class="label">'.$LANG['RIGHTS_HOLDER'].':</span> ';
			$outStr .= $this->collMeta[$this->collid]['rightsholder'];
			$outStr .= '</div>';
		}
		if($this->collMeta[$this->collid]['accessrights']){
			$outStr .= '<div class="field-div">'.
				'<span class="label">'.$LANG['ACCESS_RIGHTS'].':</span> '.
				$this->collMeta[$this->collid]['accessrights'].
				'</div>';
		}
		return $outStr;
	}

	private function getDwcaPath($collid){
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT uspid, title, path FROM uploadspecparameters WHERE (collid = '.$collid.') AND (uploadtype = 8)';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if(trim($r->path)){
					$retArr[$r->title] = str_replace('/archive.do', '/resource.do', $r->path);
				}
			}
			$rs->free();
		}
		return $retArr;
	}

	//Collection metadata editing functions
	public function submitCollEdits($postArr){
		if($this->collid){
			$icon = $this->cleanInStr($postArr['iconurl']);
			if(isset($_FILES['iconfile']['name']) && $_FILES['iconfile']['name']) $icon = $this->addIconImageFile();
			$sql = 'UPDATE omcollections '.
				'SET institutioncode = "'.$this->cleanInStr($postArr['institutioncode']).'",'.
				'collectioncode = '.($postArr['collectioncode']?'"'.$this->cleanInStr($postArr['collectioncode']).'"':'NULL').','.
				'collectionname = "'.$this->cleanInStr($postArr['collectionname']).'",'.
				'collectionid = '.($postArr['collectionid']?'"'.$this->cleanInStr($postArr['collectionid']).'"':'NULL').','.
				'fulldescription = '.($postArr['fulldescription']?'"'.$this->cleanInStr($postArr['fulldescription']).'"':'NULL').','.
				'latitudedecimal = '.(is_numeric($postArr['latitudedecimal'])?$postArr['latitudedecimal']:'NULL').','.
				'longitudedecimal = '.(is_numeric($postArr['longitudedecimal'])?$postArr['longitudedecimal']:'NULL').','.
				'publishToGbif = '.(array_key_exists('publishToGbif',$postArr)&&is_numeric($postArr['publishToGbif'])?1:0).','.
				'publishToIdigbio = '.(array_key_exists('publishToIdigbio',$postArr)&&is_numeric($postArr['publishToIdigbio'])?1:0).','.
				'publicedits = '.(array_key_exists('publicedits',$postArr)&&is_numeric($postArr['publicedits'])?1:0).','.
				'guidtarget = '.(array_key_exists('guidtarget',$postArr)?'"'.$this->cleanInStr($postArr['guidtarget']).'"':'NULL').','.
				'rights = '.($postArr['rights']?'"'.$this->cleanInStr($postArr['rights']).'"':'NULL').','.
				'rightsholder = '.($postArr['rightsholder']?'"'.$this->cleanInStr($postArr['rightsholder']).'"':'NULL').','.
				'accessrights = '.($postArr['accessrights']?'"'.$this->cleanInStr($postArr['accessrights']).'"':'NULL').','.
				'icon = '.($icon?'"'.$icon.'"':'NULL').','.
				(isset($postArr['managementtype'])&&$postArr['managementtype']?'managementtype = "'.$this->cleanInStr($postArr['managementtype']).'",':'').
				(isset($postArr['colltype'])&&$postArr['colltype']?'colltype = "'.$this->cleanInStr($postArr['colltype']).'",':'').
				(isset($postArr['sortseq'])&&is_numeric($postArr['sortseq'])?'sortseq = '.$this->cleanInStr($postArr['sortseq']).',':'').
				'individualurl = '.($postArr['individualurl']?'"'.$this->cleanInStr($postArr['individualurl']).'"':'NULL').' '.
				'WHERE (collid = '.$this->collid.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				return 'ERROR updating collection: '.$this->conn->error;
			}

			//Modify collection category, if needed
			if(isset($postArr['ccpk']) && $postArr['ccpk']){
				$rs = $this->conn->query('SELECT ccpk FROM omcollcatlink WHERE collid = '.$this->collid);
				if($r = $rs->fetch_object()){
					if($r->ccpk <> $postArr['ccpk']){
						if(!$this->conn->query('UPDATE omcollcatlink SET ccpk = '.$postArr['ccpk'].' WHERE ccpk = '.$r->ccpk.' AND collid = '.$this->collid)){
							return 'ERROR updating collection category link: '.$this->conn->error;
						}
					}
				}
				else{
					if(!$this->conn->query('INSERT INTO omcollcatlink (ccpk,collid) VALUES('.$postArr['ccpk'].','.$this->collid.')')){
						return 'ERROR inserting collection category link(1): '.$this->conn->error;
					}
				}
			}
			else{
				$this->conn->query('DELETE FROM omcollcatlink WHERE collid = '.$this->collid);
			}
		}
		return true;
	}

	public function submitCollAdd($postArr){
		$icon = array_key_exists('iconurl',$postArr)?$this->cleanInStr($postArr['iconurl']):'';
		if(isset($_FILES['iconfile']['name']) && $_FILES['iconfile']['name']) $icon = $this->addIconImageFile();
		$sql = 'INSERT INTO omcollections(institutioncode,collectioncode,collectionname,collectionid,fulldescription,latitudedecimal,longitudedecimal,'.
			'publicedits,publishToGbif,publishToIdigbio,guidtarget,rights,rightsholder,accessrights,icon,managementtype,colltype,collectionguid,individualurl,sortseq) '.
			'VALUES ("'.$this->cleanInStr($postArr['institutioncode']).'",'.
			($postArr['collectioncode']?'"'.$this->cleanInStr($postArr['collectioncode']).'"':'NULL').',"'.
			$this->cleanInStr($postArr['collectionname']).'",'.
			($postArr['collectionid']?'"'.$this->cleanInStr($postArr['collectionid']).'"':'NULL').','.
			($postArr['fulldescription']?'"'.$this->cleanInStr($postArr['fulldescription']).'"':'NULL').','.
			($postArr['latitudedecimal']?is_numeric($postArr['latitudedecimal']):'NULL').','.
			($postArr['longitudedecimal']?is_numeric($postArr['longitudedecimal']):'NULL').','.
			(array_key_exists('publicedits',$postArr)&&is_numeric($postArr['publicedits'])?$postArr['publicedits']:0).','.
			(array_key_exists('publishToGbif',$postArr)&&is_numeric($postArr['publishToGbif'])?$postArr['publishToGbif']:0).','.
			(array_key_exists('publishToIdigbio',$postArr)&&is_numeric($postArr['publishToIdigbio'])?$postArr['publishToIdigbio']:0).','.
			(array_key_exists('guidtarget',$postArr)?'"'.$this->cleanInStr($postArr['guidtarget']).'"':'NULL').','.
			($postArr['rights']?'"'.$this->cleanInStr($postArr['rights']).'"':'NULL').','.
			($postArr['rightsholder']?'"'.$this->cleanInStr($postArr['rightsholder']).'"':'NULL').','.
			($postArr['accessrights']?'"'.$this->cleanInStr($postArr['accessrights']).'"':'NULL').','.
			($icon?'"'.$icon.'"':'NULL').','.
			(array_key_exists('managementtype',$postArr)?'"'.$this->cleanInStr($postArr['managementtype']).'"':'Snapshot').','.
			(array_key_exists('colltype',$postArr)?'"'.$this->cleanInStr($postArr['colltype']).'"':'Preserved Specimens').',"'.
			UuidFactory::getUuidV4().'",'.
			(array_key_exists('individualurl',$postArr)?'"'.$this->cleanInStr($postArr['individualurl']).'"':'NULL').','.
			(array_key_exists('sortseq',$postArr)&&is_numeric($postArr['sortseq'])?$postArr['sortseq']:'NULL').') ';
		//echo "<div>$sql</div>";
		$cid = 0;
		if($this->conn->query($sql)){
			$cid = $this->conn->insert_id;
			$sql = 'INSERT INTO omcollectionstats(collid,recordcnt,uploadedby) VALUES('.$cid.',0,"'.$GLOBALS['SYMB_UID'].'")';
			$this->conn->query($sql);
			//Add collection to category
			if(isset($postArr['ccpk']) && $postArr['ccpk']){
				$sql = 'INSERT INTO omcollcatlink (ccpk,collid) VALUES('.$postArr['ccpk'].','.$cid.')';
				if(!$this->conn->query($sql)){
					$status = 'ERROR inserting collection category link(2): '.$this->conn->error.'; SQL: '.$sql;
					return $status;
				}
			}
			$this->collid = $cid;
		}
		else{
			$cid = 'ERROR inserting new collection: '.$this->conn->error;
		}
		return $cid;
	}

	private function addIconImageFile(){
		$targetPath = $GLOBALS['SERVER_ROOT'].'/content/collicon/';
		$urlBase = $GLOBALS['CLIENT_ROOT'].'/content/collicon/';
		$urlBase = $this->getDomainPath().$urlBase;

		//Clean file name
		$fileName = basename($_FILES['iconfile']['name']);
		$imgExt = '';
		if($p = strrpos($fileName,".")) $imgExt = strtolower(substr($fileName,$p));
		$fileName = strtolower($_REQUEST["institutioncode"].($_REQUEST["collectioncode"]?'-'.$_REQUEST["collectioncode"]:''));
		$fileName = str_replace(array("%20","%23"," ","__"),"_",$fileName);
		if(strlen($fileName) > 30) $fileName = substr($fileName,0,30);
		$fileName .= $imgExt;

		//Upload file
		$fullUrl = '';
		if(move_uploaded_file($_FILES['iconfile']['tmp_name'], $targetPath.$fileName)) $fullUrl = $urlBase.$fileName;

		return $fullUrl;
	}

	//Resource link functions
	public function saveResourceLink($postArr){
		if($this->collid){
			$sql = 'UPDATE omcollections SET resourceJson = '.($postArr['resourcejson']?'"'.$this->cleanInStr($postArr['resourcejson']).'"':'NULL').' WHERE collid = '.$this->collid;
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR updating resource link: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	//Collection contact functions
	public function saveContact($postArr){
		$modArr = array();
		$contactArr = $this->getContactArr();
		if($postArr['firstName']) $modArr['firstName'] = $postArr['firstName'];
		if($postArr['lastName']) $modArr['lastName'] = $postArr['lastName'];
		if($postArr['role']) $modArr['role'] = $postArr['role'];
		if($postArr['email']) $modArr['email'] = $postArr['email'];
		if(isset($postArr['centralContact']) && $postArr['centralContact']){
			$modArr['centralContact'] = $postArr['centralContact'];
			if($contactArr){
				foreach($contactArr as $cIndex => $cArr){
					if(isset($cArr['centralContact'])) unset($contactArr[$cIndex]['centralContact']);
				}
			}
		}
		if($postArr['phone']) $modArr['phone'] = $postArr['phone'];
		if($postArr['orcid']){
			if(preg_match('/(\d{4}-\d{4}-\d{4}-\d{4})/', $postArr['orcid'], $m)){
				$modArr['orcid'] = $m[1];
			}
		}
		$contactIndex = $postArr['contactIndex'];
		if(is_numeric($contactIndex)) $contactArr[$contactIndex] = $modArr;
		else $contactArr[] = $modArr;
		return $this->updateContactJson($contactArr);
	}

	public function deleteContact($index){
		$contactArr = $this->getContactArr();
		unset($contactArr[$index]);
		return $this->updateContactJson($contactArr);
	}

	private function updateContactJson($contactArr){
		if($this->collid){
			$sql = 'UPDATE omcollections SET contactJson = "'.$this->cleanInStr(json_encode($contactArr)).'" WHERE collid = '.$this->collid;
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR updating contact: '.$this->conn->error;
				return false;
			}
			return true;
		}
		return false;
	}

	private function getContactArr(){
		$jsonStr = '';
		$sql = 'SELECT contactJson FROM omcollections WHERE collid = '.$this->collid;
		if($rs = $this->conn->query($sql)){
			if($r = $rs->fetch_object()){
				$jsonStr = $r->contactJson;
			}
			$rs->free();
		}
		return json_decode($jsonStr,true);
	}

	//Institution address functions
	public function getAddress(){
		$retArr = Array();
		if($this->collid){
			$sql = 'SELECT i.iid, i.institutioncode, i.institutionname, i.institutionname2, i.address1, i.address2, '.
				'i.city, i.stateprovince, i.postalcode, i.country, i.phone, i.contact, i.email, i.url, i.notes '.
				'FROM institutions i INNER JOIN omcollections c ON i.iid = c.iid '.
				'WHERE (c.collid = '.$this->collid.") ";
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['iid'] = $r->iid;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
				$retArr['phone'] = $r->phone;
				$retArr['contact'] = $r->contact;
				$retArr['email'] = $r->email;
				$retArr['url'] = $r->url;
				$retArr['notes'] = $r->notes;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function linkAddress($addIID){
		$status = false;
		if($this->collid && is_numeric($addIID)){
			$con = MySQLiConnectionFactory::getCon("write");
			$sql = 'UPDATE omcollections SET iid = '.$addIID.' WHERE collid = '.$this->collid;
			if($con->query($sql)){
				$status = true;
			}
			else{
				$this->errorMessage = 'ERROR linking institution address: '.$con->error;
			}
			$con->close();
		}
		return $status;
	}

	public function removeAddress($removeIID){
		$status = false;
		if($this->collid && is_numeric($removeIID)){
			$con = MySQLiConnectionFactory::getCon("write");
			$sql = 'UPDATE omcollections SET iid = NULL '.
				'WHERE collid = '.$this->collid.' AND iid = '.$removeIID;
			if($con->query($sql)){
				$status = true;
			}
			else{
				$this->errorMessage = 'ERROR removing institution address: '.$con->error;
			}
			$con->close();
		}
		return $status;
	}

	//Publishing functions
	public function batchTriggerGBIFCrawl($collIdArr){
		$sql = 'SELECT collid, publishToGbif, dwcaUrl, aggKeysStr FROM omcollections WHERE CollID IN('.implode(',',$collIdArr).') ';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			if($row->publishToGbif && $row->aggKeysStr){
				$gbifKeyArr = json_decode($row->aggKeysStr,true);
				if(isset($gbifKeyArr['datasetKey']) && $row->dwcaUrl) $this->triggerGBIFCrawl($gbifKeyArr['datasetKey'], $row->dwcaUrl, $row->collid, $row->collectionname);
			}
		}
		$rs->free();
	}

	public function triggerGBIFCrawl($datasetKey, $dwcUri, $collid, $collectionname){
		global $GBIF_USERNAME,$GBIF_PASSWORD;
		if(!$this->logFH){
			$this->setVerboseMode(3);
			$logPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1)=='/'?'':'/')."content/logs/gbif/GBIF_".date('Y-m-d').".log";
			$this->setLogFH($logPath);
		}
		$this->logOrEcho('Starting GBIF harvest for: '.$collectionname.' (#'.$collid.')');
		if($datasetKey){
			$loginStr = $GBIF_USERNAME.':'.$GBIF_PASSWORD;
			if($dwcUri){
				//Make sure end point is up-to-date
				$epUrl = 'https://api.gbif.org/v1/dataset/'.$datasetKey.'/endpoint';
				//Get endpoint
				$ch = curl_init($epUrl);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Accept: application/json'));
				$endpointArr = json_decode(curl_exec($ch),true);
				curl_close($ch);

				$endpointChanged = true;
				foreach($endpointArr as $epArr){
					if($epArr['url'] == $dwcUri) $endpointChanged = false;
					break;
				}

				if($endpointChanged || !$endpointArr){
					//Endpoint has changed, delete old endpoints
					foreach($endpointArr as $epArr){
						$this->logOrEcho('Resetting Endpoints due to change...', 1);
						if(isset($epArr['key'])){
							$this->logOrEcho('Deleting Endpoint (key: '.$epArr['key'].')...', 2);
							$ch = curl_init($epUrl.'/'.$epArr['key']);
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FAILONERROR, true);
							curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Accept: application/json'));
							curl_exec($ch);
							if(curl_error($ch)) $this->logOrEcho('ERROR deleting Endpoint: '.curl_error($ch), 3);
							curl_close($ch);
						}
					}

					//Add new endpoint
					$this->logOrEcho('Adding new Endpoint (url: '.$dwcUri.')...', 1);
					$ch = curl_init($epUrl);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					$dataStr = json_encode( array( 'type' => 'DWC_ARCHIVE','url' => $dwcUri ) );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataStr );
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FAILONERROR, true);
					curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Accept: application/json'));
					$endpointStr = curl_exec($ch);
					if(!strpos($endpointStr,' ') && strlen($endpointStr) == 36) $this->endpointKey = $endpointStr;
					if(curl_error($ch)) $this->logOrEcho('ERROR adding Endpoint: '.curl_error($ch), 2);
					curl_close($ch);
				}
			}

			//Trigger Crawl
			$this->logOrEcho('Triggering crawl...', 1);
			$dsUrl = 'https://api.gbif.org/v1/dataset/'.$datasetKey.'/crawl';
			$ch = curl_init($dsUrl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
			curl_exec($ch);
			if(curl_error($ch)) $this->logOrEcho('ERROR triggering crawl: '.curl_error($ch), 2);
			curl_close($ch);
			$this->logOrEcho('Done!', 1);
		}
		else{
			$this->logOrEcho('ABORT: datasetKey IS NULL', 1);
		}
	}

	public function setAggKeys($aggKeyArr){
		if($aggKeyArr){
			if(isset($aggKeyArr['organizationKey'])) $this->organizationKey = $aggKeyArr['organizationKey'];
			if(isset($aggKeyArr['installationKey']) && $aggKeyArr['installationKey']) $this->installationKey = $aggKeyArr['installationKey'];
			if(isset($aggKeyArr['datasetKey']) && $aggKeyArr['datasetKey']){
				$dsKey = $aggKeyArr['datasetKey'];
				if(!strpos($dsKey,' ') && strlen($dsKey) == 36) $this->datasetKey = $dsKey;
			}
			if(isset($aggKeyArr['endpointKey']) && $aggKeyArr['endpointKey']) $this->endpointKey = $aggKeyArr['endpointKey'];
			if(isset($aggKeyArr['idigbioKey']) && $aggKeyArr['idigbioKey']) $this->idigbioKey = $aggKeyArr['idigbioKey'];
		}
	}

	public function getOrganizationKey(){
		return $this->organizationKey;
	}

	public function getInstallationKey(){
		if(!$this->installationKey) $this->setGbifInstKey();
		return $this->installationKey;
	}

	private function setGbifInstKey(){
		$sql = 'SELECT aggKeysStr FROM omcollections WHERE aggKeysStr IS NOT NULL ';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$keyArr = json_decode($row->aggKeysStr,true);
			if(isset($keyArr['installationKey']) && $keyArr['installationKey']){
				$this->installationKey = $keyArr['installationKey'];
				break;
			}
		}
		$rs->free();
	}

	public function getDatasetKey(){
		return $this->datasetKey;
	}

	public function getEndpointKey(){
		return $this->endpointKey;
	}

	public function getIdigbioKey(){
		return $this->idigbioKey;
	}

	public function findIdigbioKey($guid){
		global $CLIENT_ROOT;
		$url = 'https://search.idigbio.org/v2/search/recordsets?rsq={%22recordids%22:[';
		$url .= '%22'.$guid.'%22,';
		$url .= '%22https://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/webservices/dwc/'.$guid.'%22,';
		$url .= '%22http://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/webservices/dwc/'.$guid.'%22';
		$url .= ']}';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($ch);
		curl_close($ch);
		$returnArr = json_decode($result,true);
		if(isset($returnArr['items'][0]['uuid'])) $this->idigbioKey = $returnArr['items'][0]['uuid'];
		if($this->idigbioKey) $this->updateAggKeys();
		return $this->idigbioKey;
	}

	public function updateAggKeys(){
		$status = true;
		if($this->collid){
			$aggKeyArr = array();
			if($this->organizationKey) $aggKeyArr['organizationKey'] = $this->organizationKey;
			if($this->installationKey) $aggKeyArr['installationKey'] = $this->installationKey;
			if($this->datasetKey) $aggKeyArr['datasetKey'] = $this->datasetKey;
			if($this->endpointKey) $aggKeyArr['endpointKey'] = $this->endpointKey;
			if($this->idigbioKey) $aggKeyArr['idigbioKey'] = $this->idigbioKey;
			$conn = MySQLiConnectionFactory::getCon("write");
			$sql = 'UPDATE omcollections SET aggKeysStr = '.($aggKeyArr?'"'.$this->cleanInStr(json_encode($aggKeyArr)).'"':'NULL').' WHERE (collid = '.$this->collid.')';
			if(!$conn->query($sql)){
				return 'ERROR saving key: '.$conn->error;
			}
			$conn->close();
		}
		return $status;
	}

	//Get taxon and geo statistics
	public function getTaxonCounts($f=''){
		$returnArr = Array();
		$family = $this->cleanInStr($f);
		$sql = '';
		if($family){
			$sql = 'SELECT t.unitname1 as taxon, Count(o.occid) AS cnt '.
				'FROM omoccurrences o INNER JOIN taxa t ON o.tidinterpreted = t.tid '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.Family = "'.$family.'") AND (t.unitname1 != "'.$family.'") '.
				'GROUP BY o.CollID, t.unitname1, o.Family ';
		}
		else{
			$sql = 'SELECT o.family as taxon, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.Family IS NOT NULL) AND (o.Family <> "") '.
				'GROUP BY o.CollID, o.Family ';
		}
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$returnArr[$row->taxon] = $row->cnt;
		}
		$rs->free();
		asort($returnArr);
		return $returnArr;
	}

	public function getGeographyStats($country,$state){
		$retArr = Array();
		$sql = '';
		if($state){
			$sql = 'SELECT o.county as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') '.($country?'AND (o.country = "'.$this->cleanInStr($country).'") ':'').
				'AND (o.stateprovince = "'.$this->cleanInStr($state).'") AND (o.county IS NOT NULL) '.
				'GROUP BY o.StateProvince, o.county';
		}
		elseif($country){
			$sql = 'SELECT o.stateprovince as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.StateProvince IS NOT NULL) AND (o.country = "'.$this->cleanInStr($country).'") '.
				'GROUP BY o.StateProvince, o.country';
		}
		else{
			$sql = 'SELECT o.country as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.Country IS NOT NULL) '.
				'GROUP BY o.Country ';
		}
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$t = $row->termstr;
			$cnt = $row->cnt;
			if($state){
				$t = trim(str_ireplace(array(' county',' co.',' counties'),'',$t));
				if(array_key_exists($t, $retArr)) $cnt = $cnt + $retArr[$t];
			}
			//if($country) $t = ucwords(strtolower($t));
			if($t) $retArr[$t] = $cnt;
		}
		$rs->free();
		ksort($retArr);
		return $retArr;
	}

	public function getTaxonomyStats($famStr){
		$retArr = Array();
		$sql = 'SELECT IFNULL(ts.family,o.family) as taxon, count(DISTINCT o.occid) as cnt '.
			'FROM omoccurrences o LEFT JOIN taxstatus ts ON o.tidinterpreted = ts.tid '.
			'WHERE (o.collid = '.$this->collid.') AND (ts.taxauthid = 1 OR ts.taxauthid IS NULL) '.
			'GROUP BY IFNULL(ts.family,o.family)';
		if($famStr){
			$sql = 'SELECT t.unitname1 as taxon, count(o.occid) as cnt '.
				'FROM omoccurrences o INNER JOIN taxa t ON o.tidinterpreted = t.tid '.
				'WHERE (o.family = "'.$this->cleanInStr($famStr).'" OR o.sciname = "'.$this->cleanInStr($famStr).'") AND (o.collid = '.$this->collid.') AND (t.unitname1 IS NOT NULL) AND (t.rankid > 140) '.
				'GROUP BY t.unitname1';
		}
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->taxon) $retArr[ucwords($r->taxon)] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	//Statistic functions
	public function getBasicStats(){
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT uploaddate, recordcnt, georefcnt, familycnt, genuscnt, speciescnt, dynamicProperties FROM omcollectionstats WHERE collid = '.$this->collid;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$uDate = "";
				if($row->uploaddate){
					$uDate = $row->uploaddate;
					$month = substr($uDate,5,2);
					$day = substr($uDate,8,2);
					$year = substr($uDate,0,4);
					$uDate = date("j F Y",mktime(0,0,0,$month,$day,$year));
				}
				$retArr['uploaddate'] = $uDate;
				$retArr['recordcnt'] = $row->recordcnt;
				$retArr['georefcnt'] = $row->georefcnt;
				$retArr['familycnt'] = $row->familycnt;
				$retArr['genuscnt'] = $row->genuscnt;
				$retArr['speciescnt'] = $row->speciescnt;
				$retArr['dynamicProperties'] = $row->dynamicProperties;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function updateStatistics($verbose = false){
		$occurMaintenance = new OccurrenceMaintenance();
		if($verbose){
			echo '<ul>';
			$occurMaintenance->setVerbose(true);
			echo '<li>General cleaning in preparation for collecting stats...</li>';
			flush();
			ob_flush();
		}
		$occurMaintenance->generalOccurrenceCleaning($this->collid);
		if($verbose){
			echo '<li>Updating statistics...</li>';
			flush();
			ob_flush();
		}
		$occurMaintenance->updateCollectionStats($this->collid, true);
		if($verbose){
			echo '<li>Finished updating collection statistics</li>';
			flush();
			ob_flush();
		}
	}

	public function getStatCollectionList($catId = ""){
		//$catIdArr = array();
		//Set collections
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk, '.
			'cat.category, cat.icon AS caticon, cat.acronym '.
			'FROM omcollections c LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid '.
			'LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk '.
			'ORDER BY ccl.sortsequence, cat.category, c.sortseq, c.CollectionName ';
		//echo "<div>SQL: ".$sql."</div>";
		$result = $this->conn->query($sql);
		$collArr = array();
		while($r = $result->fetch_object()){
			$collType = '';
			if(stripos($r->colltype, "observation") !== false) $collType = 'obs';
			if(stripos($r->colltype, "specimen")) $collType = 'spec';
			if($collType){
				if($r->ccpk){
					if(!isset($collArr[$collType]['cat'][$r->ccpk]['name'])){
						$collArr[$collType]['cat'][$r->ccpk]['name'] = $r->category;
						$collArr[$collType]['cat'][$r->ccpk]['icon'] = $r->caticon;
						$collArr[$collType]['cat'][$r->ccpk]['acronym'] = $r->acronym;
						//if(in_array($r->ccpk,$catIdArr)) $retArr[$collType]['cat'][$catId]['isselected'] = 1;
					}
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]["instcode"] = $r->institutioncode;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collcode"] = $r->collectioncode;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collname"] = $r->collectionname;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]["icon"] = $r->icon;
				}
				else{
					$collArr[$collType]['coll'][$r->collid]["instcode"] = $r->institutioncode;
					$collArr[$collType]['coll'][$r->collid]["collcode"] = $r->collectioncode;
					$collArr[$collType]['coll'][$r->collid]["collname"] = $r->collectionname;
					$collArr[$collType]['coll'][$r->collid]["icon"] = $r->icon;
				}
			}
		}
		$result->free();

		$retArr = array();
		//Modify sort so that default catid is first
		if(isset($collArr['spec']['cat'][$catId])){
			$retArr['spec']['cat'][$catId] = $collArr['spec']['cat'][$catId];
			unset($collArr['spec']['cat'][$catId]);
		}
		elseif(isset($collArr['obs']['cat'][$catId])){
			$retArr['obs']['cat'][$catId] = $collArr['obs']['cat'][$catId];
			unset($collArr['obs']['cat'][$catId]);
		}
		foreach($collArr as $t => $tArr){
			foreach($tArr as $g => $gArr){
				foreach($gArr as $id => $idArr){
					$retArr[$t][$g][$id] = $idArr;
				}
			}
		}
		return $retArr;
	}

	public function batchUpdateStatistics($collId){
		if(preg_match('/^[0-9,]+$/',$collId)){
			echo 'Updating collection statistics...';
			echo '<ul>';
			//echo '<li>General cleaning in preparation for collecting stats... </li>';
			flush();
			ob_flush();
			$occurMaintenance = new OccurrenceMaintenance();
			//$occurMaintenance->generalOccurrenceCleaning();
			$sql = 'SELECT collid, collectionname FROM omcollections WHERE collid IN('.$collId.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				echo '<li style="margin-left:15px;">Cleaning statistics for: '.$r->collectionname.'</li>';
				flush();
				ob_flush();
				$occurMaintenance->updateCollectionStats($r->collid, true);
			}
			$rs->free();
			echo '<li>Statistics update complete!</li>';
			echo '</ul>';
			flush();
			ob_flush();
		}
	}

	public function runStatistics($collId){
		$returnArr = Array();
		if(preg_match('/^[0-9,]+$/',$collId)){
			$sql = 'SELECT c.collid, c.CollectionName, IFNULL(s.recordcnt,0) AS recordcnt, IFNULL(s.georefcnt,0) AS georefcnt, s.dynamicProperties '.
				'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
				'WHERE c.collid IN('.$collId.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$returnArr[$r->CollectionName]['collid'] = $r->collid;
				$returnArr[$r->CollectionName]['CollectionName'] = $r->CollectionName;
				$returnArr[$r->CollectionName]['recordcnt'] = $r->recordcnt;
				$returnArr[$r->CollectionName]['georefcnt'] = $r->georefcnt;
				$returnArr[$r->CollectionName]['dynamicProperties'] = $r->dynamicProperties;
			}
			$rs->free();
			$sql2 = 'SELECT c.CollectionName, COUNT(DISTINCT o.family) AS FamilyCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
				'COUNT(DISTINCT i.occid) AS OccurrenceImageCount '.
				'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tidinterpreted = t.TID '.
				'INNER JOIN omcollections AS c ON o.collid = c.CollID '.
				'LEFT JOIN images AS i ON o.occid = i.occid '.
				'WHERE c.CollID IN('.$collId.') '.
				'GROUP BY c.CollectionName ';
			//echo $sql2;
			$rs = $this->conn->query($sql2);
			while($r = $rs->fetch_object()){
				$returnArr[$r->CollectionName]['familycnt'] = $r->FamilyCount;
				$returnArr[$r->CollectionName]['genuscnt'] = $r->GeneraCount;
				$returnArr[$r->CollectionName]['speciescnt'] = $r->SpeciesCount;
				$returnArr[$r->CollectionName]['TotalTaxaCount'] = $r->TotalTaxaCount;
				$returnArr[$r->CollectionName]['OccurrenceImageCount'] = $r->OccurrenceImageCount;
			}
			$rs->free();
			$sql3 = 'SELECT COUNT(DISTINCT o.family) AS FamilyCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
				'COUNT(DISTINCT CASE WHEN i.occid IS NOT NULL THEN i.occid ELSE NULL END) AS TotalImageCount '.
				'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.TID '.
				'LEFT JOIN images AS i ON o.occid = i.occid '.
				'WHERE o.collid IN('.$collId.') ';
			//echo $sql3;
			$rs = $this->conn->query($sql3);
			while($r = $rs->fetch_object()){
				$returnArr['familycnt'] = $r->FamilyCount;
				$returnArr['genuscnt'] = $r->GeneraCount;
				$returnArr['speciescnt'] = $r->SpeciesCount;
				$returnArr['TotalTaxaCount'] = $r->TotalTaxaCount;
				$returnArr['TotalImageCount'] = $r->TotalImageCount;
			}
			$rs->free();
		}
		return $returnArr;
	}

	public function runStatisticsQuery($collId,$taxon,$country){
		$returnArr = Array();
		if(preg_match('/^[0-9,]+$/',$collId)){
			$sqlFrom = 'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tidinterpreted = t.TID LEFT JOIN omcollections AS c ON o.collid = c.CollID ';
			$sqlWhere = 'WHERE o.collid IN('.$collId.') ';
			if($taxon){
				$pTID = '';
				$sql = 'SELECT TID FROM taxa WHERE SciName = "'.$this->cleanInStr($taxon).'" ';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$pTID = $r->TID;
				}
				$rs->free();
				$sqlWhere .= 'AND ((o.sciname = "'.$this->cleanInStr($taxon).'") OR (o.tidinterpreted IN(SELECT DISTINCT tid FROM taxaenumtree WHERE taxauthid = 1 AND parenttid IN('.$pTID.')))) ';
			}
			if($country) $sqlWhere .= 'AND o.country = "'.$this->cleanInStr($country).'" ';
			$sql2 = 'SELECT c.CollID, c.CollectionName, COUNT(DISTINCT o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, '.
				'COUNT(DISTINCT o.family) AS FamilyCount, COUNT(DISTINCT t.UnitName1) AS GeneraCount, COUNT(o.typeStatus) AS TypeCount, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS SpecimensCountID, '.
				'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
				'COUNT(CASE WHEN ISNULL(o.family) THEN o.occid ELSE NULL END) AS SpecimensNullFamily, '.
				'COUNT(CASE WHEN ISNULL(o.country) THEN o.occid ELSE NULL END) AS SpecimensNullCountry, '.
				'COUNT(CASE WHEN ISNULL(o.decimalLatitude) THEN o.occid ELSE NULL END) AS SpecimensNullLatitude ';
			$sql2 .= $sqlFrom.$sqlWhere;
			$sql2 .= 'GROUP BY c.CollectionName ';
			//echo 'sql2: '.$sql2;
			$rs = $this->conn->query($sql2);
			while($r = $rs->fetch_object()){
				$returnArr[$r->CollectionName]['CollID'] = $r->CollID;
				$returnArr[$r->CollectionName]['CollectionName'] = $r->CollectionName;
				$returnArr[$r->CollectionName]['recordcnt'] = $r->SpecimenCount;
				$returnArr[$r->CollectionName]['georefcnt'] = $r->GeorefCount;
				$returnArr[$r->CollectionName]['speciesID'] = $r->SpecimensCountID;
				$returnArr[$r->CollectionName]['familycnt'] = $r->FamilyCount;
				$returnArr[$r->CollectionName]['genuscnt'] = $r->GeneraCount;
				$returnArr[$r->CollectionName]['speciescnt'] = $r->SpeciesCount;
				$returnArr[$r->CollectionName]['TotalTaxaCount'] = $r->TotalTaxaCount;
				$returnArr[$r->CollectionName]['types'] = $r->TypeCount;
				$returnArr[$r->CollectionName]['SpecimensNullFamily'] = $r->SpecimensNullFamily;
				$returnArr[$r->CollectionName]['SpecimensNullCountry'] = $r->SpecimensNullCountry;
				$returnArr[$r->CollectionName]['SpecimensNullLatitude'] = $r->SpecimensNullLatitude;
			}
			$rs->free();
			$sql3 = 'SELECT o.family, COUNT(o.occid) AS SpecimensPerFamily, COUNT(o.decimalLatitude) AS GeorefSpecimensPerFamily, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerFamily, '.
				'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerFamily ';
			$sql3 .= $sqlFrom.$sqlWhere;
			$sql3 .= 'GROUP BY o.family ';
			//echo 'sql3: '.$sql3;
			$rs = $this->conn->query($sql3);
			while($r = $rs->fetch_object()){
				if($r->family){
					$returnArr['families'][$r->family]['SpecimensPerFamily'] = $r->SpecimensPerFamily;
					$returnArr['families'][$r->family]['GeorefSpecimensPerFamily'] = $r->GeorefSpecimensPerFamily;
					$returnArr['families'][$r->family]['IDSpecimensPerFamily'] = $r->IDSpecimensPerFamily;
					$returnArr['families'][$r->family]['IDGeorefSpecimensPerFamily'] = $r->IDGeorefSpecimensPerFamily;
				}
			}
			$rs->free();
			$sql4 = 'SELECT o.country, COUNT(o.occid) AS CountryCount, COUNT(o.decimalLatitude) AS GeorefSpecimensPerCountry, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerCountry, '.
				'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerCountry ';
			$sql4 .= $sqlFrom.$sqlWhere;
			$sql4 .= 'GROUP BY o.country ';
			//echo 'sql4: '.$sql4;
			$rs = $this->conn->query($sql4);
			while($r = $rs->fetch_object()){
				if($r->country){
					$returnArr['countries'][$r->country]['CountryCount'] = $r->CountryCount;
					$returnArr['countries'][$r->country]['GeorefSpecimensPerCountry'] = $r->GeorefSpecimensPerCountry;
					$returnArr['countries'][$r->country]['IDSpecimensPerCountry'] = $r->IDSpecimensPerCountry;
					$returnArr['countries'][$r->country]['IDGeorefSpecimensPerCountry'] = $r->IDGeorefSpecimensPerCountry;
				}
			}
			$rs->free();
			$sql5 = 'SELECT c.CollID, c.CollectionName, COUNT(DISTINCT CASE WHEN i.occid IS NOT NULL THEN i.occid ELSE NULL END) AS TotalImageCount ';
			$sql5 .= $sqlFrom;
			$sql5 .= 'LEFT JOIN images AS i ON o.occid = i.occid ';
			$sql5 .= $sqlWhere;
			$sql5 .= 'GROUP BY c.CollectionName ';
			//echo 'sql5: '.$sql5;
			$rs = $this->conn->query($sql5);
			while($r = $rs->fetch_object()){
				$returnArr[$r->CollectionName]['OccurrenceImageCount'] = $r->TotalImageCount;
			}
			$rs->free();
		}
		return $returnArr;
	}

	public function getYearStatsHeaderArr($months){
		$dateArr = array();
		$a = $months + 1;
		$reps = $a;
		for ($i = 0; $i < $reps; $i++) {
			$timestamp = mktime(0, 0, 0, date('n') - $i, 1);
			$dateArr[$a] = date('Y', $timestamp).'-'.date('n', $timestamp);
			$a--;
		}
		ksort($dateArr);

		return $dateArr;
	}

	public function getYearStatsDataArr($collId,$days){
		$statArr = array();
		if(preg_match('/^[0-9,]+$/',$collId) && is_numeric($days)){
			$sql = 'SELECT CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, c.collectionname '.
				'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
				'LEFT JOIN images AS i ON o.occid = i.occid '.
				'WHERE o.collid IN('.$collId.') AND ((o.dateLastModified IS NOT NULL AND datediff(curdate(), o.dateLastModified) < '.$days.') OR (datediff(curdate(), i.InitialTimeStamp) < '.$days.')) '.
				'ORDER BY c.collectionname ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$statArr[$r->collcode]['collcode'] = $r->collcode;
				$statArr[$r->collcode]['collectionname'] = $r->collectionname;
			}

			$sql = 'SELECT CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, CONCAT_WS("-",year(o.dateEntered),month(o.dateEntered)) as dateEntered, '.
				'c.collectionname, month(o.dateEntered) as monthEntered, year(o.dateEntered) as yearEntered, COUNT(o.occid) AS speccnt '.
				'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
				'WHERE o.collid in('.$collId.') AND o.dateEntered IS NOT NULL AND datediff(curdate(), o.dateEntered) < '.$days.' '.
				'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$statArr[$r->collcode]['stats'][$r->dateEntered]['speccnt'] = $r->speccnt;
			}

			$sql = 'SELECT CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, CONCAT_WS("-",year(o.dateLastModified),month(o.dateLastModified)) as dateEntered, '.
				'c.collectionname, month(o.dateLastModified) as monthEntered, year(o.dateLastModified) as yearEntered, '.
				'COUNT(CASE WHEN o.processingstatus = "unprocessed" THEN o.occid ELSE NULL END) AS unprocessedCount, '.
				'COUNT(CASE WHEN o.processingstatus = "stage 1" THEN o.occid ELSE NULL END) AS stage1Count, '.
				'COUNT(CASE WHEN o.processingstatus = "stage 2" THEN o.occid ELSE NULL END) AS stage2Count, '.
				'COUNT(CASE WHEN o.processingstatus = "stage 3" THEN o.occid ELSE NULL END) AS stage3Count '.
				'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
				'WHERE o.collid in('.$collId.') AND o.dateLastModified IS NOT NULL AND datediff(curdate(), o.dateLastModified) < '.$days.' '.
				'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$statArr[$r->collcode]['stats'][$r->dateEntered]['unprocessedCount'] = $r->unprocessedCount;
				$statArr[$r->collcode]['stats'][$r->dateEntered]['stage1Count'] = $r->stage1Count;
				$statArr[$r->collcode]['stats'][$r->dateEntered]['stage2Count'] = $r->stage2Count;
				$statArr[$r->collcode]['stats'][$r->dateEntered]['stage3Count'] = $r->stage3Count;
			}

			$sql2 = 'SELECT CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, CONCAT_WS("-",year(i.InitialTimeStamp),month(i.InitialTimeStamp)) as dateEntered, '.
				'c.collectionname, month(i.InitialTimeStamp) as monthEntered, year(i.InitialTimeStamp) as yearEntered, '.
				'COUNT(i.imgid) AS imgcnt '.
				'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
				'LEFT JOIN images AS i ON o.occid = i.occid '.
				'WHERE o.collid in('.$collId.') AND datediff(curdate(), i.InitialTimeStamp) < '.$days.' '.
				'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
			//echo $sql2;
			$rs = $this->conn->query($sql2);
			while($r = $rs->fetch_object()){
				$statArr[$r->collcode]['stats'][$r->dateEntered]['imgcnt'] = $r->imgcnt;
			}

			$sql3 = 'SELECT CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, CONCAT_WS("-",year(e.InitialTimeStamp),month(e.InitialTimeStamp)) as dateEntered, '.
				'c.collectionname, month(e.InitialTimeStamp) as monthEntered, year(e.InitialTimeStamp) as yearEntered, '.
				'COUNT(DISTINCT e.occid) AS georcnt '.
				'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
				'LEFT JOIN omoccuredits AS e ON o.occid = e.occid '.
				'WHERE o.collid in('.$collId.') AND datediff(curdate(), e.InitialTimeStamp) < '.$days.' '.
				'AND ((e.FieldName = "decimallongitude" AND e.FieldValueNew IS NOT NULL) '.
				'OR (e.FieldName = "decimallatitude" AND e.FieldValueNew IS NOT NULL)) '.
				'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
			//echo $sql2;
			$rs = $this->conn->query($sql3);
			while($r = $rs->fetch_object()){
				$statArr[$r->collcode]['stats'][$r->dateEntered]['georcnt'] = $r->georcnt;
			}
			$rs->free();
		}
		return $statArr;
	}

	public function getOrderStatsDataArr($collId){
		$statsArr = Array();
		if(preg_match('/^[0-9,]+$/',$collId)){
			$sql = 'SELECT (CASE WHEN t.RankId = 100 THEN t.SciName WHEN t2.RankId = 100 THEN t2.SciName ELSE NULL END) AS SciName, '.
				'COUNT(DISTINCT o.occid) AS SpecimensPerOrder, '.
				'COUNT(DISTINCT CASE WHEN o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS GeorefSpecimensPerOrder, '.
				'COUNT(DISTINCT CASE WHEN t2.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerOrder, '.
				'COUNT(DISTINCT CASE WHEN t2.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerOrder '.
				'FROM omoccurrences AS o LEFT JOIN taxaenumtree AS e ON o.tidinterpreted = e.tid '.
				'LEFT JOIN taxa AS t ON e.parenttid = t.TID '.
				'LEFT JOIN taxa AS t2 ON o.tidinterpreted = t2.TID '.
				'WHERE (o.collid IN('.$collId.')) AND (t.RankId = 100 OR t2.RankId = 100) AND e.taxauthid = 1 '.
				'GROUP BY SciName ';
			$rs = $this->conn->query($sql);
			//echo $sql;
			while($r = $rs->fetch_object()){
				$order = str_replace(array('"',"'"),"",$r->SciName);
				if($order){
					$statsArr[$order]['SpecimensPerOrder'] = $r->SpecimensPerOrder;
					$statsArr[$order]['GeorefSpecimensPerOrder'] = $r->GeorefSpecimensPerOrder;
					$statsArr[$order]['IDSpecimensPerOrder'] = $r->IDSpecimensPerOrder;
					$statsArr[$order]['IDGeorefSpecimensPerOrder'] = $r->IDGeorefSpecimensPerOrder;
				}
			}
			$rs->free();
		}
		return $statsArr;
	}

	//Misc functions
	public function unreviewedCommentsExist(){
		$retCnt = 0;
		$sql = 'SELECT count(c.comid) AS reccnt '.
			'FROM omoccurrences o INNER JOIN omoccurcomments c ON o.occid = c.occid '.
			'WHERE (o.collid = '.$this->collid.') AND (c.reviewstatus < 3)';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retCnt = $r->reccnt;
			}
			$rs->free();
		}
		return $retCnt;
	}

	//General data retrival functions
	public function getInstitutionArr(){
		$retArr = array();
		$sql = 'SELECT iid,institutionname,institutioncode FROM institutions ORDER BY institutionname,institutioncode ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->iid] = $r->institutionname.' ('.$r->institutioncode.')';
		}
		return $retArr;
	}

	public function getCategoryArr(){
		$retArr = array();
		$sql = 'SELECT ccpk, category FROM omcollcategories ORDER BY category ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->ccpk] = $r->category;
		}
		$rs->free();
		return $retArr;
	}

	public function traitCodingActivated(){
		$bool = false;
		$sql = 'SELECT traitid FROM tmtraits LIMIT 1';
		$rs = $this->conn->query($sql);
		if($rs->num_rows) $bool = true;
		$rs->free();
		return $bool;
	}

	public function materialSampleIsActive(){
		return $this->materialSampleIsActive;
	}

	//Misc functions
	public function cleanOutArr(&$arr){
		foreach($arr as $k => $v){
			$arr[$k] = $this->cleanOutStr($v);
		}
	}
}
?>