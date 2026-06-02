<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class EthnoDataManager {

    private $conn;
    private $collid;
    private $occid;
    private $eventid;
    private $tid;
    private $kingdomid;
    private $linkageSqlWhere;
    private $dataid;

    public function __construct(){
        $this->conn = MySQLiConnectionFactory::getCon("write");
    }

    public function __destruct(){
        if(!($this->conn === null)) $this->conn->close();
    }

    public function setCollid($id){
        if(is_numeric($id)){
            $this->collid = $id;
            return true;
        }
        return false;
    }

    public function setOccid($id){
        if(is_numeric($id)){
            $this->occid = $id;
            return true;
        }
        return false;
    }

    public function setTid($id){
        if(is_numeric($id)){
            $this->tid = $id;
            return true;
        }
        return false;
    }

    public function setEventid($id){
        if(is_numeric($id)){
            $this->eventid = $id;
            return true;
        }
        return false;
    }

    public function getTid(){
        return $this->tid;
    }

    public function getDataId(){
        return $this->dataid;
    }

    public function getKingdomId(){
        return $this->kingdomid;
    }

    public function getEventId(){
        return $this->eventid;
    }

    public function getPersonnelArr(){
        $returnArr = array();
        $sql = 'SELECT p.ethPerID, cp.projectCode, cp.defaultdisplay, p.title, p.firstname, p.lastname '.
            'FROM ethnocollperlink AS cp LEFT JOIN ethnopersonnel AS p ON cp.perID = p.ethPerID '.
            'WHERE cp.collID = '.$this->collid.' '.
            'ORDER BY p.title, p.firstname, p.lastname ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->ethPerID]['projectCode'] = $row->projectCode;
                $returnArr[$row->ethPerID]['defaultdisplay'] = $row->defaultdisplay;
                $returnArr[$row->ethPerID]['title'] = $row->title;
                $returnArr[$row->ethPerID]['firstname'] = $row->firstname;
                $returnArr[$row->ethPerID]['lastname'] = $row->lastname;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getTaxaPersonnelArr($tid){
        $returnArr = array();
        $sql = 'SELECT p.ethPerID, p.title, p.firstname, p.lastname, d.tid, o.tidinterpreted '.
            'FROM ethnodatapersonnellink AS dp LEFT JOIN ethnopersonnel AS p ON dp.ethPerID = p.ethPerID '.
            'LEFT JOIN ethnodata AS d ON dp.ethdid = d.ethdid '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE d.tid = '.$tid.' OR o.tidinterpreted = '.$tid.' '.
            'ORDER BY p.title, p.firstname, p.lastname ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->ethPerID]['title'] = $row->title;
                $returnArr[$row->ethPerID]['firstname'] = $row->firstname;
                $returnArr[$row->ethPerID]['lastname'] = $row->lastname;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getSearchPersonnelArr(){
        $returnArr = array();
        $sql = 'SELECT p.ethPerID, p.title, p.firstname, p.lastname, d.tid, o.tidinterpreted '.
            'FROM ethnodatapersonnellink AS dp LEFT JOIN ethnopersonnel AS p ON dp.ethPerID = p.ethPerID '.
            'LEFT JOIN ethnodata AS d ON dp.ethdid = d.ethdid '.
            'ORDER BY p.title, p.firstname, p.lastname ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->ethPerID]['title'] = $row->title;
                $returnArr[$row->ethPerID]['firstname'] = $row->firstname;
                $returnArr[$row->ethPerID]['lastname'] = $row->lastname;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getCommunityArr(){
        $returnArr = array();
        $sql = 'SELECT p.ethComID, p.communityname '.
            'FROM ethnocollcommlink AS cp LEFT JOIN ethnocommunity AS p ON cp.commID = p.ethComID '.
            'WHERE cp.collID = '.$this->collid.' '.
            'ORDER BY p.communityname ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->ethComID]['commID'] = $row->ethComID;
                $returnArr[$row->ethComID]['communityname'] = $row->communityname;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getReferenceArr(){
        $returnArr = array();
        $sql = 'SELECT r.refid, r.title '.
            'FROM ethnocollreflink AS cr LEFT JOIN referenceobject AS r ON cr.refid = r.refid '.
            'WHERE cr.collID = '.$this->collid.' '.
            'ORDER BY r.title ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->refid]['refid'] = $row->refid;
                $returnArr[$row->refid]['title'] = $row->title;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function createDataCollectionEventRecord($pArr){
        $commId = (array_key_exists('ethComID',$pArr)?$pArr['ethComID']:'');
        $valueStr = 'VALUES (';
        $valueStr .= $pArr['collid'].',';
        $valueStr .= ($pArr['occid']?$pArr['occid']:'null').',';
        $valueStr .= ($pArr['datasource']==='reference'&&$pArr['refid']?$pArr['refid'].',':'null,');
        $valueStr .= ($pArr['datasource']!=='reference'&&$commId?$commId.',':'null,');
        $valueStr .= ($this->cleanInStr($pArr['datasource'])?'"'.$this->cleanInStr($pArr['datasource']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['eventdate'])?'"'.$this->cleanInStr($pArr['eventdate']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['eventlocation'])?'"'.$this->cleanInStr($pArr['eventlocation']).'") ':'null) ');
        $sql = 'INSERT INTO ethnodataevent(collid,occid,refid,ethComID,datasource,eventdate,eventlocation) '.$valueStr;
        if($this->conn->query($sql)){
            $this->eventid = $this->conn->insert_id;
            if($pArr['datasource']==='elicitation'){
                $personnel = (array_key_exists('consultant',$pArr)?$pArr['consultant']:array());
                if($personnel){
                    foreach($personnel as $id){
                        $this->createDataEventPersonnelRecord($id,$this->eventid);
                    }
                }
            }
        }
    }

    public function createDataEventPersonnelRecord($pId,$deId){
        $valueStr = 'VALUES (';
        $valueStr .= $deId.',';
        $valueStr .= $pId.') ';
        $sql = 'INSERT INTO ethnodataeventperlink(etheventid,ethPerID) '.$valueStr;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function saveDataEventRecordChanges($pArr){
        $this->eventid = $pArr['eventid'];
        $this->clearDataEventPersonnelRecords();
        $valueStr = 'SET ';
        $valueStr .= 'refid='.($pArr['datasource']==='reference'&&$pArr['refid']?$pArr['refid']:'null').',';
        $valueStr .= 'ethComID='.($pArr['datasource']!=='reference'&&$pArr['ethComID']?$pArr['ethComID']:'null').',';
        $valueStr .= 'datasource='.($this->cleanInStr($pArr['datasource'])?'"'.$this->cleanInStr($pArr['datasource']).'"':'null').',';
        $valueStr .= 'eventdate='.($this->cleanInStr($pArr['eventdate'])?'"'.$this->cleanInStr($pArr['eventdate']).'"':'null').',';
        $valueStr .= 'eventlocation='.($this->cleanInStr($pArr['eventlocation'])?'"'.$this->cleanInStr($pArr['eventlocation']).'"':'null').',';
        $valueStr .= 'namedatadiscussion='.($this->cleanInStr($pArr['namedatadiscussion'])?'"'.$this->cleanInStr($pArr['namedatadiscussion']).'"':'null').',';
        $valueStr .= 'usedatadiscussion='.($this->cleanInStr($pArr['usedatadiscussion'])?'"'.$this->cleanInStr($pArr['usedatadiscussion']).'"':'null').',';
        $valueStr .= 'consultantdiscussion='.($this->cleanInStr($pArr['consultantdiscussion'])?'"'.$this->cleanInStr($pArr['consultantdiscussion']).'"':'null');
        $sql = 'UPDATE ethnodataevent '.$valueStr.' '.
            'WHERE etheventid = '.$this->eventid.' ';
        if($this->conn->query($sql)){
            $personnel = $pArr['consultant'];
            foreach($personnel as $id){
                $this->createDataEventPersonnelRecord($id,$this->eventid);
            }
        };
    }

    public function deleteDataEvent($pArr){
        $this->eventid = $pArr['eventid'];
        $this->clearDataEventPersonnelRecords();
        $sql = 'DELETE FROM ethnodataevent WHERE etheventid = '.$this->eventid;
        if($this->conn->query($sql)){
            return true;
        };
        return false;
    }

    public function clearDataEventPersonnelRecords(){
        $sql = 'DELETE FROM ethnodataeventperlink WHERE etheventid = '.$this->eventid;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function getDataEventDetailArr(){
        $returnArr = array();
        $sql = 'SELECT occid, etheventid, refid, ethComID, datasource, eventdate, eventlocation, namedatadiscussion, usedatadiscussion, consultantdiscussion '.
            'FROM ethnodataevent '.
            'WHERE etheventid = '.$this->eventid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->etheventid;
                $returnArr['eventid'] = $recId;
                $returnArr['occid'] = $row->occid;
                $returnArr['personnelArr'] = $this->getDataEventRecordPersonnelArr($recId);
                $returnArr['refid'] = $row->refid;
                $returnArr['ethComID'] = $row->ethComID;
                $returnArr['datasource'] = $row->datasource;
                $returnArr['eventdate'] = $row->eventdate;
                $returnArr['eventlocation'] = $row->eventlocation;
                $returnArr['namedatadiscussion'] = $row->namedatadiscussion;
                $returnArr['usedatadiscussion'] = $row->usedatadiscussion;
                $returnArr['consultantdiscussion'] = $row->consultantdiscussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataEventRecordPersonnelArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethPerID FROM ethnodataeventperlink WHERE etheventid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethPerID;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataEventArr(){
        $returnArr = array();
        $sql = 'SELECT d.etheventid, d.eventdate, d.eventlocation, d.datasource, r.title, o.recordNumber '.
            'FROM ethnodataevent AS d LEFT JOIN referenceobject AS r ON d.refid = r.refid '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE d.collid = '.$this->collid.' ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                if($row->datasource === 'elicitation'){
                    if($row->recordNumber){
                        $label = 'Collection '.$row->recordNumber.': ';
                    }
                    else{
                        $label = 'Record '.$row->etheventid.': ';
                    }
                    if($row->eventdate) {
                        $label .= $row->eventdate . ': ';
                    }
                    if($row->eventlocation) {
                        $label .= $row->eventlocation . ': ';
                    }
                    $label .= $this->getEventConsultantStr($row->etheventid);
                    $returnArr[$row->etheventid]['etheventid'] = $row->etheventid;
                    $returnArr[$row->etheventid]['label'] = $label;
                }
                else{
                    $returnArr[$row->etheventid]['etheventid'] = $row->etheventid;
                    $returnArr[$row->etheventid]['label'] = $row->title;
                }
            }
            $rs->close();
        }
        asort($returnArr);
        return $returnArr;
    }

    public function getEventConsultantStr($id){
        $consultantArr = array();
        $sql = 'SELECT CONCAT_WS(" ",p.title,p.firstname,p.lastname) AS fullName '.
            'FROM ethnodataeventperlink AS l LEFT JOIN ethnopersonnel AS p ON l.ethPerID = p.ethPerID '.
            'WHERE l.etheventid = '.$id.' ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $consultantArr[] = $row->fullName;
            }
            $rs->close();
        }
        return implode(", ",$consultantArr);
    }

    public function getNameSemanticTagArr(){
        $returnArr = array();
        $sql = 'SELECT t1.ethTagId AS parentID, t1.tag AS parentTag, t1.description AS parentDesc, t2.ethTagId AS childID, '.
            't2.tag AS childTag, t2.description AS childDesc '.
            'FROM ethnonamesemantictags AS t1 LEFT JOIN ethnonamesemantictags AS t2 ON t1.ethTagId = t2.parentTagId '.
            'WHERE ISNULL(t1.parentTagId) '.
            'ORDER BY t1.sortsequence, t2.sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                if(!array_key_exists($row->parentID,$returnArr)){
                    $returnArr[$row->parentID]['ptag'] = $row->parentTag;
                    $returnArr[$row->parentID]['pdesc'] = $row->parentDesc;
                }
                if($row->childID){
                    $returnArr[$row->parentID][$row->childID]['ctag'] = $row->childTag;
                    $returnArr[$row->parentID][$row->childID]['cdesc'] = $row->childDesc;
                }
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getOccCollid($occid){
        $collid = 0;
        $sql = 'SELECT collid '.
            'FROM omoccurrences '.
            'WHERE occid = '.$occid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $collid = $row->collid;
            }
            $rs->close();
        }
        return $collid;
    }

    public function getPersonnelTargetLanguage($perId,$collId){
        $langId = '';
        $sql = 'SELECT targetLanguage '.
            'FROM ethnocollperlink '.
            'WHERE collID = '.$collId.' AND perID = '.$perId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $langId = $row->targetLanguage;
            }
            $rs->close();
        }
        return $langId;
    }

    public function getOccDataArr(){
        $returnArr = array();
        $sql = 'SELECT d.ethdid, d.verbatimVernacularName, d.langId, g.id, g.iso639P3code, g.`name`, d.verbatimParse, '.
            'd.annotatedVernacularName, d.verbatimLanguage, d.annotatedParse, d.typology, d.translation, d.tid, '.
            'd.annotatedGloss, d.taxonomicDescription, d.refpages, r.title, d.verbatimGloss, d.nameDiscussion, o.sciname, '.
            'CONCAT_WS(", ",de.eventdate,de.eventlocation) AS dataEventStr, g2.id AS id2, g2.iso639P3code AS iso639P3code2, '.
            'g2.`name` AS name2, d.otherVerbatimVernacularName, d.otherLangId, d.consultantComments, d.useDiscussion, o.tidinterpreted '.
            'FROM ethnodata AS d LEFT JOIN glottolog AS g ON d.langId = g.id '.
            'LEFT JOIN glottolog AS g2 ON d.otherLangId = g2.id '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'LEFT JOIN ethnodataevent AS de ON d.etheventid = de.etheventid '.
            'LEFT JOIN referenceobject AS r ON de.refid = r.refid '.
            'WHERE d.occid = '.$this->occid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethdid;
                $tid = ($row->tid?$row->tid:$row->tidinterpreted);
                $langName = ($row->id?$row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name:'');
                $langName2 = ($row->id2?$row->id2.' | '.($row->iso639P3code2?$row->iso639P3code2:'[No ISO code]').' | '.$row->name2:'');
                $returnArr[$recId]['personnelArr'] = $this->getDataRecordPersonnelArr($recId);
                $returnArr[$recId]['semanticTags'] = $this->getDataRecordSemanticsArr($recId);
                $returnArr[$recId]['partsTags'] = $this->getDataRecordPartsArr($recId);
                $returnArr[$recId]['useTags'] = $this->getDataRecordUsesArr($recId);
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['annotatedVernacularName'] = $row->annotatedVernacularName;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['langId'] = $row->langId;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['kingdomId'] = $this->getUseEditKingdomId($tid);
                $returnArr[$recId]['otherVerbatimVernacularName'] = $row->otherVerbatimVernacularName;
                $returnArr[$recId]['otherLangId'] = $row->otherLangId;
                $returnArr[$recId]['otherLangName'] = $langName2;
                $returnArr[$recId]['verbatimLanguage'] = $row->verbatimLanguage;
                $returnArr[$recId]['verbatimParse'] = $row->verbatimParse;
                $returnArr[$recId]['verbatimGloss'] = $row->verbatimGloss;
                $returnArr[$recId]['annotatedParse'] = $row->annotatedParse;
                $returnArr[$recId]['annotatedGloss'] = $row->annotatedGloss;
                $returnArr[$recId]['taxonomicDescription'] = $row->taxonomicDescription;
                $returnArr[$recId]['typology'] = $row->typology;
                $returnArr[$recId]['translation'] = $row->translation;
                $returnArr[$recId]['nameDiscussion'] = $row->nameDiscussion;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['reftitle'] = $row->title;
                $returnArr[$recId]['dataeventstr'] = $row->dataEventStr;
                $returnArr[$recId]['consultantComments'] = $row->consultantComments;
                $returnArr[$recId]['useDiscussion'] = $row->useDiscussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataArr(){
        $returnArr = array();
        $sql = 'SELECT d.ethdid, d.verbatimVernacularName, d.langId, d.tid, g.id, g.iso639P3code, g.`name`, d.verbatimParse, '.
            'd.annotatedVernacularName, d.verbatimLanguage, d.annotatedParse, d.typology, d.translation, '.
            'd.annotatedGloss, d.taxonomicDescription, d.refpages, r.title, d.verbatimGloss, d.nameDiscussion, '.
            'CONCAT_WS(" ",o.catalogNumber,o.recordedBy,o.recordNumber) AS occStr, IFNULL(t.SciName,o.sciname) AS sciname, '.
            'g2.id AS id2, g2.iso639P3code AS iso639P3code2, g2.`name` AS name2, d.otherVerbatimVernacularName, d.otherLangId, '.
            'd.consultantComments, d.useDiscussion, o.tidinterpreted, CONCAT_WS(", ",de.eventdate,de.eventlocation) AS dataEventStr '.
            'FROM ethnodata AS d LEFT JOIN glottolog AS g ON d.langId = g.id '.
            'LEFT JOIN glottolog AS g2 ON d.otherLangId = g2.id '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'LEFT JOIN taxa AS t ON d.tid = t.TID '.
            'LEFT JOIN ethnodataevent AS de ON d.etheventid = de.etheventid '.
            'LEFT JOIN referenceobject AS r ON de.refid = r.refid '.
            'WHERE d.etheventid = '.$this->eventid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethdid;
                $tid = ($row->tid?$row->tid:$row->tidinterpreted);
                $langName = ($row->id?$row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name:'');
                $langName2 = ($row->id2?$row->id2.' | '.($row->iso639P3code2?$row->iso639P3code2:'[No ISO code]').' | '.$row->name2:'');
                $returnArr[$recId]['personnelArr'] = $this->getDataRecordPersonnelArr($recId);
                $returnArr[$recId]['semanticTags'] = $this->getDataRecordSemanticsArr($recId);
                $returnArr[$recId]['partsTags'] = $this->getDataRecordPartsArr($recId);
                $returnArr[$recId]['useTags'] = $this->getDataRecordUsesArr($recId);
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['kingdomId'] = $this->getUseEditKingdomId($tid);
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['langId'] = $row->langId;
                $returnArr[$recId]['otherVerbatimVernacularName'] = $row->otherVerbatimVernacularName;
                $returnArr[$recId]['otherLangId'] = $row->otherLangId;
                $returnArr[$recId]['otherLangName'] = $langName2;
                $returnArr[$recId]['verbatimParse'] = $row->verbatimParse;
                $returnArr[$recId]['verbatimGloss'] = $row->verbatimGloss;
                $returnArr[$recId]['typology'] = $row->typology;
                $returnArr[$recId]['translation'] = $row->translation;
                $returnArr[$recId]['nameDiscussion'] = $row->nameDiscussion;
                $returnArr[$recId]['annotatedVernacularName'] = $row->annotatedVernacularName;
                $returnArr[$recId]['verbatimLanguage'] = $row->verbatimLanguage;
                $returnArr[$recId]['annotatedParse'] = $row->annotatedParse;
                $returnArr[$recId]['annotatedGloss'] = $row->annotatedGloss;
                $returnArr[$recId]['taxonomicDescription'] = $row->taxonomicDescription;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['reftitle'] = $row->title;
                $returnArr[$recId]['occstr'] = $row->occStr;
                $returnArr[$recId]['consultantComments'] = $row->consultantComments;
                $returnArr[$recId]['useDiscussion'] = $row->useDiscussion;
                $returnArr[$recId]['dataeventstr'] = $row->dataEventStr;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getTaxaDataArr($tid){
        $returnArr = array();
        $sql = 'SELECT d.ethdid, d.verbatimVernacularName, d.langId, g.id, g.iso639P3code, g.`name`, d.verbatimParse, '.
            'd.annotatedVernacularName, d.verbatimLanguage, d.annotatedParse, d.typology, d.translation, d.tid, '.
            'd.annotatedGloss, d.taxonomicDescription, d.refpages, r.title, d.verbatimGloss, d.nameDiscussion, o.sciname, '.
            'CONCAT_WS(", ",de.eventdate,de.eventlocation) AS dataEventStr, g2.id AS id2, g2.iso639P3code AS iso639P3code2, '.
            'g2.`name` AS name2, d.otherVerbatimVernacularName, d.otherLangId, d.consultantComments, d.useDiscussion, o.tidinterpreted '.
            'FROM ethnodata AS d LEFT JOIN glottolog AS g ON d.langId = g.id '.
            'LEFT JOIN glottolog AS g2 ON d.otherLangId = g2.id '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'LEFT JOIN ethnodataevent AS de ON d.etheventid = de.etheventid '.
            'LEFT JOIN referenceobject AS r ON de.refid = r.refid '.
            'WHERE d.tid = '.$tid.' OR o.tidinterpreted = '.$tid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethdid;
                $tid = ($row->tid?$row->tid:$row->tidinterpreted);
                $langName = ($row->id?$row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name:'');
                $langName2 = ($row->id2?$row->id2.' | '.($row->iso639P3code2?$row->iso639P3code2:'[No ISO code]').' | '.$row->name2:'');
                $returnArr[$recId]['personnelArr'] = $this->getDataRecordPersonnelArr($recId);
                $returnArr[$recId]['semanticTags'] = $this->getDataRecordSemanticsArr($recId);
                $returnArr[$recId]['partsTags'] = $this->getDataRecordPartsArr($recId);
                $returnArr[$recId]['useTags'] = $this->getDataRecordUsesArr($recId);
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['annotatedVernacularName'] = $row->annotatedVernacularName;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['langId'] = $row->langId;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['kingdomId'] = $this->getUseEditKingdomId($tid);
                $returnArr[$recId]['otherVerbatimVernacularName'] = $row->otherVerbatimVernacularName;
                $returnArr[$recId]['otherLangId'] = $row->otherLangId;
                $returnArr[$recId]['otherLangName'] = $langName2;
                $returnArr[$recId]['verbatimLanguage'] = $row->verbatimLanguage;
                $returnArr[$recId]['verbatimParse'] = $row->verbatimParse;
                $returnArr[$recId]['verbatimGloss'] = $row->verbatimGloss;
                $returnArr[$recId]['annotatedParse'] = $row->annotatedParse;
                $returnArr[$recId]['annotatedGloss'] = $row->annotatedGloss;
                $returnArr[$recId]['taxonomicDescription'] = $row->taxonomicDescription;
                $returnArr[$recId]['typology'] = $row->typology;
                $returnArr[$recId]['translation'] = $row->translation;
                $returnArr[$recId]['nameDiscussion'] = $row->nameDiscussion;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['reftitle'] = $row->title;
                $returnArr[$recId]['dataeventstr'] = $row->dataEventStr;
                $returnArr[$recId]['consultantComments'] = $row->consultantComments;
                $returnArr[$recId]['useDiscussion'] = $row->useDiscussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordSemanticsArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethTagId FROM ethnodatanamesemtaglink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethTagId;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordPersonnelArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethPerID FROM ethnodatapersonnellink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethPerID;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordPartsArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethpuid FROM ethnodatausepartslink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethpuid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordUsesArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethuoid FROM ethnodatausetaglink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethuoid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function createDataRecord($pArr){
        $occid = ((array_key_exists('associateoccid',$pArr)&&$pArr['associateoccid'])?$pArr['associateoccid']:0);
        $dataSource = $pArr['datasource'];
        $valueStr = 'VALUES (';
        $valueStr .= ($dataSource==='elicitation'&&$occid?$occid.',':'null,');
        $valueStr .= $pArr['eventid'].',';
        $valueStr .= ($dataSource==='reference'&&$this->cleanInStr($pArr['refpages'])?'"'.$this->cleanInStr($pArr['refpages']).'",':'null,');
        $valueStr .= (($pArr['tid']&&!$occid)?$pArr['tid'].',':'null,');
        $valueStr .= ($this->cleanInStr($pArr['verbatimVernacularName'])?'"'.$this->cleanInStr($pArr['verbatimVernacularName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['languageid'])?'"'.$this->cleanInStr($pArr['languageid']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['verbatimParse'])?'"'.$this->cleanInStr($pArr['verbatimParse']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['verbatimGloss'])?'"'.$this->cleanInStr($pArr['verbatimGloss']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['freetranslation'])?'"'.$this->cleanInStr($pArr['freetranslation']).'",':'null,');
        $valueStr .= ($dataSource==='elicitation'&&$this->cleanInStr($pArr['nameDiscussion'])?'"'.$this->cleanInStr($pArr['nameDiscussion']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['annotatedVernacularName'])?'"'.$this->cleanInStr($pArr['annotatedVernacularName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['verbatimLanguage'])?'"'.$this->cleanInStr($pArr['verbatimLanguage']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['annotatedParse'])?'"'.$this->cleanInStr($pArr['annotatedParse']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['annotatedGloss'])?'"'.$this->cleanInStr($pArr['annotatedGloss']).'",':'null,');
        $valueStr .= ($dataSource==='reference'&&$this->cleanInStr($pArr['taxonomicDescription'])?'"'.$this->cleanInStr($pArr['taxonomicDescription']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['otherVerbatimVernacularName'])?'"'.$this->cleanInStr($pArr['otherVerbatimVernacularName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['otherLangId'])?'"'.$this->cleanInStr($pArr['otherLangId']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['consultantComments'])?'"'.$this->cleanInStr($pArr['consultantComments']).'",':'null,');
        $valueStr .= ($dataSource==='elicitation'&&$this->cleanInStr($pArr['useDiscussion'])?'"'.$this->cleanInStr($pArr['useDiscussion']).'",':'null,');
        $valueStr .= (array_key_exists('typology',$pArr)&&$this->cleanInStr($pArr['typology'])?'"'.$this->cleanInStr($pArr['typology']).'") ':'null) ');
        $sql = 'INSERT INTO ethnodata(occid,etheventid,refpages,tid,verbatimVernacularName,langId,verbatimParse,verbatimGloss,'.
            'translation,nameDiscussion,annotatedVernacularName,verbatimLanguage,annotatedParse,annotatedGloss,taxonomicDescription,'.
            'otherVerbatimVernacularName,otherLangId,consultantComments,useDiscussion,typology) '.$valueStr;
        //echo $sql;
        if($this->conn->query($sql)){
            $this->dataid = $this->conn->insert_id;
            $semantics = (array_key_exists('semantics',$pArr)?$pArr['semantics']:array());
            $parts = (array_key_exists('parts',$pArr)?$pArr['parts']:array());
            $uses = (array_key_exists('uses',$pArr)?$pArr['uses']:array());
            if($dataSource==='elicitation'){
                $personnel = $pArr['consultant'];
                foreach($personnel as $id){
                    $this->createDataPersonnelRecord($id,$this->dataid);
                }
            }
            if($semantics){
                foreach($semantics as $id){
                    $this->createNameSemanticRecord($id,$this->dataid);
                }
            }
            if($parts){
                foreach($parts as $id){
                    $this->createUsePartRecord($id,$this->dataid);
                }
            }
            if($uses){
                foreach($uses as $id){
                    $this->createUseTypeRecord($id,$this->dataid);
                }
            }
        }
    }

    public function saveDataRecordChanges($pArr){
        $occid = ((array_key_exists('associateoccid',$pArr)&&$pArr['associateoccid'])?$pArr['associateoccid']:0);
        $this->dataid = $pArr['dataid'];
        $dataSource = $pArr['datasource'];
        $this->clearDataPersonnelRecords();
        $this->clearNameSemanticRecords();
        $this->clearUsePartRecords();
        $this->clearUseTypeRecords();
        $valueStr = 'SET ';
        $valueStr .= 'occid='.($dataSource==='elicitation'&&$occid?$occid:'null').',';
        $valueStr .= 'tid='.(($pArr['tid']&&!$occid)?$pArr['tid']:'null').',';
        $valueStr .= 'refpages='.($dataSource==='reference'&&$this->cleanInStr($pArr['refpages'])?'"'.$this->cleanInStr($pArr['refpages']).'"':'null').',';
        $valueStr .= 'verbatimVernacularName='.($this->cleanInStr($pArr['verbatimVernacularName'])?'"'.$this->cleanInStr($pArr['verbatimVernacularName']).'"':'null').',';
        $valueStr .= 'langId='.($this->cleanInStr($pArr['languageid'])?'"'.$this->cleanInStr($pArr['languageid']).'"':'null').',';
        $valueStr .= 'otherVerbatimVernacularName='.($this->cleanInStr($pArr['otherVerbatimVernacularName'])?'"'.$this->cleanInStr($pArr['otherVerbatimVernacularName']).'"':'null').',';
        if(array_key_exists('otherlanguageid',$pArr)){
            $valueStr .= 'otherLangId='.($this->cleanInStr($pArr['otherlanguageid'])?'"'.$this->cleanInStr($pArr['otherlanguageid']).'"':'null').',';
        }
        $valueStr .= 'verbatimParse='.($this->cleanInStr($pArr['verbatimParse'])?'"'.$this->cleanInStr($pArr['verbatimParse']).'"':'null').',';
        $valueStr .= 'verbatimGloss='.($this->cleanInStr($pArr['verbatimGloss'])?'"'.$this->cleanInStr($pArr['verbatimGloss']).'"':'null').',';
        $valueStr .= 'translation='.($this->cleanInStr($pArr['freetranslation'])?'"'.$this->cleanInStr($pArr['freetranslation']).'"':'null').',';
        $valueStr .= 'nameDiscussion='.($dataSource==='elicitation'&&$this->cleanInStr($pArr['nameDiscussion'])?'"'.$this->cleanInStr($pArr['nameDiscussion']).'"':'null').',';
        $valueStr .= 'annotatedVernacularName='.($this->cleanInStr($pArr['annotatedVernacularName'])?'"'.$this->cleanInStr($pArr['annotatedVernacularName']).'"':'null').',';
        $valueStr .= 'verbatimLanguage='.($this->cleanInStr($pArr['verbatimLanguage'])?'"'.$this->cleanInStr($pArr['verbatimLanguage']).'"':'null').',';
        $valueStr .= 'annotatedParse='.($this->cleanInStr($pArr['annotatedParse'])?'"'.$this->cleanInStr($pArr['annotatedParse']).'"':'null').',';
        $valueStr .= 'annotatedGloss='.($this->cleanInStr($pArr['annotatedGloss'])?'"'.$this->cleanInStr($pArr['annotatedGloss']).'"':'null').',';
        $valueStr .= 'taxonomicDescription='.($dataSource==='reference'&&$this->cleanInStr($pArr['taxonomicDescription'])?'"'.$this->cleanInStr($pArr['taxonomicDescription']).'"':'null').',';
        $valueStr .= 'consultantComments='.($this->cleanInStr($pArr['consultantComments'])?'"'.$this->cleanInStr($pArr['consultantComments']).'"':'null').',';
        $valueStr .= 'useDiscussion='.($dataSource==='elicitation'&&$this->cleanInStr($pArr['useDiscussion'])?'"'.$this->cleanInStr($pArr['useDiscussion']).'"':'null').',';
        $valueStr .= 'typology='.(array_key_exists('typology',$pArr)&&$this->cleanInStr($pArr['typology'])?'"'.$this->cleanInStr($pArr['typology']).'"':'null');
        $sql = 'UPDATE ethnodata '.$valueStr.' '.
            'WHERE ethdid = '.$this->dataid.' ';
        if($this->conn->query($sql)){
            $semantics = (array_key_exists('semantics',$pArr)?$pArr['semantics']:array());
            $parts = (array_key_exists('parts',$pArr)?$pArr['parts']:array());
            $uses = (array_key_exists('uses',$pArr)?$pArr['uses']:array());
            if($dataSource==='elicitation'){
                $personnel = $pArr['consultant'];
                foreach($personnel as $id){
                    $this->createDataPersonnelRecord($id,$this->dataid);
                }
            }
            if($semantics){
                foreach($semantics as $id){
                    $this->createNameSemanticRecord($id,$this->dataid);
                }
            }
            if($parts){
                foreach($parts as $id){
                    $this->createUsePartRecord($id,$this->dataid);
                }
            }
            if($uses){
                foreach($uses as $id){
                    $this->createUseTypeRecord($id,$this->dataid);
                }
            }
        };
    }

    public function deleteDataRecord($pArr){
        $this->dataid = $pArr['dataid'];
        $this->clearDataPersonnelRecords();
        $this->clearNameSemanticRecords();
        $this->clearUsePartRecords();
        $this->clearUseTypeRecords();
        $sql = 'DELETE FROM ethnodata WHERE ethdid = '.$this->dataid;
        if($this->conn->query($sql)){
            return true;
        };
        return false;
    }

    public function getDataEditArr($dataId){
        $returnArr = array();
        $sql = 'SELECT d.verbatimVernacularName, d.langId, d.verbatimParse, de.datasource, t.SciName, d.tid, '.
            'd.annotatedVernacularName, d.verbatimLanguage, d.annotatedParse, d.typology, d.translation, '.
            'd.annotatedGloss, d.taxonomicDescription, d.refpages, d.verbatimGloss, d.nameDiscussion, d.occid, '.
            'd.consultantComments, d.useDiscussion, d.otherVerbatimVernacularName, d.otherLangId, o.tidinterpreted '.
            'FROM ethnodata AS d LEFT JOIN taxa AS t ON d.tid = t.TID '.
            'LEFT JOIN ethnodataevent AS de ON d.etheventid = de.etheventid '.
            'LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE d.ethdid = '.$dataId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $tid = ($row->tid?$row->tid:$row->tidinterpreted);
                $returnArr['personnelArr'] = $this->getDataRecordPersonnelArr($dataId);
                $returnArr['semanticTags'] = $this->getDataRecordSemanticsArr($dataId);
                $returnArr['partsTags'] = $this->getDataRecordPartsArr($dataId);
                $returnArr['useTags'] = $this->getDataRecordUsesArr($dataId);
                $returnArr['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr['SciName'] = $row->SciName;
                $returnArr['tid'] = $row->tid;
                $returnArr['kingdomId'] = $this->getUseEditKingdomId($tid);
                $returnArr['occid'] = $row->occid;
                $returnArr['langId'] = $row->langId;
                $returnArr['verbatimParse'] = $row->verbatimParse;
                $returnArr['verbatimGloss'] = $row->verbatimGloss;
                $returnArr['typology'] = $row->typology;
                $returnArr['translation'] = $row->translation;
                $returnArr['nameDiscussion'] = $row->nameDiscussion;
                $returnArr['annotatedVernacularName'] = $row->annotatedVernacularName;
                $returnArr['verbatimLanguage'] = $row->verbatimLanguage;
                $returnArr['annotatedParse'] = $row->annotatedParse;
                $returnArr['annotatedGloss'] = $row->annotatedGloss;
                $returnArr['taxonomicDescription'] = $row->taxonomicDescription;
                $returnArr['refpages'] = $row->refpages;
                $returnArr['datasource'] = $row->datasource;
                $returnArr['consultantComments'] = $row->consultantComments;
                $returnArr['useDiscussion'] = $row->useDiscussion;
                $returnArr['otherVerbatimVernacularName'] = $row->otherVerbatimVernacularName;
                $returnArr['otherLangId'] = $row->otherLangId;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function setOccTaxonomy(){
        $sql = 'SELECT DISTINCT o.tidinterpreted, t.TID '.
            'FROM omoccurrences AS o LEFT JOIN taxaenumtree AS te ON o.tidinterpreted = te.tid '.
            'LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'WHERE o.occid = '.$this->occid.' AND te.taxauthid = 1 AND t.RankId = 10 '.
            'AND (t.TID IN(SELECT tid FROM ethnotaxapartsused) OR t.TID IN(SELECT tid FROM ethnotaxauseheaders)) ';
        if($rs = $this->conn->query($sql)){
            if($row = $rs->fetch_object()){
                if($row->tidinterpreted) $this->tid = $row->tidinterpreted;
                if($row->TID) $this->kingdomid = $row->TID;
                else $this->setDefaultkingdomId();
            }
            $rs->close();
            return true;
        }
        return false;
    }

    public function setTaxonomy(){
        $sql = 'SELECT DISTINCT t.TID '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'WHERE te.tid = '.$this->tid.' AND te.taxauthid = 1 AND t.RankId = 10 '.
            'AND (t.TID IN(SELECT tid FROM ethnotaxapartsused) OR t.TID IN(SELECT tid FROM ethnotaxauseheaders)) ';
        if($rs = $this->conn->query($sql)){
            if($row = $rs->fetch_object()){
                if($row->TID) $this->kingdomid = $row->TID;
                else $this->setDefaultkingdomId();
            }
            $rs->close();
            return true;
        }
        return false;
    }

    public function setDefaultkingdomId(){
        $sql = 'SELECT TID FROM taxa WHERE SciName = "Plantae" ';
        if($rs = $this->conn->query($sql)){
            if($row = $rs->fetch_object()){
                if($row->TID) $this->kingdomid = $row->TID;
            }
            $rs->close();
        }
    }

    public function getPartsUsedTagArr($kingdomid){
        $returnArr = array();
        $sql = 'SELECT ethpuid, description '.
            'FROM ethnotaxapartsused '.
            'WHERE tid = '.$kingdomid.' '.
            'ORDER BY sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->ethpuid] = $row->description;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getPartsUsedTagArrFull(){
        $returnArr = array();
        $sql = 'SELECT tid, ethpuid, description '.
            'FROM ethnotaxapartsused '.
            'ORDER BY tid, sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[$row->tid][$row->ethpuid] = $row->description;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getPartsUsedTidArr(){
        $returnArr = array();
        $sql = 'SELECT DISTINCT tid FROM ethnotaxapartsused ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->tid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getUseTagArr($kingdomid){
        $returnArr = array();
        $sql = 'SELECT h.ethuhid, h.headerText, o.ethuoid, o.optionText '.
            'FROM ethnotaxauseheaders AS h LEFT JOIN ethnouseoptions AS o ON h.ethuhid = o.ethuhid '.
            'WHERE h.tid = '.$kingdomid.' '.
            'ORDER BY h.sortsequence, o.sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                if(!array_key_exists($row->ethuhid,$returnArr)) $returnArr[$row->ethuhid]['header'] = $row->headerText;
                $returnArr[$row->ethuhid][$row->ethuoid] = $row->optionText;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getUseTagArrFull(){
        $returnArr = array();
        $sql = 'SELECT h.tid, h.ethuhid, h.headerText, o.ethuoid, o.optionText '.
            'FROM ethnotaxauseheaders AS h LEFT JOIN ethnouseoptions AS o ON h.ethuhid = o.ethuhid '.
            'ORDER BY h.tid, h.sortsequence, o.sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                if(!array_key_exists($row->tid,$returnArr)) $returnArr[$row->tid] = array();
                if(!array_key_exists($row->ethuhid,$returnArr[$row->tid])) $returnArr[$row->tid][$row->ethuhid]['header'] = $row->headerText;
                $returnArr[$row->tid][$row->ethuhid][$row->ethuoid] = $row->optionText;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getUseTidArr(){
        $returnArr = array();
        $sql = 'SELECT DISTINCT tid FROM ethnotaxauseheaders ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->tid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getOccTextStr($showOcc = true){
        $occText = '';
        $sql = 'SELECT o.occid, CONCAT_WS(" ",o.catalogNumber,o.recordedBy,o.recordNumber) AS occStr '.
            'FROM omoccurrences AS o '.
            'WHERE o.occid = '.$this->occid.' ';
        //echo $sql; exit;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($showOcc){
                    $occText = $r->occid.': '.$r->occStr;
                }
                else{
                    $occText = $r->occStr;
                }
            }
            $rs->free();
        }

        return $occText;
    }

    public function getUseEditKingdomId($tId){
        $return = 0;
        $sql = 'SELECT t2.TID AS kingdomId '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
            'WHERE te.tid = '.$tId.' AND te.taxauthid = 1 AND t2.RankId = 10 ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $return = $row->kingdomId;
            }
            $rs->close();
        }
        return $return;
    }

    public function getLinkageArr($dataId){
        $returnArr = array();
        $sql = 'SELECT l.ethlinkid, l.ethdidlink, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid, '.
            'g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName '.
            'FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdidlink = n.ethdid '.
            'LEFT JOIN glottolog AS g ON n.langId = g.id '.
            'LEFT JOIN taxa AS t ON n.tid = t.TID '.
            'LEFT JOIN omoccurrences AS o ON n.occid = o.occid '.
            'LEFT JOIN referenceobject AS r ON l.refid = r.refid '.
            'WHERE l.ethdid = '.$dataId.' OR l.ethdidlink = '.$dataId.' ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethdidlink;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }

        $sql = 'SELECT l.ethlinkid, l.ethnid, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid, '.
            'g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName '.
            'FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdid = n.ethdid '.
            'LEFT JOIN glottolog AS g ON n.langId = g.id '.
            'LEFT JOIN taxa AS t ON n.tid = t.TID '.
            'LEFT JOIN omoccurrences AS o ON n.occid = o.occid '.
            'LEFT JOIN referenceobject AS r ON l.refid = r.refid '.
            'WHERE l.ethdidlink = '.$dataId.' ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethnid;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getOccLinkageArr(){
        $returnArr = array();
        $sql = 'SELECT l.ethlinkid, l.ethnidlink, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid, '.
            'g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName '.
            'FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdidlink = n.ethdid '.
            'LEFT JOIN glottolog AS g ON n.langId = g.id '.
            'LEFT JOIN taxa AS t ON n.tid = t.TID '.
            'LEFT JOIN omoccurrences AS o ON n.occid = o.occid '.
            'LEFT JOIN referenceobject AS r ON l.refid = r.refid '.
            'WHERE n.occid = '.$this->occid.' ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethnidlink;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }

        $sql = 'SELECT l.ethlinkid, l.ethnid, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid, '.
            'g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName '.
            'FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdid = n.ethdid '.
            'LEFT JOIN glottolog AS g ON n.langId = g.id '.
            'LEFT JOIN taxa AS t ON n.tid = t.TID '.
            'LEFT JOIN omoccurrences AS o ON n.occid = o.occid '.
            'LEFT JOIN referenceobject AS r ON l.refid = r.refid '.
            'WHERE n.occid = '.$this->occid.' ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethnid;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getTaxaLinkageArr($tid){
        $returnArr = array();
        $sql = 'SELECT l.ethlinkid, l.ethnidlink, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid,
            g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName, n.tid, o.tidinterpreted
            FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdidlink = n.ethdid
            LEFT JOIN glottolog AS g ON n.langId = g.id
            LEFT JOIN taxa AS t ON n.tid = t.TID
            LEFT JOIN omoccurrences AS o ON n.occid = o.occid
            LEFT JOIN referenceobject AS r ON l.refid = r.refid
            WHERE n.tid = '.$tid.' OR o.tidinterpreted = '.$tid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethnidlink;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }

        $sql = 'SELECT l.ethlinkid, l.ethnid, l.linktype, l.refpages, l.discussion, r.title, g.id, l.refid, '.
            'g.iso639P3code, g.`name`, IFNULL(t.SciName,o.sciname) AS sciname, n.verbatimVernacularName, n.tid, o.tidinterpreted '.
            'FROM ethnolinkages AS l LEFT JOIN ethnodata AS n ON l.ethdid = n.ethdid '.
            'LEFT JOIN glottolog AS g ON n.langId = g.id '.
            'LEFT JOIN taxa AS t ON n.tid = t.TID '.
            'LEFT JOIN omoccurrences AS o ON n.occid = o.occid '.
            'LEFT JOIN referenceobject AS r ON l.refid = r.refid '.
            'WHERE n.tid = '.$tid.' OR o.tidinterpreted = '.$tid;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethlinkid;
                $langName = $row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name;
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['linkedNameId'] = $row->ethnid;
                $returnArr[$recId]['refid'] = $row->refid;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['refSource'] = $row->title;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['linktype'] = $row->linktype;
                $returnArr[$recId]['discussion'] = $row->discussion;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function createDataLinkage($pArr){
        if(array_key_exists('linkdataid',$pArr)){
            foreach($pArr['linkdataid'] as $id){
                $valueStr = 'VALUES (';
                $valueStr .= $pArr['dataid'].',';
                $valueStr .= (int)$id.',';
                $valueStr .= ($this->cleanInStr($pArr['linktype'])?'"'.$this->cleanInStr($pArr['linktype']).'",':'null,');
                $valueStr .= (array_key_exists("refid",$pArr)&&$pArr['refid']?$pArr['refid'].',':'null,');
                $valueStr .= ($this->cleanInStr($pArr['refpages'])?'"'.$this->cleanInStr($pArr['refpages']).'",':'null,');
                $valueStr .= ($this->cleanInStr($pArr['discussion'])?'"'.$this->cleanInStr($pArr['discussion']).'") ':'null) ');
                $sql = 'INSERT INTO ethnolinkages(ethdid,ethdidlink,linktype,refid,refpages,discussion) '.$valueStr;
                $this->conn->query($sql);
            }
        }
    }

    public function saveDataLinkageChanges($pArr){
        $result = false;
        $valueStr = 'SET ';
        $valueStr .= 'ethdidlink='.$pArr['linknameid'].',';
        $valueStr .= 'linktype='.($this->cleanInStr($pArr['linktype'])?'"'.$this->cleanInStr($pArr['linktype']).'"':'null').',';
        $valueStr .= 'refid='.(array_key_exists("refid",$pArr)&&$pArr['refid']?$pArr['refid']:'null').',';
        $valueStr .= 'refpages='.($this->cleanInStr($pArr['refpages'])?'"'.$this->cleanInStr($pArr['refpages']).'"':'null').',';
        $valueStr .= 'discussion='.($this->cleanInStr($pArr['discussion'])?'"'.$this->cleanInStr($pArr['discussion']).'"':'null');
        $sql = 'UPDATE ethnolinkages '.$valueStr.' '.
            'WHERE ethlinkid = '.$pArr['linkid'].' AND ethdid = '.$pArr['dataid'].' ';
        if($this->conn->query($sql)){
            $result = true;
        };

        $valueStr = 'SET ';
        $valueStr .= 'ethdid='.$pArr['linknameid'].',';
        $valueStr .= 'linktype='.($this->cleanInStr($pArr['linktype'])?'"'.$this->cleanInStr($pArr['linktype']).'"':'null').',';
        $valueStr .= 'refid='.(array_key_exists("refid",$pArr)&&$pArr['refid']?$pArr['refid']:'null').',';
        $valueStr .= 'refpages='.($this->cleanInStr($pArr['refpages'])?'"'.$this->cleanInStr($pArr['refpages']).'"':'null').',';
        $valueStr .= 'discussion='.($this->cleanInStr($pArr['discussion'])?'"'.$this->cleanInStr($pArr['discussion']).'"':'null');
        $sql = 'UPDATE ethnolinkages '.$valueStr.' '.
            'WHERE ethlinkid = '.$pArr['linkid'].' AND ethdidlink = '.$pArr['dataid'].' ';
        if($this->conn->query($sql)){
            $result = true;
        };

        return $result;
    }

    public function deleteDataLinkage($pArr){
        $sql = 'DELETE FROM ethnolinkages WHERE ethlinkid = '.$pArr['linkid'];
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function prepareLinkageSqlWhere($pArr){
        $this->linkageSqlWhere = '';
        $dataId = $pArr['dataid'];

        if(array_key_exists("genusMatch",$pArr) || array_key_exists("scinameMatch",$pArr)){
            $tid = $this->getDataRecordTid($dataId);
            if(array_key_exists("genusMatch",$pArr)){
                $genusTid = $this->getGenusTid($tid);
                $childTidArr = $this->getChildTidArr($genusTid);
                $tidStr = implode(",", $childTidArr);
            }
            else{
                $tidStr = $tid;
            }
            if($tidStr){
                $this->linkageSqlWhere .= 'AND ((ISNULL(ed.occid) AND ed.tid IN('.$tidStr.')) OR (ed.occid IS NOT NULL AND o.tidinterpreted IN('.$tidStr.'))) ';
            }
        }
        if(array_key_exists("semanticMatch",$pArr)){
            $tagArr = $this->getDataRecordSemanticsArr($dataId);
            $tagStr = implode(",", $tagArr);
            if($tagStr){
                $this->linkageSqlWhere .= 'AND (est.ethTagId IN('.$tagStr.')) ';
            }
        }
        if(array_key_exists("levenshteinMatch",$pArr)){
            $idArr = $this->getLevenshteinMatchArr($pArr["linkageVerbatimName"],$pArr["levenshteinValue"]);
            $idStr = implode(",", $idArr);
            if($idStr){
                $this->linkageSqlWhere .= 'AND (ed.ethdid IN('.$idStr.')) ';
            }
        }
        if(array_key_exists("vernacularDiffLang",$pArr)){
            $this->linkageSqlWhere .= 'AND (ed.langId <> "'.$pArr["linkageLangId"].'") ';
        }
        if(array_key_exists("vernacularStringMatch",$pArr)){
            $this->linkageSqlWhere .= 'AND (ed.verbatimVernacularName LIKE "%'.$pArr["vernacularStringMatchValue"].'%") ';
        }
        if(array_key_exists("vernacularRegexMatch",$pArr)){
            $idArr = $this->getVernacularRegexMatchArr($pArr["vernacularRegexMatchValue"]);
            $idStr = implode(",", $idArr);
            if($idStr){
                $this->linkageSqlWhere .= 'AND (ed.ethdid IN('.$idStr.')) ';
            }
        }
        if(array_key_exists("verbatimParseMatch",$pArr)){
            $this->linkageSqlWhere .= 'AND (ed.verbatimParse = "'.$pArr["verbatimParseValue"].'") ';
        }
        if(array_key_exists("verbatimParseRegexMatch",$pArr)){
            $idArr = $this->getParseRegexMatchArr($pArr["verbatimParseRegexMatchValue"]);
            $idStr = implode(",", $idArr);
            if($idStr){
                $this->linkageSqlWhere .= 'AND (ed.ethdid IN('.$idStr.')) ';
            }
        }
        if(array_key_exists("verbatimGlossMatch",$pArr)){
            $this->linkageSqlWhere .= 'AND (ed.verbatimGloss = "'.$pArr["verbatimGlossValue"].'") ';
        }
        if(array_key_exists("verbatimGlossRegexMatch",$pArr)){
            $idArr = $this->getGlossRegexMatchArr($pArr["verbatimGlossRegexMatchValue"]);
            $idStr = implode(",", $idArr);
            if($idStr){
                $this->linkageSqlWhere .= 'AND (ed.ethdid IN('.$idStr.')) ';
            }
        }

        $this->linkageSqlWhere = substr($this->linkageSqlWhere, 4);
    }

    public function getLinkageSearchReturn($pArr){
        $returnArr = array();
        $sql = 'SELECT DISTINCT ed.ethdid, c.CollectionName, ed.verbatimVernacularName, g.`name` AS languageName, ed.verbatimGloss, ed.verbatimParse '.
            'FROM ethnodata AS ed LEFT JOIN ethnodataevent AS edev ON ed.etheventid = edev.etheventid '.
            'LEFT JOIN omoccurrences AS o ON ed.occid = o.occid '.
            'LEFT JOIN omcollections AS c ON edev.collid = c.CollID '.
            'LEFT JOIN glottolog AS g ON ed.langId = g.id ';
        if(array_key_exists("semanticMatch",$pArr)){
            $sql .= 'LEFT JOIN ethnodatanamesemtaglink AS est ON ed.ethdid = est.ethdid ';
        }
        $sql .= 'WHERE '.$this->linkageSqlWhere;
        $sql .= 'AND ed.ethdid <> '.$pArr["dataid"].' ';
        $sql .= 'ORDER BY ed.verbatimVernacularName ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $returnArr[$r->ethdid]['CollectionName'] = $r->CollectionName;
            $returnArr[$r->ethdid]['verbatimVernacularName'] = $r->verbatimVernacularName;
            $returnArr[$r->ethdid]['languageName'] = $r->languageName;
            $returnArr[$r->ethdid]['verbatimGloss'] = $r->verbatimGloss;
            $returnArr[$r->ethdid]['verbatimParse'] = $r->verbatimParse;
        }

        return $returnArr;
    }

    public function getGlossRegexMatchArr($regexStr){
        $returnArr = array();
        $sql = 'SELECT ethdid, verbatimGloss FROM ethnodata ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $pregMatchStr = "/" . $regexStr . "/";
            if(preg_match($pregMatchStr, $r->verbatimGloss)){
                $returnArr[] = $r->ethdid;
            }
        }
        return $returnArr;
    }

    public function getParseRegexMatchArr($regexStr){
        $returnArr = array();
        $sql = 'SELECT ethdid, verbatimParse FROM ethnodata ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $pregMatchStr = "/" . $regexStr . "/";
            if(preg_match($pregMatchStr, $r->verbatimParse)){
                $returnArr[] = $r->ethdid;
            }
        }
        return $returnArr;
    }

    public function getVernacularRegexMatchArr($regexStr){
        $returnArr = array();
        $sql = 'SELECT ethdid, verbatimVernacularName FROM ethnodata ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $pregMatchStr = "/" . $regexStr . "/";
            if(preg_match($pregMatchStr, $r->verbatimVernacularName)){
                $returnArr[] = $r->ethdid;
            }
        }
        return $returnArr;
    }

    public function getLevenshteinMatchArr($matchStr,$minDistance){
        $returnArr = array();
        $dataArr = array();
        $sql = 'SELECT ethdid, verbatimVernacularName FROM ethnodata ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $dataArr[$r->ethdid] = $r->verbatimVernacularName;
        }
        foreach($dataArr as $id => $name){
            if($name){
                $lev = levenshtein($name,$matchStr);
                if($lev <= $minDistance){
                    $returnArr[] = $id;
                }
            }
        }
        return $returnArr;
    }

    public function getDataRecordTid($id){
        $tid = 0;
        $sql = 'SELECT DISTINCT IFNULL(d.tid,o.tidinterpreted) AS tid '.
            'FROM ethnodata AS d LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE d.ethdid = '.$id.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $tid = $r->tid;
        }
        return $tid;
    }

    public function getGenusTid($tid){
        $returnTid = 0;
        $sql = 'SELECT t.RankId, t2.TID '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.tid '.
            'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
            'WHERE t.TID = '.$tid.' AND t2.RankId = 180 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            if($r->RankId >= 180){
                $returnTid = $tid;
            }
            else{
                $returnTid = $r->TID;
            }
        }
        return $returnTid;
    }

    public function getChildTidArr($tid){
        $returnArr = array();
        $sql = 'SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = '.$tid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $returnArr[] = $r->tid;
        }
        $returnArr[] = $tid;
        return $returnArr;
    }

    public function getVernacularNameList($name){
        $retArr = Array();
        $sql = 'SELECT ethdid, verbatimVernacularName '.
            'FROM ethnodata '.
            'WHERE verbatimVernacularName LIKE "'.$name.'%" '.
            'ORDER BY verbatimVernacularName ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->verbatimVernacularName]['id'] = $r->ethdid;
            $retArr[$r->verbatimVernacularName]['value'] = $r->verbatimVernacularName;
        }
        return $retArr;
    }

    public function getLangNameSearchDropDownList(){
        $retArr = Array();
        $sql = 'SELECT DISTINCT g.id, g.iso639P3code, g.`name` '.
            'FROM glottolog AS g LEFT JOIN ethnocolllanglink AS cl ON g.id = cl.langID '.
            'WHERE cl.langID IS NOT NULL '.
            'ORDER BY g.`name` ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $display = $r->id.' | '.($r->iso639P3code?$r->iso639P3code:'[No ISO code]').' | '.$r->name;
            $retArr[$r->name]['id'] = $r->id;
            $retArr[$r->name]['name'] = $display;
        }
        return $retArr;
    }

    public function createDataPersonnelRecord($pId,$nId){
        $valueStr = 'VALUES (';
        $valueStr .= $nId.',';
        $valueStr .= $pId.') ';
        $sql = 'INSERT INTO ethnodatapersonnellink(ethdid,ethPerID) '.$valueStr;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function clearDataPersonnelRecords(){
        $sql = 'DELETE FROM ethnodatapersonnellink WHERE ethdid = '.$this->dataid;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function createNameSemanticRecord($pId,$nId){
        $valueStr = 'VALUES (';
        $valueStr .= $nId.',';
        $valueStr .= $pId.') ';
        $sql = 'INSERT INTO ethnodatanamesemtaglink(ethdid,ethTagId) '.$valueStr;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function clearNameSemanticRecords(){
        $sql = 'DELETE FROM ethnodatanamesemtaglink WHERE ethdid = '.$this->dataid;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function createUsePartRecord($pId,$uId){
        $valueStr = 'VALUES (';
        $valueStr .= $uId.',';
        $valueStr .= $pId.') ';
        $sql = 'INSERT INTO ethnodatausepartslink(ethdid,ethpuid) '.$valueStr;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function clearUsePartRecords(){
        $sql = 'DELETE FROM ethnodatausepartslink WHERE ethdid = '.$this->dataid;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function createUseTypeRecord($tId,$uId){
        $valueStr = 'VALUES (';
        $valueStr .= $uId.',';
        $valueStr .= $tId.') ';
        $sql = 'INSERT INTO ethnodatausetaglink(ethdid,ethuoid) '.$valueStr;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function clearUseTypeRecords(){
        $sql = 'DELETE FROM ethnodatausetaglink WHERE ethdid = '.$this->dataid;
        if($this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function getOccTaxaArr($occId){
        $returnArr = array();
        $sql = 'SELECT sciname, tidinterpreted FROM omoccurrences WHERE occid = '.$occId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr['sciname'] = $row->sciname;
                $returnArr['tid'] = $row->tidinterpreted;
            }
            $rs->close();
        }
        return $returnArr;
    }

    protected function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }

    public function filterTaxaSppArray($sppArr){
        foreach($sppArr as $sciNameKey => $subArr){
            $tId = $subArr["tid"];
            $sql = 'SELECT occid FROM omoccurrences WHERE tidinterpreted = '.$tId.' LIMIT 1 ';
            $rs = $this->conn->query($sql);
            if($rs->num_rows < 1){
                $sql2 = 'SELECT tdbid FROM taxadescrblock WHERE tid = '.$tId.' LIMIT 1 ';
                $rs2 = $this->conn->query($sql2);
                if($rs2->num_rows < 1){
                    unset($sppArr[$sciNameKey]);
                }
            }
        }
        return $sppArr;
    }
}
?>
