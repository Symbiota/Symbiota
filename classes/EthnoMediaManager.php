<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class EthnoMediaManager {

    private $conn;
    private $collid;
    private $mediaid;
    private $uploadFileRootPath = "";
    private $targetPath;
    private $fileName;
    private $status;
    private $errorStr;

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

    public function setMediaid($id){
        if(is_numeric($id)){
            $this->mediaid = $id;
            return true;
        }
        return false;
    }

    public function getMediaid(){
        return $this->mediaid;
    }

    public function getErrorStr(){
        return $this->errorStr;
    }

    private function setRootPath(){
        $this->uploadFileRootPath = $GLOBALS['SERVER_ROOT'];
        if(substr($this->uploadFileRootPath,-1) !== '/') $this->uploadFileRootPath .= '/';
        $this->uploadFileRootPath .= 'content/media/'.$this->collid.'/';
    }

    private function setTargetPath(){
        $this->targetPath = $GLOBALS['SERVER_ROOT'];
        if(substr($this->targetPath,-1) !== '/') $this->targetPath .= '/';
        $this->targetPath .= 'content/media/';
        if(!file_exists($this->targetPath.$this->collid)){
            if (!mkdir($concurrentDirectory = $this->targetPath . $this->collid, 0775) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $this->targetPath .= $this->collid.'/';
    }

    public function getEAFArr(){
        $retArr = Array();
        $sql = 'SELECT ethmediaid, description '.
            'FROM ethnomedia ';
        if($this->collid) $sql .= 'WHERE collid = '.$this->collid.' ';
        $sql .= 'ORDER BY description ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->ethmediaid]['desc'] = $row->description;
        }
        $rs->free();
        return $retArr;
    }

    public function getOccEAFArr($occId){
        $retArr = Array();
        $sql = 'SELECT m.ethmediaid, m.description '.
            'FROM ethnomedia AS m LEFT JOIN ethnomedocclink AS l ON m.ethmediaid = l.ethmediaid '.
            'WHERE m.collid = '.$this->collid.' AND l.occid = '.$occId.' '.
            'ORDER BY m.description ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->ethmediaid]['desc'] = $row->description;
        }
        $rs->free();
        return $retArr;
    }

    public function getTaxaEAFArr($tid){
        $retArr = Array();
        if($tid){
            $sql = 'SELECT c.CollectionName, m.ethmediaid, m.description, tl.tid, o.tidinterpreted '.
                'FROM ethnomedia AS m LEFT JOIN ethnomedocclink AS l ON m.ethmediaid = l.ethmediaid '.
                'LEFT JOIN ethnomedtaxalink AS tl ON m.ethmediaid = tl.ethmediaid '.
                'LEFT JOIN omoccurrences AS o ON l.occid = o.occid '.
                'LEFT JOIN omcollections AS c ON m.collid = c.CollID '.
                'WHERE tl.tid = '.$tid.' OR o.tidinterpreted = '.$tid.' '.
                'ORDER BY c.CollectionName, m.description ';
            //echo '<div>'.$sql.'</div>';
            $rs = $this->conn->query($sql);
            while($row = $rs->fetch_object()){
                $retArr[$row->CollectionName][$row->ethmediaid]['desc'] = $row->description;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getCollectionList(){
        $retArr = array();
        $sql = 'SELECT DISTINCT m.collid, c.CollectionName '.
            'FROM ethnomedia AS m LEFT JOIN omcollections AS c ON m.collid = c.CollID ' .
            'ORDER BY c.CollectionName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->collid] = $r->CollectionName;
            }
        }
        return $retArr;
    }

    public function createTierArr($xml){
        $time_slot_array = array();
        $tempArr = array();
        foreach ($xml->TIER as $a_tier){
            //$parentTierID = $a_tier['PARENT_REF']?(string)$a_tier['PARENT_REF']:(string)$a_tier['TIER_ID'];
            $tier_id = trim(stripslashes((string)$a_tier['TIER_ID']));
            if($tier_id){
                $include = false;
                foreach ($a_tier->ANNOTATION as $a_nnotation){
                    if($a_nnotation){
                        $include = true;
                    }
                }
                $participant = trim(stripslashes((string)$a_tier['PARTICIPANT']));
                if(($participant || $tier_id) && $include) {
                    $parent_ref = trim(stripslashes((string)$a_tier['PARENT_REF']));
                    if(!$participant && $parent_ref) {
                        $participant = $parent_ref;
                    }
                    $spkr = '';
                    $speaker = ($participant?$participant:$tier_id);
                    $speaker = str_replace("  "," ",$speaker);
                    $speaker_parts = explode(" ", $speaker);
                    foreach($speaker_parts as $sp_prt){
                        $spkr .= trim(substr($sp_prt, 0, 1));
                    }
                    if(strlen($spkr) > 3) $spkr = substr($spkr, 0, 3);
                    if(!array_key_exists($speaker,$tempArr)) $tempArr[$speaker]['participant'] = $speaker;
                    $tempArr[$speaker]['tiers'][$tier_id]['name'] = $tier_id;
                    $tempArr[$speaker]['tiers'][$tier_id]['code'] = $spkr;
                    $tempArr[$speaker]['tiers'][$tier_id]['color'] = $this->getRandomColor();
                }
            }
        }
        return $tempArr;
    }

    public function getEAFInfoArr(){
        $retArr = Array();
        $sql = 'SELECT collid, url, description, eaffile, displaySettings '.
            'FROM ethnomedia '.
            'WHERE ethmediaid = '.$this->mediaid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr['url'] = $r->url;
            $retArr['description'] = $r->description;
            $retArr['eaffile'] = $r->eaffile;
            $retArr['displaySettings'] = $r->displaySettings;
        }
        return $retArr;
    }

    public function getTaxaArr(){
        $retArr = array();
        $sql = 'SELECT e.ethMedTaxaLinkID, t.SciName '.
            'FROM ethnomedtaxalink AS e LEFT JOIN taxa AS t ON e.tid = t.TID '.
            'WHERE e.ethmediaid = '.$this->mediaid.' ';
        //echo $sql; exit;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->ethMedTaxaLinkID] = $r->SciName;
            }
            $rs->free();
        }
        asort($retArr);

        return $retArr;
    }

    public function getOccArr(){
        $retArr = array();
        $sql = 'SELECT e.ethMedOccLinkID, o.occid, CONCAT_WS(" ",o.catalogNumber,o.recordedBy,o.recordNumber) AS occStr '.
            'FROM ethnomedocclink AS e LEFT JOIN omoccurrences AS o ON e.occid = o.occid '.
            'WHERE e.ethmediaid = '.$this->mediaid.' ';
        //echo $sql; exit;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $occText = $r->occid.': '.$r->occStr;
                $retArr[$r->ethMedOccLinkID] = $occText;
            }
            $rs->free();
        }

        return $retArr;
    }

    public function saveEAFInfo($pArr){
        $valueStr = 'SET ';
        if($this->cleanInStr($pArr['mp3url'])) $valueStr .= 'url="'.$this->cleanInStr($pArr['mp3url']).'",';
        if($this->cleanInStr($pArr['description'])) $valueStr .= 'description="'.$this->cleanInStr($pArr['description']).'",';
        $valueStr .= "displaySettings='".$this->cleanInStr($pArr['displaySettings'])."'";
        $sql = 'UPDATE ethnomedia '.$valueStr.' '.
            'WHERE ethmediaid = '.$this->mediaid.' ';
        if($this->conn->query($sql)) return 'EAF Edits Saved';
        else return false;
    }

    public function addTaxonLink($tid){
        if(is_numeric($tid)){
            $sql = 'INSERT INTO ethnomedtaxalink(ethmediaid,tid) '.
                'VALUES('.$this->mediaid.','.$tid.') ';
            //echo $sql; exit;
            if(!$this->conn->query($sql)){
                $this->errorStr = 'ERROR inserting ethnomedtaxalink: '.$this->conn->error;
                return false;
            }
            return true;
        }
        return false;
    }

    public function deleteTaxonLink($linkid){
        $sql = 'DELETE FROM ethnomedtaxalink WHERE ethMedTaxaLinkID = '.$linkid.' ';
        if(!$this->conn->query($sql)){
            $this->errorStr = 'ERROR deleting ethnomedtaxalink record: '.$this->conn;
            return false;
        }
        return true;
    }

    public function deleteOccLink($linkid){
        $sql = 'DELETE FROM ethnomedocclink WHERE ethMedOccLinkID = '.$linkid.' ';
        if(!$this->conn->query($sql)){
            $this->errorStr = 'ERROR deleting ethnomedocclink record: '.$this->conn;
            return false;
        }
        return true;
    }

    public function addFile(){
        set_time_limit(120);
        ini_set("max_input_time",120);
        $this->setRootPath();
        $this->setTargetPath();
        if(!$this->loadFile()) return $this->status;
        $this->databaseFile();
        return $this->status;
    }

    private function loadFile(){
        $this->fileName = basename($_FILES['eaffile']['name']);
        if(substr($this->fileName,-4) === '.eaf'){
            $this->fileName = $this->cleanFileName($this->fileName,$this->targetPath);
            if(move_uploaded_file($_FILES['eaffile']['tmp_name'], $this->targetPath.$this->fileName)){
                return true;
            }
        }
        $this->status = 'ERROR: Invalid eaf file uploaded.';
        return false;
    }

    private function databaseFile(){
        $filePath = '/content/media/' . $this->collid . '/';
        $valueStr = 'VALUES ('.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($_POST['mp3url']).'",';
        $valueStr .= '"'.$this->cleanInStr($_POST['description']).'",';
        $valueStr .= '"'.$filePath.$this->fileName.'") ';
        $sql = 'INSERT INTO ethnomedia(collid,url,description,eaffile) '.$valueStr;
        if($this->conn->query($sql)){
            $this->mediaid = $this->conn->insert_id;
            if(array_key_exists("occid",$_POST)) $this->addMediaOccLink($this->mediaid,$_POST['occid']);
            return true;
        }
        else return false;
    }

    public function addMediaOccLink($mediaid,$occid){
        $sql = 'INSERT INTO ethnomedocclink(ethmediaid,occid) '.
            'VALUES ('.$mediaid.','.$occid.') ';
        if($this->conn->query($sql)){
            return true;
        }
        else return false;
    }

    public function deleteEAF($mediaId){
        $fileName = "";
        $status = "EAF file deleted successfully";
        $sql = 'SELECT eaffile FROM ethnomedia WHERE ethmediaid = '.$mediaId.' ';
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $fileName = $row->eaffile;
        }
        $result->close();
        $this->deleteEAFTaxaLinks($mediaId);
        $this->deleteEAFOccLinks($mediaId);
        $sql = "DELETE FROM ethnomedia WHERE ethmediaid = ".$mediaId.' ';
        //echo $sql;
        if($this->conn->query($sql)){
            $this->setRootPath();
            $fileDelPath = $this->uploadFileRootPath.$fileName;
            if(file_exists($fileDelPath)){
                if(!unlink($fileDelPath)){
                    $status = "Deleted eaf record from database but FAILED to delete file from server. The file will have to be deleted manually.";
                }
            }
        }
        else{
            $status = "deleteFile: ".$this->conn->error."\nSQL: ".$sql;
        }
        return $status;
    }

    public function deleteEAFTaxaLinks($mediaId){
        $sql = "DELETE FROM ethnomedtaxalink WHERE ethmediaid = ".$mediaId.' ';
        if($rs = $this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function deleteEAFOccLinks($mediaId){
        $sql = "DELETE FROM ethnomedocclink WHERE ethmediaid = ".$mediaId.' ';
        if($rs = $this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function remapEAFOccLinkRecord($mediaid,$currentOccid,$targetOccid){
        $sql = 'UPDATE ethnomedocclink '.
            'SET occid = '.$targetOccid.' '.
            'WHERE ethmediaid = '.$mediaid.' AND occid = '.$currentOccid.' ';
        if($rs = $this->conn->query($sql)){
            return true;
        }
        return false;
    }

    public function getCollectionName(){
        $collName = '';
        $sql = 'SELECT collid, collectionname '.
            'FROM omcollections WHERE (collid = '.$this->collid.')';
        if($rs = $this->conn->query($sql)){
            if($row = $rs->fetch_object()){
                $collName = $row->collectionname;
            }
            $rs->close();
        }
        return $collName;
    }

    private function getRandomColor(){
        $first = str_pad(dechex(mt_rand(128,255)),2,'0',STR_PAD_LEFT);
        $second = str_pad(dechex(mt_rand(128,255)),2,'0',STR_PAD_LEFT);
        $third = str_pad(dechex(mt_rand(128,255)),2,'0',STR_PAD_LEFT);
        $color_code = $first.$second.$third;

        return $color_code;
    }

    private function cleanFileName($fName,$subPath){
        if($fName){
            $pos = strrpos($fName,'.eaf');
            $fName = substr($fName,0,$pos);
            $fName = str_replace(" ","_",$fName);
            $fName = str_replace(array(chr(231),chr(232),chr(233),chr(234),chr(260)),"a",$fName);
            $fName = str_replace(array(chr(230),chr(236),chr(237),chr(238)),"e",$fName);
            $fName = str_replace(array(chr(239),chr(240),chr(241),chr(261)),"i",$fName);
            $fName = str_replace(array(chr(247),chr(248),chr(249),chr(262)),"o",$fName);
            $fName = str_replace(array(chr(250),chr(251),chr(263)),"u",$fName);
            $fName = str_replace(array(chr(264),chr(265)),"n",$fName);
            $fName = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $fName);
            if(strlen($fName) > 30) {
                $fName = substr($fName,0,30);
            }
            $fName .= '.eaf';
            $cnt = 1;
            while(file_exists($subPath.$fName)){
                $fName = str_ireplace(".eaf","_".$cnt.".eaf",$fName);
                $cnt++;
            }
        }
        return $fName;
    }

    protected function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }
}
?>
