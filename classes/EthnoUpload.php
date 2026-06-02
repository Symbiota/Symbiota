<?php
include_once($SERVER_ROOT . '/config/dbconnection.php');

class EthnoUpload{

	private $conn;
    private $collId = 0;
	private $uploadFileName;
	private $uploadTargetPath;
	private $statArr = array();

	private $verboseMode = 1;
	private $logFH;
	private $errorStr = '';

	public function __construct() {
		$this->conn = MySQLiConnectionFactory::getCon('write');
 		// $this->setUploadTargetPath();
        $this->uploadTargetPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) != '/'?'/':'').'temp/data/';
 		set_time_limit(3000);
		ini_set('max_input_time',120);
	}

	public function __destruct(){
		if(!($this->conn === false)) {
			$this->conn->close();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fclose($this->logFH);
		}
	}

	public function setUploadFile(){
		if(array_key_exists('uploadfile',$_FILES)){
			$inFileName = basename($_FILES['uploadfile']['name']);
            $ext = substr(strrchr($inFileName, '.'), 1);
            $fileName = 'ethnoDataFile_'.time();
            $this->uploadFileName = $fileName.'.'.$ext;
            move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->uploadFileName);
		}
		elseif($this->uploadFileName){
			if(file_exists($this->uploadTargetPath.$this->uploadFileName) && substr($this->uploadFileName,-4) === '.zip'){
				$zip = new ZipArchive;
				$zip->open($this->uploadTargetPath.$this->uploadFileName);
				$zipFile = $this->uploadTargetPath.$this->uploadFileName;
				$this->uploadFileName = $zip->getNameIndex(0);
				$zip->extractTo($this->uploadTargetPath);
				$zip->close();
				unlink($zipFile);
			}
		}
	}

	public function loadSynopticFile($fieldMap){
		$this->outputMsg('Starting Upload',0);
		$this->conn->query('DELETE FROM uploadethnodataevent WHERE collid = '.$this->collId);
		$this->conn->query('OPTIMIZE TABLE uploadethnodataevent');
		$fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb') or die("Can't open file");
        $headerArr = fgetcsv($fh);
        $this->conn->query('SET autocommit=0');
        $this->conn->query('SET unique_checks=0');
        $this->conn->query('SET foreign_key_checks=0');
        $id = 1;
        $recordCnt = 0;
        while($recordArr = fgetcsv($fh)){
            $communityname = '';
            $recordNumber = '';
            $datasource = '';
            $eventdate = '';
            $eventlocation = '';
            $namedatadiscussion = '';
            $usedatadiscussion = '';
            $consultantdiscussion = '';
            foreach($fieldMap as $csvField => $field){
                if($field === 'communityname'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $communityname = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'recordnumber'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $recordNumber = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'datasource'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $datasource = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'eventdate'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $eventdate = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'eventlocation'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $eventlocation = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'namedatadiscussion'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $namedatadiscussion = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'usedatadiscussion'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $usedatadiscussion = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'consultantdiscussion'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $consultantdiscussion = $this->cleanInStr($recordArr[$index]);
                }
            }
            if($communityname || $recordNumber || $datasource || $eventdate || $eventlocation || $namedatadiscussion || $usedatadiscussion || $consultantdiscussion){
                $sql = 'INSERT INTO uploadethnodataevent(collid,communityname,recordNumber,datasource,eventdate,eventlocation,namedatadiscussion,usedatadiscussion,consultantdiscussion) ';
                $sql .= 'VALUES ('.$this->collId.','.($communityname?'"'.$communityname.'"':'null').','.($recordNumber?'"'.$recordNumber.'"':'null').','.($datasource?'"'.$datasource.'"':'"elicitation"').','.
                    ($eventdate?'"'.$eventdate.'"':'null').','.($eventlocation?'"'.$eventlocation.'"':'null').','.($namedatadiscussion?'"'.$namedatadiscussion.'"':'null').','.
                    ($usedatadiscussion?'"'.$usedatadiscussion.'"':'null').','.($consultantdiscussion?'"'.$consultantdiscussion.'"':'null').')';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $recordCnt++;
                    if($recordCnt%1000 === 0){
                        $this->outputMsg('Upload count: '.$recordCnt,1);
                        ob_flush();
                        flush();
                    }
                }
                else{
                    $this->outputMsg('ERROR loading data: '.$this->conn->error);
                }
            }
            $id++;
        }
        $this->conn->query('COMMIT');
        $this->conn->query('SET autocommit=1');
        $this->conn->query('SET unique_checks=1');
        $this->conn->query('SET foreign_key_checks=1');
		fclose($fh);
		$this->setSynopticUploadCount();
	}

    public function loadVernacularFile($fieldMap){
        $consultantArr = $this->getConsultantArr();
	    $this->outputMsg('Starting Upload',0);
        $this->conn->query('DELETE FROM uploadethnodata WHERE collid = '.$this->collId);
        $this->conn->query('OPTIMIZE TABLE uploadethnodata');
        $fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb') or die("Can't open file");
        $headerArr = fgetcsv($fh);
        $this->conn->query('SET autocommit=0');
        $this->conn->query('SET unique_checks=0');
        $this->conn->query('SET foreign_key_checks=0');
        $id = 1;
        $recordCnt = 0;
        while($recordArr = fgetcsv($fh)){
            $consultantName = '';
            $consultantId = '';
            $recordNumber = '';
            $refpages = '';
            $verbatimVernacularName = '';
            $annotatedVernacularName = '';
            $verbatimLanguage = '';
            $languageGlottologId = '';
            $otherVerbatimVernacularName = '';
            $otherLanguageGlottologId = '';
            $verbatimParse = '';
            $annotatedParse = '';
            $verbatimGloss = '';
            $annotatedGloss = '';
            $typology = '';
            $translation = '';
            $taxonomicDescription = '';
            $nameDiscussion = '';
            $consultantComments = '';
            $useDiscussion = '';
            foreach($fieldMap as $csvField => $field){
                if($field === 'consultantname'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $consultantName = $this->cleanInStr($recordArr[$index]);
                    $consultantId = array_key_exists($consultantName,$consultantArr)?$consultantArr[$consultantName]['id']:0;
                    if(!$consultantId){
                        $this->outputMsg('ERROR: Consultant '.$consultantName.' is not associated with this project.');
                    }
                }
                if($field === 'recordnumber'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $recordNumber = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'refpages'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $refpages = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'verbatimvernacularname'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $verbatimVernacularName = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'annotatedvernacularname'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $annotatedVernacularName = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'verbatimlanguage'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $verbatimLanguage = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'languageglottologid'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $languageGlottologId = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'otherverbatimvernacularname'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $otherVerbatimVernacularName = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'otherlanguageglottologid'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $otherLanguageGlottologId = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'verbatimparse'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $verbatimParse = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'annotatedparse'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $annotatedParse = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'verbatimgloss'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $verbatimGloss = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'annotatedgloss'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $annotatedGloss = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'typology'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $typology = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'translation'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $translation = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'taxonomicdescription'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $taxonomicDescription = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'namediscussion'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $nameDiscussion = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'consultantcomments'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $consultantComments = $this->cleanInStr($recordArr[$index]);
                }
                if($field === 'usediscussion'){
                    $index = array_search($csvField, array_keys($fieldMap), true);
                    $useDiscussion = $this->cleanInStr($recordArr[$index]);
                }
            }
            if($consultantId){
                if(!$languageGlottologId){
                    $languageGlottologId = $consultantArr[$consultantName]['langid'];
                }
                if($consultantName || $recordNumber || $refpages || $verbatimVernacularName || $annotatedVernacularName || $verbatimLanguage || $languageGlottologId || $otherVerbatimVernacularName || $otherLanguageGlottologId || $verbatimParse || $annotatedParse || $verbatimGloss || $annotatedGloss || $typology || $translation || $taxonomicDescription || $nameDiscussion || $consultantComments || $useDiscussion){
                    $sql = 'INSERT INTO uploadethnodata(collid,consultantName,recordNumber,refpages,verbatimVernacularName,annotatedVernacularName,verbatimLanguage,languageGlottologId,otherVerbatimVernacularName,otherLanguageGlottologId,verbatimParse,annotatedParse,verbatimGloss,annotatedGloss,typology,`translation`,taxonomicDescription,nameDiscussion,consultantComments,useDiscussion,ethPerID) ';
                    $sql .= 'VALUES ('.$this->collId.','.($consultantName?'"'.$consultantName.'"':'null').','.($recordNumber?'"'.$recordNumber.'"':'null').','.($refpages?'"'.$refpages.'"':'null').','.
                        ($verbatimVernacularName?'"'.$verbatimVernacularName.'"':'null').','.($annotatedVernacularName?'"'.$annotatedVernacularName.'"':'null').','.($verbatimLanguage?'"'.$verbatimLanguage.'"':'null').','.
                        ($languageGlottologId?'"'.$languageGlottologId.'"':'null').','.($otherVerbatimVernacularName?'"'.$otherVerbatimVernacularName.'"':'null').','.($otherLanguageGlottologId?'"'.$otherLanguageGlottologId.'"':'null').','.
                        ($verbatimParse?'"'.$verbatimParse.'"':'null').','.($annotatedParse?'"'.$annotatedParse.'"':'null').','.($verbatimGloss?'"'.$verbatimGloss.'"':'null').','.
                        ($annotatedGloss?'"'.$annotatedGloss.'"':'null').','.($typology?'"'.$typology.'"':'null').','.($translation?'"'.$translation.'"':'null').','.
                        ($taxonomicDescription?'"'.$taxonomicDescription.'"':'null').','.($nameDiscussion?'"'.$nameDiscussion.'"':'null').','.($consultantComments?'"'.$consultantComments.'"':'null').','.
                        ($useDiscussion?'"'.$useDiscussion.'"':'null').','.($consultantId?$consultantId:'null').')';
                    //echo "<div>".$sql."</div>";
                    if($this->conn->query($sql)){
                        $recordCnt++;
                        if($recordCnt%1000 === 0){
                            $this->outputMsg('Upload count: '.$recordCnt,1);
                            ob_flush();
                            flush();
                        }
                    }
                    else{
                        $this->outputMsg('ERROR loading data: '.$this->conn->error);
                    }
                }
            }
            $id++;
        }
        $this->conn->query('COMMIT');
        $this->conn->query('SET autocommit=1');
        $this->conn->query('SET unique_checks=1');
        $this->conn->query('SET foreign_key_checks=1');
        fclose($fh);
        $this->setVernacularUploadCount();
    }

	private function removeUploadFile(){
		if($this->uploadTargetPath && $this->uploadFileName && file_exists($this->uploadTargetPath . $this->uploadFileName)) {
			unlink($this->uploadTargetPath.$this->uploadFileName);
		}
	}

	public function cleanSynopticUpload(){
		$this->outputMsg('Linking uploaded community names to communitites linked to this project... ');
		$sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnocommunity AS c ON ue.communityname = c.communityname '.
			'LEFT JOIN ethnocollcommlink AS cc ON c.ethComID = cc.commID '.
			'SET ue.ethComID = c.ethComID '.
			'WHERE ue.collid = '.$this->collId.' AND cc.collID = '.$this->collId.' AND ue.communityname IS NOT NULL AND ISNULL(ue.ethComID) AND c.communityname IS NOT NULL ';
		if(!$this->conn->query($sql)){
			$this->outputMsg('ERROR: '.$this->conn->error,1);
		}
        $this->outputMsg('Linking records to currently existing records... ');
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN omoccurrences AS o ON ue.recordNumber = o.recordNumber '.
            'LEFT JOIN ethnodata AS d ON o.occid = d.occid '.
            'LEFT JOIN ethnodataevent AS de ON d.etheventid = de.etheventid '.
            'SET ue.etheventid = de.etheventid, ue.occid = o.occid '.
            'WHERE ue.collid = '.$this->collId.' AND o.collid = '.$this->collId.' AND de.collid = '.$this->collId.' AND ue.recordNumber IS NOT NULL AND o.occid IS NOT NULL AND de.etheventid IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $this->outputMsg('Merging data with currently existing records... ');
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.datasource = de.datasource '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.datasource) AND de.datasource IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.ethComID = de.ethComID '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.ethComID) AND de.ethComID IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.eventdate = de.eventdate '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.eventdate) AND de.eventdate IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.eventlocation = de.eventlocation '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.eventlocation) AND de.eventlocation IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.namedatadiscussion = de.namedatadiscussion '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.namedatadiscussion) AND de.namedatadiscussion IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.usedatadiscussion = de.usedatadiscussion '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.usedatadiscussion) AND de.usedatadiscussion IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN ethnodataevent AS de ON ue.etheventid = de.etheventid '.
            'SET ue.consultantdiscussion = de.consultantdiscussion '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL AND ISNULL(ue.consultantdiscussion) AND de.consultantdiscussion IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $this->outputMsg('Linking records to existing occurrence records... ');
        $sql = 'UPDATE uploadethnodataevent AS ue LEFT JOIN omoccurrences AS o ON ue.recordNumber = o.recordNumber '.
            'SET ue.eventdate = o.eventDate, ue.eventlocation = o.locality, ue.occid = o.occid '.
            'WHERE ue.collid = '.$this->collId.' AND o.collid = '.$this->collId.' AND ISNULL(ue.etheventid) AND ue.recordNumber IS NOT NULL AND o.occid IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
	}

    public function cleanVernacularUpload(){
        $this->outputMsg('Linking records to existing occurrence records... ');
        $sql = 'UPDATE uploadethnodata AS ue LEFT JOIN omoccurrences AS o ON ue.recordNumber = o.recordNumber '.
            'SET ue.occid = o.occid, ue.tid = o.tidinterpreted '.
            'WHERE ue.collid = '.$this->collId.' AND o.collid = '.$this->collId.' AND ue.recordNumber IS NOT NULL AND o.occid IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
        $this->outputMsg('Linking records to existing data event records... ');
        $sql = 'UPDATE uploadethnodata AS ue LEFT JOIN omoccurrences AS o ON ue.recordNumber = o.recordNumber '.
            'LEFT JOIN ethnodataevent AS de ON o.occid = de.occid '.
            'SET ue.etheventid = de.etheventid '.
            'WHERE ue.collid = '.$this->collId.' AND o.collid = '.$this->collId.' AND de.collid = '.$this->collId.' AND ISNULL(ue.etheventid) ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
    }

	public function analysisSynopticUpload(){
		$sql1 = 'SELECT count(*) as cnt FROM uploadethnodataevent WHERE collid = '.$this->collId.' ';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$this->statArr['total'] = $r1->cnt;
		}
		$rs1->free();

		$sql2 = 'SELECT count(*) as cnt FROM uploadethnodataevent WHERE collid = '.$this->collId.' AND etheventid IS NOT NULL ';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			$this->statArr['exist'] = $r2->cnt;
			$this->statArr['new'] = $this->statArr['total'] - $this->statArr['exist'];
		}
		$rs2->free();
	}

    public function analysisVernacularUpload(){
        $sql1 = 'SELECT count(*) as cnt FROM uploadethnodata WHERE collid = '.$this->collId.' ';
        $rs1 = $this->conn->query($sql1);
        while($r1 = $rs1->fetch_object()){
            $this->statArr['total'] = $r1->cnt;
        }
        $rs1->free();

        $sql2 = 'SELECT count(*) as cnt FROM uploadethnodata WHERE collid = '.$this->collId.' AND etheventid IS NOT NULL ';
        $rs2 = $this->conn->query($sql2);
        while($r2 = $rs2->fetch_object()){
            $this->statArr['exist'] = $r2->cnt;
            $this->statArr['new'] = $this->statArr['total'] - $this->statArr['exist'];
        }
        $rs2->free();

        $sql3 = 'SELECT count(*) as cnt FROM uploadethnodata WHERE collid = '.$this->collId.' AND ((ISNULL(occid) AND ISNULL(etheventid)) OR ISNULL(ethPerID)) ';
        $rs3 = $this->conn->query($sql3);
        while($r3 = $rs3->fetch_object()){
            $this->statArr['bad'] = $r3->cnt;
        }
        $rs3->free();
    }

	public function transferSynopticUpload(){
		$this->outputMsg('Starting data transfer...');

		$this->outputMsg('Updating existing records... ');
        $sql = 'UPDATE ethnodataevent AS de LEFT JOIN uploadethnodataevent AS ue ON de.etheventid = ue.etheventid '.
            'SET de.occid = ue.occid, de.ethComID = ue.ethComID, de.datasource = ue.datasource, de.namedatadiscussion = ue.namedatadiscussion, '.
            'de.usedatadiscussion = ue.usedatadiscussion, de.consultantdiscussion = ue.consultantdiscussion, de.eventdate = ue.eventdate, de.eventlocation = ue.eventlocation '.
            'WHERE ue.collid = '.$this->collId.' AND ue.etheventid IS NOT NULL ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }

        $this->outputMsg('Adding new records... ');
        $sql = 'INSERT INTO ethnodataevent(collid,occid,ethComID,datasource,eventdate,eventlocation,namedatadiscussion,usedatadiscussion,consultantdiscussion) '.
            'SELECT DISTINCT collid, occid, ethComID, datasource, eventdate, eventlocation, namedatadiscussion, usedatadiscussion, consultantdiscussion '.
            'FROM uploadethnodataevent '.
            'WHERE collid = '.$this->collId.' AND ISNULL(etheventid) ';
        if(!$this->conn->query($sql)){
            $this->outputMsg('ERROR: '.$this->conn->error,1);
        }
		$this->outputMsg('Done! ');
	}

    public function transferVernacularUpload(){
        global $SYMB_UID;
        $this->outputMsg('Starting data transfer...');

        $this->outputMsg('Priming records to upload... ');
        $uploadsql = 'SELECT DISTINCT occid,etheventid,tid,ethPerID,refpages,verbatimVernacularName,annotatedVernacularName,verbatimLanguage,'.
            'languageGlottologId,otherVerbatimVernacularName,otherLanguageGlottologId,verbatimParse,annotatedParse,verbatimGloss,'.
            'annotatedGloss,typology,`translation`,taxonomicDescription,nameDiscussion,consultantComments,useDiscussion '.
            'FROM uploadethnodata '.
            'WHERE collid = '.$this->collId.' AND (occid IS NOT NULL OR etheventid IS NOT NULL) AND ethPerID IS NOT NULL ';
        //echo $uploadsql;
        if($rs = $this->conn->query($uploadsql)){
            while($row = $rs->fetch_object()){
                $uoccid = $row->occid;
                $uetheventid = $row->etheventid;
                $utid = $row->tid;
                $uethPerID = $row->ethPerID;
                $urefpages = $this->conn->real_escape_string($row->refpages);
                $uverbatimVernacularName = $this->conn->real_escape_string($row->verbatimVernacularName);
                $uannotatedVernacularName = $this->conn->real_escape_string($row->annotatedVernacularName);
                $uverbatimLanguage = $this->conn->real_escape_string($row->verbatimLanguage);
                $ulanguageGlottologId = $row->languageGlottologId;
                $uotherVerbatimVernacularName = $this->conn->real_escape_string($row->otherVerbatimVernacularName);
                $uotherLanguageGlottologId = $row->otherLanguageGlottologId;
                $uverbatimParse = $this->conn->real_escape_string($row->verbatimParse);
                $uannotatedParse = $this->conn->real_escape_string($row->annotatedParse);
                $uverbatimGloss = $this->conn->real_escape_string($row->verbatimGloss);
                $uannotatedGloss = $this->conn->real_escape_string($row->annotatedGloss);
                $utypology = $row->typology;
                $utranslation = $this->conn->real_escape_string($row->translation);
                $utaxonomicDescription = $this->conn->real_escape_string($row->taxonomicDescription);
                $unameDiscussion = $this->conn->real_escape_string($row->nameDiscussion);
                $uconsultantComments = $this->conn->real_escape_string($row->consultantComments);
                $uuseDiscussion = $this->conn->real_escape_string($row->useDiscussion);
                if(!$uetheventid){
                    $sql = 'SELECT etheventid FROM ethnodataevent WHERE occid = '.$uoccid.' ';
                    if($rs2 = $this->conn->query($sql)){
                        if($row = $rs2->fetch_object()){
                            $uetheventid = $row->etheventid;
                        }
                        else{
                            $sql2 = 'INSERT INTO ethnodataevent(collid,occid,eventdate,eventlocation,datasource) '.
                                'SELECT '.$this->collId.', '.$uoccid.', eventDate, CONCAT_WS("; ",country,stateProvince,locality), "elicitation" '.
                                'FROM omoccurrences '.
                                'WHERE occid = '.$uoccid.' ';
                            if($this->conn->query($sql2)){
                                $uetheventid = $this->conn->insert_id;
                            }
                            else{
                                $this->outputMsg('ERROR loading data: '.$this->conn->error);
                            }
                        }
                    }
                    else{
                        $this->outputMsg('ERROR loading data: '.$this->conn->error);
                    }
                }
                if($uetheventid){
                    $sql = 'INSERT INTO ethnodata(occid,etheventid,tid,refpages,verbatimVernacularName,annotatedVernacularName,verbatimLanguage,langId,otherVerbatimVernacularName,otherLangId,verbatimParse,annotatedParse,verbatimGloss,annotatedGloss,typology,`translation`,taxonomicDescription,nameDiscussion,consultantComments,useDiscussion) '.
                        'VALUES ('.$uoccid.','.$uetheventid.','.($utid?$utid:'null').','.($urefpages?'"'.$urefpages.'"':'null').','.
                        ($uverbatimVernacularName?'"'.$uverbatimVernacularName.'"':'null').','.($uannotatedVernacularName?'"'.$uannotatedVernacularName.'"':'null').','.($uverbatimLanguage?'"'.$uverbatimLanguage.'"':'null').','.
                        ($ulanguageGlottologId?'"'.$ulanguageGlottologId.'"':'null').','.($uotherVerbatimVernacularName?'"'.$uotherVerbatimVernacularName.'"':'null').','.($uotherLanguageGlottologId?'"'.$uotherLanguageGlottologId.'"':'null').','.
                        ($uverbatimParse?'"'.$uverbatimParse.'"':'null').','.($uannotatedParse?'"'.$uannotatedParse.'"':'null').','.($uverbatimGloss?'"'.$uverbatimGloss.'"':'null').','.
                        ($uannotatedGloss?'"'.$uannotatedGloss.'"':'null').','.($utypology?'"'.$utypology.'"':'null').','.($utranslation?'"'.$utranslation.'"':'null').','.
                        ($utaxonomicDescription?'"'.$utaxonomicDescription.'"':'null').','.($unameDiscussion?'"'.$unameDiscussion.'"':'null').','.($uconsultantComments?'"'.$uconsultantComments.'"':'null').','.
                        ($uuseDiscussion?'"'.$uuseDiscussion.'"':'null').')';
                    if($this->conn->query($sql)){
                        $recordId = $this->conn->insert_id;
                        if($uethPerID){
                            $sql2 = 'INSERT INTO ethnodatapersonnellink(ethdid,ethPerID) '.
                                'VALUES('.$recordId.','.$uethPerID.') ';
                            if(!$this->conn->query($sql2)){
                                $this->outputMsg('ERROR loading data: '.$this->conn->error);
                            }
                        }
                    }
                    else{
                        $this->outputMsg('ERROR loading data: '.$this->conn->error);
                    }
                }
            }
            $rs->close();
        }

        $this->outputMsg('Done! ');
    }

	public function exportSynopticUpload(){
		$fieldArr = array('ethComID','communityname','recordNumber','datasource','eventdate','eventlocation','namedatadiscussion','usedatadiscussion','consultantdiscussion');
		$fileName = 'termUpload_'.time().'.csv';
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$sql = 'SELECT '.implode(',',$fieldArr).' FROM uploadethnodataevent WHERE collid = '.$this->collId.' ';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$out = fopen('php://output', 'wb');
			echo implode(',',$fieldArr)."\n";
			while($r = $rs->fetch_assoc()){
				fputcsv($out, $r);
			}
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
		$rs->free();
	}

    public function exportVernacularUpload(){
        $fieldArr = array('consultantName','recordNumber','refpages','verbatimVernacularName','annotatedVernacularName','verbatimLanguage',
            'languageGlottologId','otherVerbatimVernacularName','otherLanguageGlottologId','verbatimParse','annotatedParse',
            'verbatimGloss','annotatedGloss','typology','translation','taxonomicDescription','nameDiscussion','consultantComments','useDiscussion');
        $fileName = 'termUpload_'.time().'.csv';
        header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header ('Content-Type: text/csv');
        header ('Content-Disposition: attachment; filename="'.$fileName.'"');
        $sql = 'SELECT '.implode(',',$fieldArr).' FROM uploadethnodata WHERE collid = '.$this->collId.' ';
        $rs = $this->conn->query($sql);
        if($rs->num_rows){
            $out = fopen('php://output', 'wb');
            echo implode(',',$fieldArr)."\n";
            while($r = $rs->fetch_assoc()){
                fputcsv($out, $r);
            }
            fclose($out);
        }
        else{
            echo "Recordset is empty.\n";
        }
        $rs->free();
    }

	//Misc get data functions
	private function setSynopticUploadCount(){
		$sql = 'SELECT COUNT(*) AS cnt FROM uploadethnodataevent WHERE collid = '.$this->collId.' ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->statArr['upload'] = $r->cnt;
		}
		$rs->free();
	}

    private function setVernacularUploadCount(){
        $sql = 'SELECT COUNT(*) AS cnt FROM uploadethnodata WHERE collid = '.$this->collId.' ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $this->statArr['upload'] = $r->cnt;
        }
        $rs->free();
    }

    private function getConsultantArr(){
        $returnArr = array();
	    $sql = 'SELECT DISTINCT p.ethPerID, CONCAT_WS(" ",p.title,p.firstname,p.lastname) AS fullName, l.targetLanguage '.
            'FROM ethnocollperlink AS l LEFT JOIN ethnopersonnel AS p ON l.perID = p.ethPerID '.
            'WHERE l.collID = '.$this->collId.' ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $returnArr[$r->fullName]['id'] = $r->ethPerID;
            $returnArr[$r->fullName]['langid'] = $r->targetLanguage;
        }
        $rs->free();

        return $returnArr;
    }

	public function getFieldArr($type){
		$fieldArr = array();
		$targetFieldArr = array();
		if($type === 'synoptic'){
            $targetFieldArr = $this->getUploadSynopticFieldArr();
        }
        if($type === 'vernacular'){
            $targetFieldArr = $this->getUploadVernacularFieldArr();
        }
        if($targetFieldArr){
            $fh = fopen($this->uploadTargetPath.$this->uploadFileName, 'rb') or die("Can't open file");
            $headerArr = fgetcsv($fh);
            foreach($headerArr as $field){
                $fieldStr = strtolower(trim($field));
                if($fieldStr){
                    $fieldArr['source'][] = $fieldStr;
                    $fieldArr['target'] = $targetFieldArr;
                }
                else{
                    break;
                }
            }
        }
		return $fieldArr;
	}

	private function getUploadSynopticFieldArr(){
		//Get metadata
		$targetArr = array();
		$sql = 'SHOW COLUMNS FROM uploadethnodataevent';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if($field !== 'collid' && $field !== 'occid' && $field !== 'etheventid' && $field !== 'ethcomid' && $field !== 'refid' && $field !== 'initialtimestamp'){
				$targetArr[$field] = $field;
			}
		}
		$rs->free();

		return $targetArr;
	}

    private function getUploadVernacularFieldArr(){
        //Get metadata
        $targetArr = array();
        $sql = 'SHOW COLUMNS FROM uploadethnodata';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $field = strtolower($row->Field);
            if($field !== 'collid' && $field !== 'occid' && $field !== 'etheventid' && $field !== 'tid' && $field !== 'initialtimestamp' && $field !== 'ethperid'){
                $targetArr[$field] = $field;
            }
        }
        $rs->free();

        return $targetArr;
    }

	//Setters and getters
	private function setUploadTargetPath(){
		$tPath = $GLOBALS['tempDirRoot'];
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath && isset($GLOBALS['TEMP_DIR_ROOT'])){
			$tPath = $GLOBALS['TEMP_DIR_ROOT'];
		}
		if(!$tPath){
			$tPath = $GLOBALS['SERVER_ROOT'];
			if(substr($tPath,-1) !== '/') {
				$tPath .= '/';
			}
			$tPath .= 'temp/downloads';
		}
		if(substr($tPath,-1) !== '/') {
			$tPath .= '/';
		}
		$this->uploadTargetPath = $tPath;
	}

	public function setFileName($fName){
		$this->uploadFileName = $fName;
	}

    public function setCollId($id){
        $this->collId = $id;
    }

	public function getFileName(){
		return $this->uploadFileName;
	}

	public function getStatArr(){
		return $this->statArr;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	public function setVerboseMode($vMode){
		global $SERVER_ROOT;
		if(is_numeric($vMode)){
			$this->verboseMode = $vMode;
			if($this->verboseMode === 2){
				//Create log File
				$logPath = $SERVER_ROOT;
				if(substr($SERVER_ROOT,-1) !== '/' && substr($SERVER_ROOT,-1) !== '\\') {
					$logPath .= '/';
				}
				$logPath .= 'temp/logs/glossaryloader_'.date('Ymd').'.log';
				$this->logFH = fopen($logPath, 'ab');
				fwrite($this->logFH,'Start time: '.date('Y-m-d h:i:s A')."\n");
			}
		}
	}

	//Misc functions
	private function outputMsg($str, $indent = 0){
		if($this->verboseMode > 0 || strpos($str, 'ERROR') === 0){
			echo '<li style="margin-left:'.(10*$indent).'px;'.(strpos($str, 'ERROR') === 0 ?'color:red':'').'">'.$str.'</li>';
			ob_flush();
			flush();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fwrite($this->logFH, ($indent ? str_repeat("\t", $indent) : '') . strip_tags($str) . "\n");
		}
	}

	private function cleanInArr(&$inArr){
		foreach($inArr as $k => $v){
			$inArr[$k] = $this->cleanInStr($v);
		}
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}

	private function encodeArr(&$inArr){
		foreach($inArr as $k => $v){
			$inArr[$k] = htmlentities($v);
		}
	}

	private function encodeString($inStr){
		global $CHARSET;
		$retStr = $inStr;
		//Get rid of Windows curly (smart) quotes
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr= str_replace($search, $replace, $inStr);
		//Get rid of UTF-8 curly smart quotes and dashes
		$badwordchars=array("\xe2\x80\x98", // left single quote
							"\xe2\x80\x99", // right single quote
							"\xe2\x80\x9c", // left double quote
							"\xe2\x80\x9d", // right double quote
							"\xe2\x80\x94", // em dash
							"\xe2\x80\xa6" // elipses
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$inStr = str_replace($badwordchars, $fixedwordchars, $inStr);

		if($inStr){
			$cs = strtolower($CHARSET);
			if($cs === 'utf-8' || $cs === 'utf8'){
				//$this->outputMsg($inStr.': '.mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true);
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
					//$retStr = iconv("ISO-8859-1//TRANSLIT","UTF-8",$inStr);
				}
			}
			elseif($cs === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
					//$retStr = iconv("UTF-8","ISO-8859-1//TRANSLIT",$inStr);
				}
			}
 		}
		return $retStr;
	}
}
